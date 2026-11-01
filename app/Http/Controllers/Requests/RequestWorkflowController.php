<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Models\RequestWorkflow;
use App\Models\RequestType;
use App\Models\Department;
use App\Models\Section;
use App\Models\Employee;
use App\Models\Request as RequestModel;
use App\Models\RequestResponse;
use App\Jobs\SendRequestCreatedNotificationsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RequestWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index request_workflows', ['only' => ['index']]);
        $this->middleware('permission:store request_workflows', ['only' => ['store']]);
        $this->middleware('permission:update request_workflows', ['only' => ['update', 'synchronize']]);
        $this->middleware('permission:update requests', ['only' => ['resendPendingNotifications']]);
        $this->middleware('permission:delete request_workflows', ['only' => ['destroy']]);
    }

    public function index($requestTypeId)
    {
        $requestType = RequestType::findOrFail($requestTypeId);
        $page_title = 'مسارات نوع الطلب: ' . $requestType->name;
        $workflows = RequestWorkflow::where('request_type_id', $requestTypeId)
            ->with([
                'section.displayDirector.employee',
                'department.displayDirector.employee',
                'department', 'defaultAssignedEmployee', 'previousWorkflow.section', 'previousWorkflow.department',
            ])
            ->orderBy('step_order', 'asc')
            ->get();
        $sections = Section::with('displayDirector')->select('id', 'name')->orderBy('name')->get();
        $departments = Department::with('displayDirector')->select('id', 'name')->orderBy('name')->get();
        $employees = Employee::active()->whereHas('user')->orderBy('name')->get(['id', 'name', 'section_id', 'department_id']);
        $workflowEmployeesData = $employees->map(fn ($employee) => [
            'id' => $employee->id,
            'name' => $employee->name,
            'section_id' => $employee->section_id,
            'department_id' => $employee->department_id,
        ])->values();
        $unitDirectors = [];
        foreach ($sections as $section) {
            $unitDirectors['section:' . $section->id] = $section->displayDirector?->employee_id;
        }
        foreach ($departments as $department) {
            $unitDirectors['department:' . $department->id] = $department->displayDirector?->employee_id;
        }
        return view('requests.workflows.index', compact('page_title', 'workflows', 'requestType', 'sections', 'departments', 'employees', 'workflowEmployeesData', 'unitDirectors'));
    }

    public function create($requestTypeId)
    {
        $requestType = RequestType::findOrFail($requestTypeId);
        $sections = Section::with('displayDirector')->select('id', 'name')->orderBy('name')->get();
        $departments = Department::with('displayDirector')->select('id', 'name')->orderBy('name')->get();
        $employees = Employee::active()->whereHas('user')->orderBy('name')->get(['id', 'name', 'section_id', 'department_id']);
        $workflowEmployeesData = $employees->map(fn ($employee) => [
            'id' => $employee->id,
            'name' => $employee->name,
            'section_id' => $employee->section_id,
            'department_id' => $employee->department_id,
        ])->values();
        $unitDirectors = [];
        foreach ($sections as $section) {
            $unitDirectors['section:' . $section->id] = $section->displayDirector?->employee_id;
        }
        foreach ($departments as $department) {
            $unitDirectors['department:' . $department->id] = $department->displayDirector?->employee_id;
        }
        $existingWorkflows = RequestWorkflow::where('request_type_id', $requestTypeId)
            ->orderBy('step_order', 'asc')
            ->get();
        $page_title = 'إضافة مسار لنوع الطلب: ' . $requestType->name;
        return view('requests.workflows.create', compact('page_title', 'requestType', 'sections', 'departments', 'existingWorkflows', 'employees', 'workflowEmployeesData', 'unitDirectors'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'request_type_id' => 'required|exists:request_types,id',
                'unit_type' => 'required|in:department,section',
                'unit_id' => 'required|integer',
                'previous_workflow_id' => 'nullable|exists:request_workflows,id',
                'step_order' => 'required|integer|min:1',
                'default_assigned_employee_id' => 'nullable|integer|exists:employees,id',
            ]);

            $requestTypeId = $request->request_type_id;
            $previousWorkflowId = $request->previous_workflow_id ?: null;
            $unit = $request->unit_type === 'department'
                ? Department::findOrFail($request->unit_id)
                : Section::findOrFail($request->unit_id);
            $sectionId = $request->unit_type === 'section' ? $request->unit_id : null;
            $departmentId = $request->unit_type === 'department' ? $request->unit_id : null;

            if ($previousWorkflowId) {
                $previousWorkflow = RequestWorkflow::findOrFail($previousWorkflowId);
                if ($previousWorkflow->request_type_id != $requestTypeId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'المسار السابق يجب أن ينتمي لنفس نوع الطلب'
                    ], 422);
                }
            }

            $duplicateCheck = RequestWorkflow::where('request_type_id', $requestTypeId)
                ->where('section_id', $sectionId)
                ->where('department_id', $departmentId)
                ->where('previous_workflow_id', $previousWorkflowId)
                ->first();

            if ($duplicateCheck) {
                return response()->json([
                    'success' => false,
                    'message' => 'يوجد مسار مشابه بالفعل (نفس القسم ونفس المسار السابق)'
                ], 422);
            }

            $workflow = RequestWorkflow::create([
                'request_type_id' => $requestTypeId,
                'section_id' => $sectionId,
                'department_id' => $departmentId,
                'previous_workflow_id' => $previousWorkflowId,
                'name' => $unit->name,
                'description' => null,
                'step_order' => $request->step_order,
                'is_active' => true,
                'default_assigned_employee_id' => $this->validatedAssignee($request, $unit),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المسار بنجاح',
                'data' => $workflow
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating workflow', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ المسار: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $workflow = RequestWorkflow::with(['section', 'department', 'previousWorkflow.section', 'previousWorkflow.department'])->findOrFail($id);
        return response()->json($workflow);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'unit_type' => 'required|in:department,section',
            'unit_id' => 'required|integer',
            'previous_workflow_id' => 'nullable|exists:request_workflows,id',
            'step_order' => 'required|integer|min:1',
            'default_assigned_employee_id' => 'nullable|integer|exists:employees,id',
        ]);

        $workflow = RequestWorkflow::findOrFail($id);
        $requestTypeId = $workflow->request_type_id;
        $previousWorkflowId = $request->previous_workflow_id;
        $unit = $request->unit_type === 'department'
            ? Department::findOrFail($request->unit_id)
            : Section::findOrFail($request->unit_id);

        if ($previousWorkflowId) {
            $previousWorkflow = RequestWorkflow::findOrFail($previousWorkflowId);
            if ($previousWorkflow->request_type_id != $requestTypeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'المسار السابق يجب أن ينتمي لنفس نوع الطلب'
                ], 422);
            }
        }

        $workflow->update([
            'section_id' => $request->unit_type === 'section' ? $request->unit_id : null,
            'department_id' => $request->unit_type === 'department' ? $request->unit_id : null,
            'previous_workflow_id' => $previousWorkflowId,
            'name' => $unit->name,
            'step_order' => $request->step_order,
            'default_assigned_employee_id' => $this->validatedAssignee($request, $unit),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المسار بنجاح'
        ]);
    }

    public function destroy($id)
    {
        $workflow = RequestWorkflow::findOrFail($id);
        $nextWorkflows = RequestWorkflow::where('previous_workflow_id', $id)->count();
        if ($nextWorkflows > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن الحذف! يوجد مسارات تالية مرتبطة بهذا المسار'
            ], 400);
        }
        $workflow->delete();
        return response()->json([
            'success' => true,
            'message' => 'تم الحذف بنجاح'
        ]);
    }

    public function synchronize(Request $request)
    {
        $validated = $request->validate([
            'request_type_id' => ['required', 'exists:request_types,id'],
        ]);

        $requestTypeId = (int) $validated['request_type_id'];
        $workflows = RequestWorkflow::where('request_type_id', $requestTypeId)
            ->active()->ordered()->with([
                'section.displayDirector.employee.user',
                'department.displayDirector.employee.user',
            ])->get();
        if ($workflows->isEmpty()) {
            return back()->withErrors(['synchronize' => 'لا توجد مسارات نشطة لمزامنتها.']);
        }

        $workflowIds = $workflows->pluck('id');
        $counts = ['created' => 0, 'updated' => 0, 'removed' => 0];

        DB::transaction(function () use ($requestTypeId, $workflows, $workflowIds, &$counts) {
            foreach ($workflows as $workflow) {
                if ($workflow->default_assigned_employee_id) {
                    continue;
                }
                $director = $workflow->section?->displayDirector?->employee
                    ?? $workflow->department?->displayDirector?->employee;
                if ($director && strtolower((string) $director->status) === 'active' && $director->user) {
                    $workflow->update(['default_assigned_employee_id' => $director->id]);
                }
            }

            RequestModel::where('request_type_id', $requestTypeId)->chunkById(100, function ($requests) use ($workflows, $workflowIds, &$counts) {
                foreach ($requests as $requestModel) {
                    $counts['removed'] += RequestResponse::where('request_id', $requestModel->id)
                        ->where('response_type', 4)
                        ->whereNotNull('workflow_id')
                        ->whereNotIn('workflow_id', $workflowIds)
                        ->delete();

                    foreach ($workflows as $workflow) {
                        $employeeId = $workflow->default_assigned_employee_id
                            ?: ($workflow->section?->displayDirector?->employee_id
                                ?? $workflow->department?->displayDirector?->employee_id);
                        $response = RequestResponse::where('request_id', $requestModel->id)
                            ->where('workflow_id', $workflow->id)
                            ->where('response_type', 4)
                            ->first();

                        if ($response) {
                            $response->update([
                                'assigned_employee_id' => $employeeId,
                                'section_id' => $workflow->section_id,
                                'department_id' => $workflow->department_id,
                            ]);
                            $counts['updated']++;
                            continue;
                        }

                        $previousPendingResponse = RequestResponse::withTrashed()
                            ->where('request_id', $requestModel->id)
                            ->where('workflow_id', $workflow->id)
                            ->where('response_type', 4)
                            ->first();
                        if ($previousPendingResponse) {
                            $previousPendingResponse->restore();
                            $previousPendingResponse->update([
                                'assigned_employee_id' => $employeeId,
                                'section_id' => $workflow->section_id,
                                'department_id' => $workflow->department_id,
                            ]);
                            $counts['updated']++;
                            continue;
                        }

                        $hasCompletedResponse = RequestResponse::where('request_id', $requestModel->id)
                            ->where('workflow_id', $workflow->id)
                            ->exists();
                        if ($hasCompletedResponse) {
                            continue;
                        }

                        RequestResponse::create([
                            'response_type' => 4,
                            'request_id' => $requestModel->id,
                            'workflow_id' => $workflow->id,
                            'assigned_employee_id' => $employeeId,
                            'section_id' => $workflow->section_id,
                            'department_id' => $workflow->department_id,
                            'notes' => '-',
                            'created_by' => auth()->id() ?? $requestModel->created_by,
                        ]);
                        $counts['created']++;
                    }
                }
            });
        });

        Cache::put('request_mail_version', (int) Cache::get('request_mail_version', 0) + 1);

        return back()->with('workflow_sync_result', $counts);
    }

    public function resendPendingNotifications(Request $request)
    {
        $validated = $request->validate([
            'request_type_id' => ['nullable', 'exists:request_types,id'],
        ]);

        $requestIds = RequestResponse::query()
            ->where('response_type', 4)
            ->whereNotNull('assigned_employee_id')
            ->whereHas('request', function ($query) use ($validated) {
                if (!empty($validated['request_type_id'])) {
                    $query->where('request_type_id', $validated['request_type_id']);
                }
            })
            ->distinct()
            ->pluck('request_id');

        foreach ($requestIds as $requestId) {
            SendRequestCreatedNotificationsJob::dispatch((int) $requestId, true);
        }

        return back()->with('reminder_resend_result', [
            'requests' => $requestIds->count(),
        ]);
    }

    private function validatedAssignee(Request $request, $unit): ?int
    {
        $employeeId = $request->input('default_assigned_employee_id');
        if ($employeeId) {
            $employee = Employee::active()->whereHas('user')->findOrFail($employeeId);
            $belongsToUnit = $unit instanceof Section
                ? (int) $employee->section_id === (int) $unit->id
                : (int) $employee->department_id === (int) $unit->id;
            if (!$belongsToUnit) {
                abort(422, 'الموظف المسؤول يجب أن يكون تابعاً للجهة المختارة.');
            }
            return (int) $employee->id;
        }

        $director = $unit->displayDirector()->with('employee')->first();
        if ($director?->employee && $director->employee->status === 'active' && $director->employee->user) {
            return (int) $director->employee_id;
        }

        return null;
    }
}
