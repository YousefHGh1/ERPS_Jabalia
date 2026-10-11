<?php

namespace App\Jobs;

use App\Models\Request as RequestModel;
use App\Models\RequestWorkflow;
use App\Models\Employee;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRequestCreatedNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $requestId
    ) {}

    public function handle(): void
    {
        $request = RequestModel::with(['requestType', 'creator'])
            ->find($this->requestId);

        if (!$request) {
            Log::warning("SendRequestCreatedNotificationsJob: request {$this->requestId} not found");
            return;
        }

        $notifiedUserIds = [];

        $workflows = RequestWorkflow::where('request_type_id', $request->request_type_id)
            ->active()
            ->ordered()
            ->get();

        $requestTypeName = $request->requestType->name ?? 'غير محدد';
        $message = "تم إرسال طلب جديد رقم: {$request->id} ({$requestTypeName}) لقسمك - يرجى مراجعته في بريد الطلبات";
        $link = route('requests.mail.waiting');

        foreach ($workflows as $workflow) {
            if (!$workflow->section_id) {
                continue;
            }
            $employees = Employee::where('section_id', $workflow->section_id)
                ->where('status', 'active')
                ->with('user')
                ->get();

            foreach ($employees as $employee) {
                if ($employee->user && !in_array($employee->user->id, $notifiedUserIds)) {
                    try {
                        Notification::create([
                            'user_id' => $employee->user->id,
                            'type' => 'request',
                            'title' => 'طلب جديد',
                            'message' => $message,
                            'link' => $link,
                            'icon' => 'fa-envelope',
                            'priority' => 'normal',
                            'is_read' => false,
                        ]);
                        $notifiedUserIds[] = $employee->user->id;
                    } catch (\Exception $e) {
                        Log::error("SendRequestCreatedNotificationsJob: failed for employee {$employee->id}: " . $e->getMessage());
                    }
                }
            }
        }

        try {
            $admins = User::role('admin')->get();
            $adminMessage = "طلب جديد رقم: {$request->id} ({$requestTypeName}) - مراجعته في بريد الطلبات";
            foreach ($admins as $user) {
                if (!in_array($user->id, $notifiedUserIds)) {
                    try {
                        Notification::create([
                            'user_id' => $user->id,
                            'type' => 'request',
                            'title' => 'طلب جديد',
                            'message' => $adminMessage,
                            'link' => $link,
                            'icon' => 'fa-envelope',
                            'priority' => 'normal',
                            'is_read' => false,
                        ]);
                        $notifiedUserIds[] = $user->id;
                    } catch (\Exception $e) {
                        Log::error("SendRequestCreatedNotificationsJob: failed for admin {$user->id}: " . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning("SendRequestCreatedNotificationsJob: admin notify: " . $e->getMessage());
        }

        Log::info("SendRequestCreatedNotificationsJob: request {$request->id}, notified " . count($notifiedUserIds) . " users");
    }
}
