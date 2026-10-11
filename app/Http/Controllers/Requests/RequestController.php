<?php
namespace App\Http\Controllers\Requests;

use App\Exceptions\RequestWorkflowException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestRequest;
use App\Http\Requests\UpdateRequestRequest;
use App\Http\Resources\RequestResource;
use App\Models\CraftLicense;
use App\Models\Department;
use App\Models\Mokalaf;
use App\Models\Request as RequestModel;
use App\Models\RequestAttachment;
use App\Models\RequestPayment;
use App\Models\RequestStatus;
use App\Models\RequestType;
use App\Models\Section;
use App\Repositories\RequestRepository;
use App\Services\RequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

class RequestController extends Controller
{
    protected $requestService;
    protected $requestRepository;

    public function __construct(RequestService $requestService, RequestRepository $requestRepository)
    {
        $this->requestService    = $requestService;
        $this->requestRepository = $requestRepository;

        $this->authorizeResource(RequestModel::class, 'request');

        $this->middleware('throttle:10,1')->only(['store', 'update', 'updateStatus', 'destroy', 'addPayment', 'cancelRequestPayment']);
    }

    public function index(Request $request)
    {
        $search        = trim((string) $request->input('search', ''));
        $requestTypeId = $request->input('request_type_id');
        $statusId      = $request->input('status_id');
        $mokalafId     = $request->input('mokalaf_id');
        $isUrgent      = $request->input('is_urgent');
        $fromDate      = $request->input('from_date');
        $toDate        = $request->input('to_date');
        $monitor       = $request->input('monitor');

        $requests = RequestModel::with(['requestType', 'status', 'mokalaf', 'creator', 'responses.section'])
            ->when($search !== '', function ($query) use ($search) {
                $query->search($search);
            })
            ->when($requestTypeId, function ($query) use ($requestTypeId) {
                $query->where('request_type_id', $requestTypeId);
            })
            ->when($statusId, function ($query) use ($statusId) {
                $query->where('status_id', $statusId);
            })
            ->when($mokalafId, function ($query) use ($mokalafId) {
                $query->where('mokalaf_id', $mokalafId);
            })
            ->when($isUrgent !== null, function ($query) use ($isUrgent) {
                $query->where('is_urgent', $isUrgent);
            })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('deadline', [$fromDate, $toDate]);
            })
            ->when($monitor === 'overdue', function ($query) {
                $query->overdue();
            })
            ->when($monitor === 'due_soon', function ($query) {
                $query->deadlineApproaching();
            })
            ->when($monitor === 'pending', function ($query) {
                $query->pending();
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $requestStats = [
            'total'    => RequestModel::count(),
            'urgent'   => RequestModel::urgent()->count(),
            'overdue'  => RequestModel::overdue()->count(),
            'due_soon' => RequestModel::deadlineApproaching()->count(),
            'pending'  => RequestModel::pending()->count(),
        ];

        $requestTypes = Cache::remember('request_types_all', 3600, function () {
            return RequestType::select('id', 'name')->orderBy('name')->get();
        });

        $requestStatuses = Cache::remember('request_statuses_all', 3600, function () {
            return RequestStatus::select('id', 'name')->orderBy('name')->get();
        });

        $mokalafs = Cache::remember('mokalafs_all', 1800, function () {
            return Mokalaf::select('id', 'name')->orderBy('name')->get();
        });

        return view('requests.index', compact('requests', 'requestTypes', 'requestStatuses', 'mokalafs', 'requestStats'));
    }

    public function create()
    {
        $requestTypes = RequestType::select('id', 'name')->orderBy('name')->get();
        $sections     = Section::select('id', 'name')->orderBy('name')->get();
        $departments  = Department::select('id', 'name')->orderBy('name')->get();

        $mokalafs = Cache::remember('mokalafs_all', 1800, function () {
            return Mokalaf::select('id', 'name')->orderBy('name')->get();
        });

        return view('requests.create', compact('requestTypes', 'mokalafs', 'sections', 'departments'));
    }

    public function store(StoreRequestRequest $request)
    {
        try {
            $statusId = $this->requestService->resolveInitialStatus()?->id ?? RequestStatus::query()->value('id') ?? null;

            if (! $statusId) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'لا توجد حالات طلب متاحة. يرجى تشغيل: php artisan db:seed --class=RequestStatusSeeder');
            }

            $requestData               = $request->validated();
            $requestData['status_id']  = $statusId;
            $requestData['created_by'] = Auth::id();

            $newRequest = $this->requestService->createRequest(
                $requestData,
                $request->hasFile('attachments') ? $request->file('attachments') : []
            );

            // Log activity
            activity()
                ->performedOn($newRequest)
                ->causedBy(Auth::user())
                ->log('تم إضافة طلب جديد: ' . $newRequest->requestType->name);

            bump_requests_list_cache_version();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'    => true,
                    'message'    => 'تم إضافة الطلب بنجاح',
                    'request_id' => $newRequest->id,
                ]);
            }

            return redirect()->route('requests.index')
                ->with('success', 'تم إضافة الطلب بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في التحقق من البيانات',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'خطأ في التحقق من البيانات');
        } catch (RequestWorkflowException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error creating request', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $message = 'حدث خطأ أثناء إضافة الطلب. يرجى المحاولة مرة أخرى.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message, 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $message);
        }
    }

    public function edit(Request $httpRequest, RequestModel $requestModel)
    {
        $this->authorize('update', $requestModel);
        $requestModel->load(['requestType', 'status', 'mokalaf', 'attachments', 'responses.section', 'responses.department', 'payments.user']);

        $requestTypes = Cache::remember('request_types_all', 3600, function () {
            return RequestType::select('id', 'name')->orderBy('name')->get();
        });

        $mokalafs = $this->mokalafsForEdit($requestModel);

        if ($httpRequest->ajax()) {
            return view('requests.edit_modal_fragment', compact('requestModel', 'requestTypes', 'mokalafs'));
        }

        return view('requests.edit', compact('requestModel', 'requestTypes', 'mokalafs'));
    }

    public function update(UpdateRequestRequest $request, RequestModel $requestModel)
    {
        $this->authorize('update', $requestModel);

        try {
            $requestData               = $request->validated();
            $requestData['updated_by'] = Auth::id();

            $this->requestService->updateRequest(
                $requestModel,
                $requestData,
                $request->hasFile('attachments') ? $request->file('attachments') : []
            );

            // Log activity
            activity()
                ->performedOn($requestModel)
                ->causedBy(Auth::user())
                ->log('تم تعديل الطلب: ' . $requestModel->requestType->name);

            bump_requests_list_cache_version();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث الطلب بنجاح',
                ]);
            }

            return redirect()->route('requests.index')
                ->with('success', 'تم تحديث الطلب بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating request', [
                'request_id' => $requestModel->id,
                'user_id'    => Auth::id(),
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تحديث الطلب. يرجى المحاولة مرة أخرى.',
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الطلب. يرجى المحاولة مرة أخرى.');
        }
    }

    public function updateStatus(Request $request, RequestModel $requestModel)
    {
        $this->authorize('update', $requestModel);
        $request->validate([
            'status_id'     => 'required|exists:request_statuses,id',
            'status_reason' => 'nullable|string|max:1000',
        ], [
            'status_id.required' => 'الحالة مطلوبة',
            'status_id.exists'   => 'الحالة المحددة غير موجودة',
        ]);

        try {
            $newStatus = RequestStatus::find($request->status_id);
            if (! $newStatus) {
                throw new RequestWorkflowException('الحالة المحددة غير موجودة');
            }

            $updatedRequest = $this->requestService->updateStatus(
                $requestModel,
                $newStatus,
                $request->input('status_reason')
            );

            // Log activity
            activity()
                ->performedOn($requestModel)
                ->causedBy(Auth::user())
                ->log('تم تغيير حالة الطلب إلى: ' . $newStatus->name);

            if ($request->expectsJson()) {
                return response()->json([
                    'success'       => true,
                    'message'       => 'تم تحديث حالة الطلب بنجاح',
                    'status'        => [
                        'id'    => $updatedRequest->status->id,
                        'name'  => $updatedRequest->status->name,
                        'color' => $updatedRequest->status->color ?? 'secondary',
                    ],
                    'status_reason' => $updatedRequest->status_reason,
                ]);
            }

            return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح');
        } catch (RequestWorkflowException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error updating request status', [
                'request_id' => $requestModel->id,
                'error'      => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تحديث الحالة'], 422);
            }

            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الحالة.');
        }
    }

    public function addPayment(Request $httpRequest, RequestModel $request)
    {
        $this->authorize('update', $request);

        $rules = [
            'amount'           => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,bank,check',
            'payment_date'     => 'required|date|after_or_equal:today|before_or_equal:today',
            'reference_number' => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ];
        $validated = $httpRequest->validate($rules, [
            'required'        => 'حقل :attribute مطلوب.',
            'numeric'         => 'حقل :attribute يجب أن يكون رقم.',
            'min'             => 'حقل :attribute يجب أن يكون على الأقل :min.',
            'in'              => 'حقل :attribute قيمة غير صالحة.',
            'date'            => 'حقل :attribute يجب أن يكون تاريخ صالح.',
            'after_or_equal'  => 'يجب أن يكون تاريخ الدفعة بتاريخ اليوم فقط.',
            'before_or_equal' => 'يجب أن يكون تاريخ الدفعة بتاريخ اليوم فقط.',
        ]);

        try {
            $payment = $this->requestService->addPayment($request, [
                'amount'         => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_date'   => $validated['payment_date'],
                'receipt_number' => $validated['reference_number'] ?? null,
                'notes'          => $validated['notes'] ?? null,
            ]);

            // Log activity
            activity()
                ->performedOn($payment)
                ->causedBy(Auth::user())
                ->log('تم إضافة دفعة للطلب: ' . $request->requestType->name . ' - المبلغ: ' . $validated['amount']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $request->refresh();

        return response()->json([
            'success'          => true,
            'message'          => 'تمت إضافة الدفعة بنجاح',
            'payment'          => $payment,
            'paid_amount'      => $request->paid_amount,
            'remaining_amount' => $request->remaining_amount,
        ]);
    }

    public function cancelRequestPayment(Request $httpRequest, RequestPayment $payment)
    {
        $requestModel = $payment->citizenRequest;
        if (! $requestModel) {
            abort(404);
        }
        $this->authorize('update', $requestModel);

        if ($payment->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'هذه الدفعة ملغاة بالفعل',
            ], 422);
        }

        $validated = $httpRequest->validate([
            'cancellation_reason' => 'required|string|min:5',
        ], [
            'cancellation_reason.required' => 'سبب الإلغاء مطلوب.',
            'cancellation_reason.min'      => 'سبب الإلغاء يجب ألا يقل عن 5 أحرف.',
        ]);

        try {
            // Log activity before cancel
            activity()
                ->performedOn($payment)
                ->causedBy(Auth::user())
                ->log('تم إلغاء دفعة للطلب: ' . $requestModel->requestType->name . ' - السبب: ' . $validated['cancellation_reason']);

            $this->requestService->cancelPayment($payment, $validated['cancellation_reason']);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $requestModel->refresh();

        return response()->json([
            'success'          => true,
            'message'          => 'تم إلغاء الدفعة بنجاح',
            'paid_amount'      => $requestModel->paid_amount,
            'remaining_amount' => $requestModel->remaining_amount,
        ]);
    }

    public function destroy(RequestModel $request)
    {
        $this->authorize('delete', $request);

        try {
            // Log activity before delete
            activity()
                ->performedOn($request)
                ->causedBy(Auth::user())
                ->log('تم حذف الطلب: ' . $request->requestType->name);

            $this->requestService->deleteRequest($request);
            bump_requests_list_cache_version();

            return redirect()->route('requests.index')
                ->with('success', 'تم حذف الطلب بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting request', [
                'request_id' => $request->id,
                'user_id'    => Auth::id(),
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return redirect()->route('requests.index')
                ->with('error', 'حدث خطأ أثناء حذف الطلب. يرجى المحاولة مرة أخرى.');
        }
    }

    public function show(RequestModel $request)
    {
        $this->authorize('view', $request);
        $request = $this->requestRepository->findWithRelations($request->id);

        if (! $request) {
            abort(404, 'الطلب غير موجود');
        }

        $request->responses = $request->responses->sortBy('created_at');

        if (request()->expectsJson() || request()->is('api/*')) {
            return new RequestResource($request);
        }

        $requestStatuses = Cache::remember('request_statuses_all', 3600, function () {
            return RequestStatus::select('id', 'name')->orderBy('name')->get();
        });

        $activities = Activity::forSubject($request)
            ->with('causer')
            ->latest()
            ->limit(20)
            ->get();

        return view('requests.show', compact('request', 'requestStatuses', 'activities'));
    }

    public function print(RequestModel $request)
    {
        $this->authorize('view', $request);
        $request = $this->requestRepository->findWithRelations($request->id);

        if (! $request) {
            abort(404, 'الطلب غير موجود');
        }

        return view('requests.print', compact('request'));
    }

    public function apiIndex(Request $request)
    {
        $filters = $request->only(['search', 'request_type_id', 'status_id', 'mokalaf_id', 'is_urgent', 'from_date', 'to_date']);
        $perPage = $request->get('per_page', 10);

        $requests = $this->requestRepository->getWithRelations($filters, $perPage);

        return RequestResource::collection($requests);
    }

    public function downloadAttachment(RequestModel $request, RequestAttachment $attachment)
    {
        if ($attachment->request_id !== $request->id) {
            abort(404, 'المرفق غير موجود');
        }

        $filePath = storage_path('app/public/' . $attachment->file_path);

        if (file_exists($filePath)) {
            return response()->download($filePath, $attachment->file_name);
        }

        return back()->with('error', 'الملف غير موجود');
    }

    public function deleteAttachment(RequestModel $request, RequestAttachment $attachment)
    {
        try {
            if ($attachment->request_id !== $request->id) {
                abort(404, 'المرفق غير موجود');
            }

            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();

            return back()->with('success', 'تم حذف المرفق بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف المرفق: ' . $e->getMessage());
        }
    }

    public function autocompleteIdentity(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $data   = [];
        $seen   = [];

        if ($search !== '') {
            $mokalafs = Mokalaf::where(function ($query) use ($search) {
                $query->where('identity_number', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%");
            })->limit(20)->get();

            foreach ($mokalafs as $mokalaf) {
                $data[]             = $this->mokalafAutocompleteItem($mokalaf);
                $seen[$mokalaf->id] = true;
            }

            $craftLicenses = CraftLicense::query()
                ->where(function ($query) use ($search) {
                    $query->where('file_number', 'like', "%{$search}%")
                        ->orWhere('id_number', 'like', "%{$search}%");
                })
                ->limit(20)
                ->get(['file_number', 'id_number']);

            foreach ($craftLicenses as $license) {
                $mokalaf = Mokalaf::where(function ($query) use ($license) {
                    if ($license->id_number) {
                        $query->orWhere('identity_number', $license->id_number);
                    }
                    if ($license->file_number) {
                        $query->orWhere('license_number', $license->file_number);
                    }
                })->first();

                if (! $mokalaf || isset($seen[$mokalaf->id])) {
                    continue;
                }

                $data[]             = $this->mokalafAutocompleteItem($mokalaf, $license->file_number);
                $seen[$mokalaf->id] = true;
            }
        }

        return response()->json($data);
    }

    public function autocompleteName(Request $request)
    {
        $search = $request->get('q');
        $data   = [];

        if ($search) {
            $mokalafs = Mokalaf::where('name', 'like', "%{$search}%")
                ->limit(20)
                ->get();

            foreach ($mokalafs as $mokalaf) {
                $data[] = [
                    'id'   => $mokalaf->id,
                    'text' => $mokalaf->name,
                ];
            }
        }

        return response()->json($data);
    }

    public function autocompleteMokalaf(Request $request)
    {
        $search = $request->get('q');
        $data   = [];

        if ($search) {
            $mokalafs = Mokalaf::where('id', 'like', "%{$search}%")
                ->limit(20)
                ->get();

            foreach ($mokalafs as $mokalaf) {
                $data[] = [
                    'id'   => $mokalaf->id,
                    'text' => $mokalaf->id . ' - ' . $mokalaf->name,
                ];
            }
        }

        return response()->json($data);
    }

    public function getMokalafByIdentity(Request $request)
    {
        $identityNumber = trim((string) $request->get('identity_number', ''));

        if (! $identityNumber) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الهوية مطلوب',
            ]);
        }

        $mokalaf = Mokalaf::where('identity_number', $identityNumber)
            ->orWhere('license_number', $identityNumber)
            ->first();

        if (! $mokalaf) {
            $license = CraftLicense::where('file_number', $identityNumber)
                ->orWhere('id_number', $identityNumber)
                ->first(['file_number', 'id_number']);

            if ($license) {
                $mokalaf = Mokalaf::where(function ($query) use ($license) {
                    if ($license->id_number) {
                        $query->orWhere('identity_number', $license->id_number);
                    }
                    if ($license->file_number) {
                        $query->orWhere('license_number', $license->file_number);
                    }
                })->first();
            }
        }

        if ($mokalaf) {
            return response()->json([
                'success' => true,
                'mokalaf' => $this->mokalafResponseData($mokalaf),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'لم يتم العثور على المكلف',
        ]);
    }

    public function getMokalafData(Request $request)
    {
        $mokalafId = $request->get('mokalaf_id');

        if (! $mokalafId) {
            return response()->json([
                'success' => false,
                'message' => 'رقم المكلف مطلوب',
            ]);
        }

        $mokalaf = Mokalaf::find($mokalafId);

        if ($mokalaf) {
            return response()->json([
                'success' => true,
                'mokalaf' => $this->mokalafResponseData($mokalaf),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'لم يتم العثور على المكلف',
        ]);
    }

    protected function mokalafsForEdit(RequestModel $requestModel)
    {
        $mokalafs = Cache::remember('mokalafs_all', 1800, function () {
            return Mokalaf::select('id', 'name')->orderBy('name')->get();
        });

        $mid = (int) ($requestModel->mokalaf_id ?? 0);
        if ($mid === 0) {
            return $mokalafs;
        }

        $alreadyListed = $mokalafs->contains(fn($m) => (int) $m->id === $mid);
        if ($alreadyListed) {
            return $mokalafs;
        }

        $extra = Mokalaf::withTrashed()->select('id', 'name', 'deleted_at')->find($mid);
        if ($extra === null) {
            return $mokalafs;
        }

        return $mokalafs->push($extra)->unique('id')->sortBy('name')->values();
    }

    private function mokalafAutocompleteItem(Mokalaf $mokalaf, ?string $fallbackIdentifier = null): array
    {
        $identifier = $mokalaf->identity_number ?: $mokalaf->license_number ?: $fallbackIdentifier ?: '-';

        return [
            'id'         => $mokalaf->id,
            'text'       => $identifier . ' - ' . $mokalaf->name,
            'mokalaf_id' => $mokalaf->id,
            'identifier' => $identifier,
            'name'       => $mokalaf->name,
            'address'    => $mokalaf->address,
            'mobile'     => $mokalaf->mobile,
        ];
    }

    private function mokalafResponseData(Mokalaf $mokalaf): array
    {
        return [
            'id'              => $mokalaf->id,
            'name'            => $mokalaf->name,
            'identity_number' => $mokalaf->identity_number,
            'license_number'  => $mokalaf->license_number,
            'jebaia_no'       => $mokalaf->license_number,
            'address'         => $mokalaf->address,
            'mobile'          => $mokalaf->mobile,
        ];
    }
}