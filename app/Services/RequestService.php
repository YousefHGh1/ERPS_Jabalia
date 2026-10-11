<?php

namespace App\Services;

use App\Events\RequestCreated;
use App\Exceptions\RequestWorkflowException;
use App\Jobs\SendRequestCreatedNotificationsJob;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Request as RequestModel;
use App\Models\RequestAttachment;
use App\Models\RequestPayment;
use App\Models\RequestResponse;
use App\Models\RequestStatus;
use App\Models\RequestWorkflow;
use App\Models\ResponseType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RequestService
{
    protected const RESPONSE_TYPE_APPROVED = 1;
    protected const RESPONSE_TYPE_WAITING = 4;

    public function __construct(
        protected CentralTreasuryPaymentService $centralTreasuryPaymentService
    ) {
    }

    public function resolveInitialStatus(): ?RequestStatus
    {
        return $this->findStatusByNames(['قيد الانتظار', 'pending']);
    }

    public function calculateFinancials(RequestModel $request): RequestModel
    {
        $request->refresh();
        $totalDue = (float) ($request->fee_amount ?? 0);
        $totalPaid = (float) $request->payments()->where('status', 'active')->sum('amount');
        $request->paid_amount = $totalPaid;
        $request->remaining_amount = max(0, $totalDue - $totalPaid);
        $request->save();

        return $request;
    }

    public function addPayment(RequestModel $request, array $data): RequestPayment
    {
        return DB::transaction(function () use ($request, $data) {
            $this->calculateFinancials($request);
            $totalDue = (float) ($request->fee_amount ?? 0);
            if ($totalDue <= 0) {
                throw new \InvalidArgumentException('لا يوجد رسم مستحق على هذا الطلب. عيّن حقل الرسم المستحق أولاً ثم احفظ الطلب.');
            }

            $balanceAfter = (float) $request->remaining_amount - (float) $data['amount'];
            if ($balanceAfter < 0) {
                throw new \InvalidArgumentException('المبلغ المدفوع يتجاوز المبلغ المتبقي.');
            }

            $payment = $this->centralTreasuryPaymentService->createRequestPayment($request, [
                'amount' => $data['amount'],
                'balance_after' => $balanceAfter,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'receipt_number' => $data['receipt_number'] ?? null,
                'payment_date' => $data['payment_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            $this->calculateFinancials($request);

            return $payment;
        });
    }

    public function cancelPayment(RequestPayment $payment, string $reason): RequestPayment
    {
        return DB::transaction(function () use ($payment, $reason) {
            $payment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            $citizenRequest = $payment->citizenRequest;
            if ($citizenRequest) {
                $this->calculateFinancials($citizenRequest);
            }

            return $payment->fresh();
        });
    }

    public function updateStatus(RequestModel $request, RequestStatus $newStatus, ?string $reason = null): RequestModel
    {
        return DB::transaction(function () use ($request, $newStatus, $reason) {
            $this->ensureStatusTransitionAllowed($request, $newStatus);

            $request->update([
                'status_id' => $newStatus->id,
                'status_reason' => filled($reason) ? $reason : null,
            ]);

            $request->loadMissing(['requestType', 'mokalaf']);
            $request->setRelation('status', $newStatus);

            if ($this->isCompletedStatus($newStatus->name)) {
                $this->notifyCompletedRequestToWorkflowSections($request);
            }

            $this->bumpRequestMailCacheVersion();

            return $request->fresh(['status', 'requestType', 'mokalaf']);
        });
    }

    public function createRequest(array $data, array $attachments = []): RequestModel
    {
        return DB::transaction(function () use ($data, $attachments) {
            $request = RequestModel::create($data);

            if (!empty($attachments)) {
                $this->handleAttachments($request, $attachments);
            }

            try {
                $this->createWorkflowResponses($request);
            } catch (\Throwable $e) {
                // Log the workflow error but don't fail the request creation
                Log::warning('Workflow creation failed for request ' . $request->id . ': ' . $e->getMessage());
            }

            $this->bumpRequestMailCacheVersion();

            SendRequestCreatedNotificationsJob::dispatch($request->id);
            event(new RequestCreated($request));

            $request = $request->fresh(['requestType', 'status', 'mokalaf', 'attachments']);
            $this->calculateFinancials($request);

            return $request;
        });
    }

    public function updateRequest(RequestModel $request, array $data, array $newAttachments = []): RequestModel
    {
        return DB::transaction(function () use ($request, $data, $newAttachments) {
            $request->update($data);

            if (!empty($newAttachments)) {
                $this->handleAttachments($request, $newAttachments);
            }

            $request = $request->fresh(['requestType', 'status', 'mokalaf', 'attachments']);
            $this->calculateFinancials($request);

            return $request;
        });
    }

    public function handleAttachments(RequestModel $request, array $files): void
    {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            try {
                $allowedMimes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg',
                    'image/png',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ];

                if (!in_array($file->getMimeType(), $allowedMimes, true)) {
                    Log::warning('Invalid file type uploaded', [
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'user_id' => Auth::id(),
                    ]);
                    continue;
                }

                $path = $file->store('request_attachments', 'public');

                RequestAttachment::create([
                    'request_id' => $request->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'created_by' => Auth::id(),
                ]);
            } catch (\Exception $e) {
                Log::error('Error handling attachment', [
                    'request_id' => $request->id,
                    'file_name' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function deleteRequest(RequestModel $request): bool
    {
        return DB::transaction(function () use ($request) {
            foreach ($request->attachments as $attachment) {
                if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }

            return $request->delete();
        });
    }

    public function createWorkflowResponses(RequestModel $request): void
    {
        $workflows = RequestWorkflow::where('request_type_id', $request->request_type_id)
            ->active()
            ->ordered()
            ->get();

        if ($workflows->isEmpty()) {
            throw new RequestWorkflowException('لم يتم إعداد مسار (سير عمل) لهذا النوع من الطلبات');
        }

        $waitingResponseType = ResponseType::find(self::RESPONSE_TYPE_WAITING);
        if (!$waitingResponseType) {
            throw new \Exception('نوع الرد "قيد الانتظار" غير موجود');
        }

        $responsesCreated = 0;

        foreach ($workflows as $workflow) {
            if (!$workflow->section_id && !$workflow->department_id) {
                Log::warning("Workflow {$workflow->id} ({$workflow->name}) has no unit - skipping");
                continue;
            }

            RequestResponse::create([
                'response_type' => self::RESPONSE_TYPE_WAITING,
                'request_id' => $request->id,
                'workflow_id' => $workflow->id,
                'section_id' => $workflow->section_id,
                'department_id' => $workflow->department_id,
                'notes' => '-',
                'created_by' => Auth::id() ?? $request->created_by,
                'ip_address' => request()->ip(),
                'host' => gethostname(),
            ]);
            $responsesCreated++;
        }

        if ($responsesCreated === 0) {
            throw new RequestWorkflowException(
                'لم يتم ربط مسارات نوع الطلب بأقسام أو دوائر. يرجى الذهاب إلى: أنواع الطلبات ثم إدارة المسارات وتحديد القسم أو الدائرة لكل مسار.'
            );
        }
    }

    protected function ensureStatusTransitionAllowed(RequestModel $request, RequestStatus $newStatus): void
    {
        if ($this->isWaitingStatus($newStatus->name)) {
            return;
        }

        $pendingSections = $this->pendingApprovalSections($request);
        if ($pendingSections === []) {
            return;
        }

        throw new RequestWorkflowException(
            'لا يمكن تغيير حالة الطلب قبل موافقة جميع الأقسام المطلوبة في المسار. الأقسام التي لم توافق بعد: ' . implode('، ', $pendingSections)
        );
    }

    /**
     * @return array<int, string>
     */
    protected function pendingApprovalSections(RequestModel $request): array
    {
        $workflows = RequestWorkflow::where('request_type_id', $request->request_type_id)
            ->with(['section', 'department', 'previousWorkflow'])
            ->get();

        if ($workflows->whereNotNull('previous_workflow_id')->isEmpty()) {
            return [];
        }

        $pendingSections = [];

        foreach ($workflows as $workflow) {
            $hasApprovedResponse = RequestResponse::where('request_id', $request->id)
                ->where('workflow_id', $workflow->id)
                ->where('response_type', self::RESPONSE_TYPE_APPROVED)
                ->exists();

            if ($hasApprovedResponse) {
                continue;
            }

            $pendingSections[] = optional($workflow->section)->name
                ?? optional($workflow->department)->name
                ?? $workflow->name
                ?? ('مسار #' . $workflow->id);
        }

        return array_values(array_unique(array_filter($pendingSections)));
    }

    protected function notifyCompletedRequestToWorkflowSections(RequestModel $request): void
    {
        Log::info('notifyCompletedRequestToWorkflowSections called for request ID: ' . $request->id);

        $requestType = $request->requestType;
        $mokalaf = $request->mokalaf;
        if (!$requestType || !$mokalaf) {
            Log::info('Skipping completed request notifications due to missing request type or mokalaf');
            return;
        }

        $workflows = RequestWorkflow::where('request_type_id', $request->request_type_id)
            ->with(['section', 'department'])
            ->get();

        $sectionIds = $workflows->pluck('section_id')->filter()->unique()->values();
        $departmentIds = $workflows->pluck('department_id')->filter()->unique()->values();

        if ($sectionIds->isEmpty() && $departmentIds->isEmpty()) {
            Log::info('No workflow units found for completed request ID: ' . $request->id);
            return;
        }

        $employees = Employee::active()
            ->where(function ($q) use ($sectionIds, $departmentIds) {
                if ($sectionIds->isNotEmpty()) {
                    $q->whereIn('section_id', $sectionIds);
                }
                if ($departmentIds->isNotEmpty()) {
                    $q->orWhereIn('department_id', $departmentIds);
                }
            })
            ->with('user')
            ->get();

        if ($employees->isEmpty()) {
            Log::info('No active employees found in workflow sections for request ID: ' . $request->id);
            return;
        }

        $notificationSent = 0;
        foreach ($employees as $employee) {
            if (!$employee->user) {
                continue;
            }

            $this->createUrgentNotification($employee->user, $request, $requestType->name, $mokalaf->name);
            $notificationSent++;
        }

        Log::info('Sent ' . $notificationSent . ' completed-request notifications for request ID: ' . $request->id);
    }

    protected function createUrgentNotification(User $user, RequestModel $request, string $requestTypeName, string $mokalafName): void
    {
        try {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'request',
                'title' => '!! عاجل - مطلوب إنشاء ملف !!',
                'message' => sprintf(
                    'الطلب رقم #%d تم إنجازه للمواطن: %s. يرجى إنشاء ملف %s لهذا المواطن فوراً.',
                    $request->id,
                    $mokalafName,
                    $requestTypeName
                ),
                'link' => '/requests/' . $request->id,
                'is_read' => false,
                'priority' => 'urgent',
                'icon' => 'fa-exclamation-triangle',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error creating urgent notification', [
                'user_id' => $user->id,
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function bumpRequestMailCacheVersion(): void
    {
        Cache::put('request_mail_version', (int) Cache::get('request_mail_version', 0) + 1);
    }

    protected function isWaitingStatus(?string $statusName): bool
    {
        $normalized = $this->normalizeStatusName($statusName);

        return in_array($normalized, ['قيد الانتظار', 'pending'], true);
    }

    protected function isCompletedStatus(?string $statusName): bool
    {
        $normalized = $this->normalizeStatusName($statusName);

        return in_array($normalized, ['مكتمل', 'completed'], true);
    }

    protected function normalizeStatusName(?string $statusName): string
    {
        return mb_strtolower(trim((string) $statusName));
    }

    protected function findStatusByNames(array $names): ?RequestStatus
    {
        $statuses = RequestStatus::query()->get();

        foreach ($names as $name) {
            $matched = $statuses->first(function (RequestStatus $status) use ($name) {
                return $this->normalizeStatusName($status->name) === $this->normalizeStatusName($name);
            });

            if ($matched) {
                return $matched;
            }
        }

        return null;
    }
}
