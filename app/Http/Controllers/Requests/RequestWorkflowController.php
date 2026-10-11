<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Models\RequestWorkflow;
use App\Models\RequestType;
use App\Models\Department;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index request_workflows', ['only' => ['index']]);
        $this->middleware('permission:store request_workflows', ['only' => ['store']]);
        $this->middleware('permission:update request_workflows', ['only' => ['update']]);
        $this->middleware('permission:delete request_workflows', ['only' => ['destroy']]);
    }

    public function index($requestTypeId)
    {
        $requestType = RequestType::findOrFail($requestTypeId);
        $page_title = 'مسارات نوع الطلب: ' . $requestType->name;
        $workflows = RequestWorkflow::where('request_type_id', $requestTypeId)
            ->with(['section', 'department', 'previousWorkflow.section', 'previousWorkflow.department'])
            ->orderBy('step_order', 'asc')
            ->get();
        $sections = Section::select('id', 'name')->orderBy('name')->get();
        $departments = Department::select('id', 'name')->orderBy('name')->get();
        return view('requests.workflows.index', compact('page_title', 'workflows', 'requestType', 'sections', 'departments'));
    }

    public function create($requestTypeId)
    {
        $requestType = RequestType::findOrFail($requestTypeId);
        $sections = Section::select('id', 'name')->orderBy('name')->get();
        $departments = Department::select('id', 'name')->orderBy('name')->get();
        $existingWorkflows = RequestWorkflow::where('request_type_id', $requestTypeId)
            ->orderBy('step_order', 'asc')
            ->get();
        $page_title = 'إضافة مسار لنوع الطلب: ' . $requestType->name;
        return view('requests.workflows.create', compact('page_title', 'requestType', 'sections', 'departments', 'existingWorkflows'));
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
}
