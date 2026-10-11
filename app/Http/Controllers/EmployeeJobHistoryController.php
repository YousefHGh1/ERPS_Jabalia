<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeJobHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EmployeeJobHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index job_history', ['only' => ['index']]);
        $this->middleware('permission:store job_history', ['only' => ['store']]);
        $this->middleware('permission:update job_history', ['only' => ['update']]);
        $this->middleware('permission:delete job_history', ['only' => ['destroy']]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required',
            'type'        => 'required|in:promotion,assigned,transfer,turning_off,back',
            'attachment'  => 'required|file|mimes:pdf',
            'date'        => 'required',
        ], [
            'required'      => __('required'),
            'validate_file' => __('validate_file'),
            'mimes'         => __('mimes'),
            'string'        => __('string'),
            'regex'         => __('regex'),
            'numeric'       => __('numeric'),
        ]);
        $employee                       = Employee::findOrFail($validatedData['employee_id']);
        $category                       = $employee->job->category;
        $validatedData['job']           = $employee->job->name;
        $validatedData['category']      = optional($category)->name;
        $validatedData['degree']        = $employee->EmployeeJobInfo->current_degree ?? '';
        $validatedData['section']       = $employee->section?->name;
        $validatedData['department']    = $employee->department?->name;
        $validatedData['branch']        = $employee->branch?->name;
        $validatedData['bonus_percent'] = optional($category)->bonus_percent ?? '0';

        if ($request->hasFile('attachment')) {
            $validatedData['attachment'] = $request->attachment->store('employee_job_history', 'public');
        }
        $jobHistory = EmployeeJobHistory::create($validatedData);
        
        // Log activity
        activity()
            ->performedOn($jobHistory)
            ->causedBy(auth()->user())
            ->log('تم إضافة سجل وظيفي: ' . __($validatedData['type']));
        
        return redirect()->back()
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {
        $employeeJobHistory = EmployeeJobHistory::findOrFail($id);
        $validatedData      = $request->validate([
            'employee_id' => 'required',
            'type'        => 'required|in:promotion,assigned,transfer,turning_off,back',
            'attachment'  => 'nullable|file|mimes:pdf',
            'date'        => 'required',
        ], [
            'required'      => __('required'),
            'validate_file' => __('validate_file'),
            'mimes'         => __('mimes'),
            'string'        => __('string'),
            'regex'         => __('regex'),
            'numeric'       => __('numeric'),
        ]);
        $employee                       = Employee::findOrFail($validatedData['employee_id']);
        $category                       = $employee->job->category;
        $validatedData['job']           = $employee->job->name;
        $validatedData['category']      = optional($category)->name;
        $validatedData['degree']        = $employee->EmployeeJobInfo->current_degree ?? '';
        $validatedData['section']       = $employee->section->name;
        $validatedData['department']    = $employee->department->name;
        $validatedData['branch']        = optional($employee->branch)->name;
        $validatedData['bonus_percent'] = optional($category)->bonus_percent ?? '0';

        if ($request->hasFile('attachment')) {
            File::delete('storage/' . $employeeJobHistory->attachment);
            $validatedData['attachment'] = $request->attachment->store('employee_job_history', 'public');
        }
        $employeeJobHistory->update($validatedData);
        
        // Log activity
        activity()
            ->performedOn($employeeJobHistory)
            ->causedBy(auth()->user())
            ->log('تم تعديل سجل وظيفي: ' . __($validatedData['type']));
        
        return redirect()->back()
            ->with('success', __('updated successfully'));
    }

    public function destroy(EmployeeJobHistory $job_history)
    {
        if ($job_history->attachment) {
            File::delete(storage_path('app/public/' . $job_history->attachment));
        }
        
        // Log activity before delete
        activity()
            ->performedOn($job_history)
            ->causedBy(auth()->user())
            ->log('تم حذف سجل وظيفي: ' . __($job_history->type));
        
        $job_history->delete();
        return redirect()->back()->with('success', __('deleted successfully'));
    }
}
