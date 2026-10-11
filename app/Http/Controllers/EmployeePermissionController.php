<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\NotificationService;

class EmployeePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index EmployeePermissions', ['only' => ['index']]);
        $this->middleware('permission:store EmployeePermissions', ['only' => ['store']]);
        $this->middleware('permission:update EmployeePermissions', ['only' => ['update']]);
        $this->middleware('permission:delete EmployeePermissions', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $employee_id = Auth::user()->employee_id;
        if (!$employee_id) {
            return redirect()->back()->with('error', 'هذا الحساب غير مرتبط بموظف');
        }
        $query = EmployeePermission::where('to_employee', $employee_id);

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $employeePermissions = $query->paginate(10);
        return view('employee.EmployeePermission', compact('employeePermissions'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'         => 'required|string',
            'date'         => 'required|date',
            'type'         => 'required',
            'start_time'   => 'required',
            'end_time'     => 'required',
        ],[
            'required' => __('required'),
            'string' => __('string'),
        ]);
        $validatedData['status'] = 'pending';
        $validatedData['employee_id'] = Auth::user()->employee_id;

        if (!$validatedData['employee_id']) {
             return redirect()->back()->with('error', 'هذا الحساب غير مرتبط بموظف');
        }

        $employee = Employee::findOrFail($validatedData['employee_id']);

        $section_director = $employee->section?->director?->employee_id;
        $department_director = $employee->department?->director?->employee_id;

        $to_employee = $section_director;
        if ($employee->id == $section_director) {
            $to_employee = $department_director;
        }

        if ($employee->id == $department_director) {
            $to_employee = 1; // Admin
        }

        $validatedData['to_employee'] = $to_employee ?? $department_director ?? 1;

        $permission = EmployeePermission::create($validatedData);

        // Notify the supervisor
        $supervisor = Employee::find($validatedData['to_employee']);
        if ($supervisor && $supervisor->user) {
            NotificationService::send($supervisor->user, [
                'type' => 'permission',
                'title' => __('New Permission Request'),
                'message' => __('Employee :name has requested a permission', ['name' => $employee->name]),
                'link' => route('EmployeePermissions.index'),
                'icon' => 'fa-clock',
                'priority' => 'normal',
            ]);
        }
        return redirect()->back()->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {
        $employeePermission = EmployeePermission::findOrFail($id);
        $validatedData = $request->validate([
            'name'         => 'required|string',
            'date'         => 'required|date',
            'type'         => 'required',
            'start_time'   => 'required',
            'end_time'     => 'required',
        ],[
            'required' => __('required'),
            'string' => __('string'),
        ]);
        $validatedData['status'] = 'pending';
        $validatedData['employee_id'] = Auth::user()->employee_id;

        if (!$validatedData['employee_id']) {
            return redirect()->back()->with('error', 'هذا الحساب غير مرتبط بموظف');
        }

        $employee = Employee::findOrFail($validatedData['employee_id']);

        $section_director = $employee->section?->director?->employee_id;
        $department_director = $employee->department?->director?->employee_id;

        $to_employee = $section_director;
        if ($employee->id == $section_director) {
            $to_employee = $department_director;
        }

        if ($employee->id == $department_director) {
            $to_employee = 1; // Admin
        }

        $validatedData['to_employee'] = $to_employee ?? $department_director ?? 1;

        $employeePermission->update($validatedData);
        return redirect()->back()->with('success', __('updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($employeePermission)
    {
        $EmployeePermission = EmployeePermission::findOrFail($employeePermission);
        $EmployeePermission->delete();
        return redirect()->back()
        ->with('success', __('deleted successfully'));
    }

    public function accept_permission($id){
        $EmployeePermission = EmployeePermission::findOrfail($id);
        $EmployeePermission->status = "accepted";
        $EmployeePermission->save();

        // Notify the employee
        $employee = Employee::find($EmployeePermission->employee_id);
        if ($employee && $employee->user) {
            NotificationService::send($employee->user, [
                'type' => 'permission',
                'title' => __('Permission Request Accepted'),
                'message' => __('Your permission request for :date has been accepted', [
                    'date' => $EmployeePermission->date
                ]),
                'link' => route('EmployeePermissions.index'),
                'icon' => 'fa-check-circle',
                'priority' => 'normal',
            ]);
        }

        return redirect()->back()
                ->with('success', __('updated successfully'));
       }
       public function reject_permission($id){
        $EmployeePermission = EmployeePermission::findOrfail($id);
        $EmployeePermission->status = "rejected";
        $EmployeePermission->save();

        // Notify the employee
        $employee = Employee::find($EmployeePermission->employee_id);
        if ($employee && $employee->user) {
            NotificationService::send($employee->user, [
                'type' => 'permission',
                'title' => __('Permission Request Rejected'),
                'message' => __('Your permission request for :date has been rejected', [
                    'date' => $EmployeePermission->date
                ]),
                'link' => route('EmployeePermissions.index'),
                'icon' => 'fa-times-circle',
                'priority' => 'high',
            ]);
        }

        return redirect()->back()
                ->with('success', __('updated successfully'));
       }
}
