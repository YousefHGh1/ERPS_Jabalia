<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Models\Request as RequestModel;
use App\Models\RequestResponse;
use App\Models\ResponseType;
use App\Models\RequestWorkflow;
use App\Models\Department;
use App\Models\Section;
use App\Models\Employee;
use App\Models\User;
use App\Http\Requests\StoreRequestMailRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RequestMailController extends Controller
{
    public function __construct()
    {
        // Rate limiting للعمليات الحساسة
        $this->middleware('throttle:20,1')->only(['store', 'update']);
    }

    /**
     * صفحة تشخيص بريد الطلبات (للمطورين فقط - غير متاح في الإنتاج)
     */
    public function debug()
    {
        $canAccess = $this->canAccessRequestMail();
        $allowedSectionIds = $canAccess ? $this->getAllowedSectionIds()->values() : [];
        return response()->json([
            'message' => 'Request mail debug (dev only)',
            'can_access' => $canAccess,
            'allowed_section_ids' => $allowedSectionIds,
            'hint' => 'استخدم /requests-mail/data?debug=1 لتفاصيل أكثر',
        ]);
    }

    /**
     * التحقق من صلاحية الدخول إلى بريد الطلبات
     * الأدمن دائماً مسموح؛ الموظفون بأقسام أو دوائر أو من لديه صلاحية البريد
     */
    private function canAccessRequestMail(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        // الأدمن دائماً مسموح (بدون الاعتماد على صلاحيات قد لا تكون موجودة)
        try {
            if ($user->hasRole('admin')) {
                return true;
            }
        } catch (\Throwable $e) {
            // تجاهل إذا فشل التحقق من الدور
        }
        // الموظفون التابعون للأقسام/الدوائر يمكنهم رؤية الطلبات (بدون صلاحيات)
        $employee = $user->employee;
        if ($employee) {
            return true;
        }
        // صلاحيات البريد
        try {
            return $user->hasPermissionTo('mail.view')
                || $user->hasPermissionTo('mail.create')
                || $user->hasPermissionTo('mail.update')
                || $user->hasPermissionTo('mail.delete');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * التحقق من صلاحية تعديل حالة الطلب (الرد على الطلب)
     */
    private function canUpdateRequestStatus(): bool
    {
        $user = Auth::user();
        try {
            if ($user->hasRole('admin')) {
                return true;
            }
        } catch (\Throwable $e) {
            // تجاهل
        }
        if ($user->employee) {
            return true;
        }
        try {
            return $user->hasPermissionTo('mail.create') || $user->hasPermissionTo('mail.update');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * وحدات المستخدم المسموح له بعرضها في بريد الطلبات (أقسام + دوائر)
     * - من له صلاحية بريد/أدمن يرى الكل
     * - موظف القسم يرى قسمه
     * - موظف الدائرة (بدون قسم) يرى كل أقسام دائرته + ردود الدائرة نفسها
     */
    private function getAllowedSectionIds(): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        $employee = $user->employee;

        // من يملك صلاحيات بريد الطلبات يرى جميع الأقسام حتى لو كان مرتبطاً بقسم محدد
        try {
            if (
                $user->hasRole('admin')
                || $user->hasPermissionTo('mail.view')
                || $user->hasPermissionTo('mail.create')
                || $user->hasPermissionTo('mail.update')
                || $user->hasPermissionTo('mail.delete')
            ) {
                return Section::pluck('id');
            }
        } catch (\Throwable $e) {
            // في حال تعذر فحص الصلاحيات نكمل بالمنطق الاحتياطي أدناه
        }

        if ($employee && $employee->section_id !== null) {
            // موظف قسم: يرى بريد قسمه فقط
            return collect([$employee->section_id]);
        }

        // موظف دائرة بدون قسم: يرى كل أقسام دائرته
        if ($employee && $employee->department_id !== null) {
            return Section::where('department_id', $employee->department_id)->pluck('id');
        }

        // أي مستخدم آخر (غير مرتبط بقسم أو دائرة) لا يرى شيئاً
        return collect();
    }

    /**
     * دوائر المستخدم المسموح له بعرضها (لردود الدوائر المباشرة)
     */
    private function getAllowedDepartmentIds(): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        $employee = $user->employee;

        try {
            if (
                $user->hasRole('admin')
                || $user->hasPermissionTo('mail.view')
                || $user->hasPermissionTo('mail.create')
                || $user->hasPermissionTo('mail.update')
                || $user->hasPermissionTo('mail.delete')
            ) {
                return Department::pluck('id');
            }
        } catch (\Throwable $e) {
        }

        // موظف قسم بدون department_id مباشر: استنتاج دائرته من قسمه
        if ($employee && $employee->section_id !== null) {
            $departmentId = $employee->section->department_id ?? null;
            return $departmentId !== null ? collect([$departmentId]) : collect();
        }

        if ($employee && $employee->department_id !== null) {
            return collect([$employee->department_id]);
        }

        return collect();
    }

    /**
     * عرض الطلبات قيد الانتظار (response_type = 4)
     */
    public function waiting(Request $request)
    {
        if (!$this->canAccessRequestMail()) {
            return redirect()->route('home.index')->with('error', 'غير مصرح لك بالوصول');
        }

        $mail = 4; // قيد الانتظار
        $search = $request->get('search');
        $responses = $this->getMailResponses($mail, 15, $search);
        return view('requests.mail.index', compact('mail', 'responses', 'search'));
    }

    /**
     * عرض الطلبات المقبولة
     */
    public function accepted(Request $request)
    {
        if (!$this->canAccessRequestMail()) {
            return redirect()->route('home.index')->with('error', 'غير مصرح لك بالوصول');
        }

        $mail = 1; // مقبول
        $search = $request->get('search');
        $responses = $this->getMailResponses($mail, 15, $search);
        return view('requests.mail.index', compact('mail', 'responses', 'search'));
    }

    /**
     * عرض الطلبات المعلقة
     */
    public function pending(Request $request)
    {
        if (!$this->canAccessRequestMail()) {
            return redirect()->route('home.index')->with('error', 'غير مصرح لك بالوصول');
        }

        $mail = 2; // معلق
        $search = $request->get('search');
        $responses = $this->getMailResponses($mail, 15, $search);
        return view('requests.mail.index', compact('mail', 'responses', 'search'));
    }

    /**
     * عرض الطلبات المرفوضة
     */
    public function unaccepted(Request $request)
    {
        if (!$this->canAccessRequestMail()) {
            return redirect()->route('home.index')->with('error', 'غير مصرح لك بالوصول');
        }

        $mail = 3; // مرفوض
        $search = $request->get('search');
        $responses = $this->getMailResponses($mail, 15, $search);
        return view('requests.mail.index', compact('mail', 'responses', 'search'));
    }

    /**
     * عرض جميع الطلبات
     */
    public function all(Request $request)
    {
        if (!$this->canAccessRequestMail()) {
            return redirect()->route('home.index')->with('error', 'غير مصرح لك بالوصول');
        }

        $mail = 0; // الكل
        $search = $request->get('search');
        $responses = $this->getMailResponses($mail, 15, $search);
        return view('requests.mail.index', compact('mail', 'responses', 'search'));
    }

    /**
     * عرض تفاصيل الطلب مع إمكانية الرد
     */
    public function show($requestId, $responseId)
    {
        if (!$this->canAccessRequestMail()) {
            return redirect()->route('home.index')->with('error', 'غير مصرح لك بالوصول');
        }

        $request = RequestModel::with([
            'requestType',
            'status',
            'mokalaf',
            'responses.responseType',
            'responses.section',
            'responses.department',
            'responses.responsedBy',
            'responses.workflow',
            'responses.workflow.section',
            'responses.workflow.department',
        ])->findOrFail($requestId);

        $response = RequestResponse::with(['responseType', 'section', 'department', 'responsedBy', 'workflow', 'workflow.section', 'workflow.department'])
            ->findOrFail($responseId);

        // التحقق من أن الرد يخص الطلب
        if ($response->request_id != $requestId) {
            return redirect()->route('requests.mail.waiting')
                ->with('error', 'الرد غير موجود');
        }

        if (!$this->responseBelongsToEmployee($response, Auth::user()->employee)) {
            return redirect()->route('requests.mail.waiting')
                ->with('error', 'غير مصرح لك بالوصول إلى هذا الطلب');
        }

        // حساب حالة الطلب الحالية
        $status = $this->calculateRequestStatus($request);
        $canUpdateStatus = $this->canUpdateRequestStatus()
            && in_array((int) $response->response_type, [2, 4], true)
            && $this->responseBelongsToEmployee($response, Auth::user()->employee);

        $responseTypes = ResponseType::whereIn('id', [1, 2, 3])->get();

        return view('requests.mail.status', compact('request', 'response', 'status', 'responseTypes', 'canUpdateStatus'));
    }

    /**
     * عرض نموذج تعديل الرد
     */
    public function edit($id)
    {
        if (!$this->canAccessRequestMail()) {
            return response()->json(['error' => 'غير مصرح لك بالوصول'], 403);
        }
        $response = RequestResponse::with(['responseType', 'section', 'department', 'request'])
            ->findOrFail($id);
        if (!$this->responseBelongsToEmployee($response, Auth::user()->employee)) {
            return response()->json(['error' => 'هذا الرد غير مسند إلى حسابك الشخصي'], 403);
        }

        return response()->json($response);
    }

    /**
     * حفظ الرد (موافقة/رفض/تعليق)
     */
    public function store(StoreRequestMailRequest $request)
    {
        if (!$this->canAccessRequestMail()) {
            return response()->json(['error' => 'غير مصرح لك بالوصول'], 403);
        }

        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $requestModel = RequestModel::findOrFail($validated['request_id']);
            $user = Auth::user();
            $employee = $user->employee;
            // إذا وُجد response_id (صفحة التفاصيل ترسله) نحدّث هذا الرد بعد التحقق من الصلاحية
            if (!empty($validated['response_id'])) {
                $response = RequestResponse::where('id', $validated['response_id'])
                    ->where('request_id', $validated['request_id'])
                    ->whereIn('response_type', [4, 2]) // قيد الانتظار أو معلق (قابل للتعديل)
                    ->first();
                if (!$response) {
                    DB::rollback();
                    return response()->json(['error' => 'الرد غير موجود أو تم الرد عليه مسبقاً'], 400);
                }
                if (!$this->responseBelongsToEmployee($response, $employee)) {
                    DB::rollback();
                    return response()->json(['error' => 'هذا الرد غير مسند إلى حسابك الشخصي'], 403);
                }
            } else {
                if (!$employee) {
                    DB::rollback();
                    return response()->json(['error' => 'حساب المستخدم غير مرتبط بموظف'], 400);
                }
                $response = RequestResponse::where('request_id', $validated['request_id'])
                    ->where('assigned_employee_id', $employee->id)
                    ->where('response_type', 4)
                    ->first();
                if (!$response) {
                    DB::rollback();
                    return response()->json(['error' => 'لا يوجد رد قيد الانتظار مسند إلى حسابك لهذا الطلب'], 400);
                }
            }

            // تحديث الرد
            $response->update([
                'response_type' => $validated['response_type'],
                'notes' => $validated['notes'] ?? '-',
                'responsed_by' => Auth::id(),
                'responsed_at' => Carbon::now(),
                'ip_address' => $request->ip(),
                'host' => gethostname(),
            ]);

            // إنشاء responses للمسارات التالية (إذا كانت الموافقة)
            if ($validated['response_type'] == 1) { // موافقة
                $this->createNextWorkflowResponses($requestModel, $response, $request);
            }

            // تحديث حالة الطلب تلقائياً
            $this->updateRequestStatus($requestModel);

            DB::commit();

            bump_requests_list_cache_version();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الرد بنجاح',
                'response' => $response->load(['responseType', 'section', 'department', 'responsedBy'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error storing request mail response', [
                'user_id' => Auth::id(),
                'request_id' => $request->input('request_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'حدث خطأ أثناء حفظ الرد. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    /**
     * هل الرد يخص وحدة الموظف (قسمه أو دائرته)؟
     */
    private function responseBelongsToEmployee(RequestResponse $response, $employee): bool
    {
        return $employee && $response->assigned_employee_id !== null
            && (int) $response->assigned_employee_id === (int) $employee->id;
    }

    /**
     * إنشاء responses للمسارات التالية
     */
    private function createNextWorkflowResponses(RequestModel $requestModel, RequestResponse $response, Request $request): void
    {
        $nextWorkflows = RequestWorkflow::where('request_type_id', $requestModel->request_type_id)
            ->where('previous_workflow_id', $response->workflow_id)
            ->active()
            ->ordered()
            ->get();

        foreach ($nextWorkflows as $workflow) {
            // التحقق من عدم وجود response مسبق
            $existingResponse = RequestResponse::where('request_id', $requestModel->id)
                ->where('workflow_id', $workflow->id)
                ->first();

            if (!$existingResponse) {
                RequestResponse::create([
                    'response_type' => 4, // قيد الانتظار
                    'request_id' => $requestModel->id,
                    'workflow_id' => $workflow->id,
                    'assigned_employee_id' => $requestModel->responses()->where('workflow_id', $workflow->id)->value('assigned_employee_id')
                        ?? RequestResponse::where('request_id', $requestModel->id)->where('workflow_id', $workflow->id)->withTrashed()->value('assigned_employee_id'),
                    'section_id' => $workflow->section_id,
                    'department_id' => $workflow->department_id,
                    'notes' => '-',
                    'created_by' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'host' => gethostname(),
                ]);

                // إرسال إشعارات للموظفين في الوحدة التالية (قسم أو دائرة)
                $empQuery = Employee::where('status', 'active');
                if ($workflow->section_id) {
                    $empQuery->where('section_id', $workflow->section_id);
                } elseif ($workflow->department_id) {
                    $empQuery->where(function ($q) use ($workflow) {
                        $q->where('department_id', $workflow->department_id)
                            ->orWhereHas('section', fn($sq) => $sq->where('department_id', $workflow->department_id));
                    });
                }
                $employees = $empQuery->with('user')->get();

                foreach ($employees as $emp) {
                    if ($emp->user) {
                        // يمكن إضافة إشعار هنا
                    }
                }
            }
        }
    }

    /**
     * تحديث الرد (للمشرفين فقط)
     */
    public function update(Request $request, $id)
    {
        if (!$this->canAccessRequestMail()) {
            return response()->json(['error' => 'غير مصرح لك بالوصول'], 403);
        }

        $response = RequestResponse::findOrFail($id);
        if (!$this->responseBelongsToEmployee($response, Auth::user()->employee)) {
            return response()->json(['error' => 'هذا الرد غير مسند إلى حسابك الشخصي'], 403);
        }

        $validated = $request->validate([
            'response_type' => 'required|in:1,2,3',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $response->update([
                'response_type' => $validated['response_type'],
                'notes' => $validated['notes'] ?? $response->notes,
                'responsed_by' => Auth::id(),
                'responsed_at' => Carbon::now(),
            ]);

            // تحديث حالة الطلب
            $this->updateRequestStatus($response->request);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الرد بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }

    /**
     * حساب حالة الطلب بناءً على الردود.
     * عند رفض أو تعليق أي قسم تُحدَّث الحالة فوراً؛ مكتمل فقط عندما تكون جميع الردود موافقة.
     */
    private function calculateRequestStatus(RequestModel $request): string
    {
        $responses = $request->responses;

        if ($responses->where('response_type', 3)->count() > 0) {
            return 'مرفوض';
        }
        if ($responses->where('response_type', 2)->count() > 0) {
            return 'معلق';
        }
        if ($responses->where('response_type', 4)->count() > 0) {
            return 'قيد الانتظار';
        }
        if ($responses->where('response_type', 1)->count() > 0 &&
            $responses->where('response_type', '!=', 1)->count() == 0) {
            return 'مكتمل';
        }
        return 'قيد الانتظار';
    }

    /**
     * تحديث حالة الطلب تلقائياً بناءً على الردود
     */
    private function updateRequestStatus(RequestModel $request): void
    {
        $status = $this->calculateRequestStatus($request);

        $requestStatus = \App\Models\RequestStatus::where('name', $status)->first();

        if ($requestStatus) {
            $request->update(['status_id' => $requestStatus->id]);
        }
    }

    /**
     * الحصول على ردود بريد الطلبات مع العلاقات، للاستخدام في عرض Blade (بدون DataTables)
     *
     * @param int $mail 0=الكل، 1=مقبول، 2=معلق، 3=مرفوض، 4=قيد الانتظار
     */
    private function getMailResponses(int $mail, int $perPage = 15, ?string $search = null)
    {
        $employeeId = Auth::user()?->employee?->id;

        // استعلام الردود مع تحميل الطلب (حتى لو كان الطلب soft-deleted ليظهر في الجدول)
        $query = RequestResponse::with([
            'request' => fn ($q) => $q->withoutGlobalScope(\Illuminate\Database\Eloquent\SoftDeletingScope::class),
            'request.requestType',
            'request.mokalaf',
            'section',
            'department',
            'responseType',
        ]);

        $employeeId
            ? $query->where('assigned_employee_id', $employeeId)
            : $query->whereRaw('1 = 0');

        if ($mail === 1) {
            $query->where('response_type', 1);
        } elseif ($mail === 2) {
            $query->where('response_type', 2);
        } elseif ($mail === 3) {
            $query->where('response_type', 3);
        } elseif ($mail === 4) {
            $query->where('response_type', 4);
        }

        // بحث بسيط برقم الطلب، اسم المكلف، أو اسم القسم
        if ($search) {
            $searchTerm = trim($search);
            $query->where(function ($q) use ($searchTerm) {
                // رقم الطلب (لو رقم فقط)
                if (ctype_digit($searchTerm)) {
                    $q->orWhereHas('request', function ($rq) use ($searchTerm) {
                        $rq->where('id', (int) $searchTerm);
                    });
                }
                // نص الطلب أو اسم المكلف
                $q->orWhereHas('request', function ($rq) use ($searchTerm) {
                    $rq->where('text', 'like', "%{$searchTerm}%")
                       ->orWhereHas('mokalaf', function ($mq) use ($searchTerm) {
                           $mq->where('name', 'like', "%{$searchTerm}%");
                       });
                })
                // اسم القسم أو الدائرة
                ->orWhereHas('section', function ($sq) use ($searchTerm) {
                    $sq->where('name', 'like', "%{$searchTerm}%");
                })
                ->orWhereHas('department', function ($dq) use ($searchTerm) {
                    $dq->where('name', 'like', "%{$searchTerm}%");
                });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * إرجاع بيانات الطلبات للجدول (AJAX)
     */
    public function data(Request $request)
    {
        if (!$this->canAccessRequestMail()) {
            return response()->json(['error' => 'غير مصرح لك بالوصول'], 403);
        }

        $mail = (int) $request->get('mail', 0); // 0=all, 1=accepted, 2=pending, 3=rejected, 4=waiting
        $employeeId = Auth::user()?->employee?->id;

        // وضع تشخيصي (debug) لمعرفة سبب عدم ظهور الطلبات
        if ($request->has('debug')) {
            $allResponses = RequestResponse::select('section_id', 'response_type')
                ->whereNull('deleted_at')
                ->get();

            $queryWithRequest = RequestResponse::with(['request'])
                ->when($employeeId, fn ($q) => $q->where('assigned_employee_id', $employeeId), fn ($q) => $q->whereRaw('1 = 0'))
                ->whereHas('request');
            if ($mail !== 0) {
                $queryWithRequest->where('response_type', $mail);
            }
            $queryCount = (clone $queryWithRequest)->count();
            $firstPage = (clone $queryWithRequest)->orderBy('created_at', 'desc')->limit(15)->get();
            $nullRequestCount = $firstPage->filter(fn ($r) => $r->request === null)->count();

            $requestIds = RequestResponse::whereNull('deleted_at')->distinct()->pluck('request_id');
            $deletedRequestsCount = \App\Models\Request::withoutGlobalScopes()->whereIn('id', $requestIds)->whereNotNull('deleted_at')->count();

            return response()->json([
                'mail_param' => $mail,
                'assigned_employee_id' => $employeeId,
                'request_responses_total' => $allResponses->count(),
                'request_responses_by_section' => $allResponses->groupBy('section_id')->map->count(),
                'waiting_by_section' => $allResponses->where('response_type', 4)->groupBy('section_id')->map->count(),
                'query_with_whereHas_count' => $queryCount,
                'first_page_size' => $firstPage->count(),
                'null_request_in_page' => $nullRequestCount,
                'requests_soft_deleted_count' => $deletedRequestsCount,
            ]);
        }

        if (!$employeeId) {
            return response()->json([
                'draw' => $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        // استعلام الردود مع تحميل الطلب (حتى لو كان الطلب soft-deleted ليظهر في الجدول)
        $query = RequestResponse::with([
            'request' => fn ($q) => $q->withoutGlobalScope(\Illuminate\Database\Eloquent\SoftDeletingScope::class),
            'request.requestType',
            'request.mokalaf',
            'section',
            'department',
            'responseType',
        ])->where('assigned_employee_id', $employeeId);

        if ($mail === 1) {
            $query->where('response_type', 1);
        } elseif ($mail === 2) {
            $query->where('response_type', 2);
        } elseif ($mail === 3) {
            $query->where('response_type', 3);
        } elseif ($mail === 4) {
            $query->where('response_type', 4);
        }

        $result = $query->orderBy('created_at', 'desc')->paginate(15);

        $data = [];
        $index = 0;
        foreach ($result as $response) {
            $req = $response->request;
            if (!$req) {
                continue;
            }
            $data[] = [
                'DT_RowIndex' => $result->firstItem() + $index,
                'id' => $req->id,
                'request_type' => $req->requestType->name ?? '-',
                'mokalaf_name' => $req->mokalaf ? ($req->join && $req->common_name ? $req->common_name : $req->mokalaf->name) : '-',
                'section' => $response->unit_name ?? '-',
                'created_at' => $req->created_at->format('d-m-Y H:i'),
                'status' => '<span class="badge badge-lg badge-' .
                    ($response->response_type == 1 ? 'success' : ($response->response_type == 2 ? 'warning' : ($response->response_type == 3 ? 'danger' : 'info'))) .
                    '">' . ($response->responseType->name ?? '-') . '</span>',
                'actions' => '<a href="' . route('requests.mail.show', ['requestId' => $req->id, 'responseId' => $response->id]) .
                    '" class="btn btn-sm btn-primary"><i class="flaticon2-eye"></i> عرض</a>' .
                    (in_array((int) $response->response_type, [2, 4], true)
                        ? ' <a href="' . route('requests.mail.show', ['requestId' => $req->id, 'responseId' => $response->id]) . '" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i> تغيير الحالة</a>'
                        : '')
            ];
            $index++;
        }

        return response()->json([
            'draw' => (int) $request->get('draw', 1),
            'recordsTotal' => $result->total(),
            'recordsFiltered' => $result->total(),
            'data' => $data
        ]);
    }
}
