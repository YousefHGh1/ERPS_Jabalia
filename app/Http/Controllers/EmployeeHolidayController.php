<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeHoliday;
use App\Models\EmployeeJobInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\NotificationService;

class EmployeeHolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index employee_holiday', ['only' => ['index']]);
        $this->middleware('permission:store employee_holiday', ['only' => ['store']]);
        $this->middleware('permission:update employee_holiday', ['only' => ['update']]);
        $this->middleware('permission:delete employee_holiday', ['only' => ['destroy']]);
    }

    public function index()
    {
        $employee_id = Auth::user()->employee_id;
        if (!$employee_id) {
            return redirect()->back()->with('error', 'هذا الحساب غير مرتبط بموظف');
        }
        $employeeholidays = EmployeeHoliday::where('for_employee' , $employee_id)->paginate(10);
        return view('holiday.EmployeeHolidays', compact('employeeholidays'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required',
            'holiday_id' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'vacation_days' => 'required|numeric',
            'spent_balance' => 'required|numeric|min:1',
            'remaining_balance' => 'required|numeric|min:0',
            'note' => 'required|string',
        ], [
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string' => __('string'),
            'regex' => __('regex'),
            'numeric' => __('numeric'),
        ]);
        $employee = Employee::findOrFail($validatedData['employee_id']);

        $section_director = $employee->section?->director?->employee_id;
        $department_director = $employee->department?->director?->employee_id;

        $for_employee = $section_director;
        if ($employee->id == $section_director) {
            $for_employee = $department_director;
        }
        
        if ($employee->id == $department_director) {
            $for_employee = 1; // Admin
        }

        $validatedData['for_employee'] = $for_employee ?? $department_director ?? 1;
        $validatedData['status'] = 'pending';
        
        $holiday = EmployeeHoliday::create($validatedData);

        // Notify the supervisor
        $supervisor = Employee::find($validatedData['for_employee']);
        if ($supervisor && $supervisor->user) {
            NotificationService::send($supervisor->user, [
                'type' => 'holiday',
                'title' => __('New Holiday Request'),
                'message' => __('Employee :name has requested a holiday', ['name' => $employee->name]),
                'link' => route('EmployeeHolidays.index'),
                'icon' => 'fa-calendar-check',
                'priority' => 'normal',
            ]);
        }


        EmployeeJobInfo::where('employee_id', $request->employee_id)->update([
            'vacation_days' => $holiday->remaining_balance,
        ]);

        return redirect()->back()
            ->with('success', __('saved successfully'));
    }

    public function update(Request $request, $id)
    {
        $employeeholidays = EmployeeHoliday::findOrfail($id);
        $validatedData = $request->validate([
            'employee_id' => 'required',
            'holiday_id' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'vacation_days' => 'required|numeric',
            'spent_balance' => 'required|numeric|min:1',
            'remaining_balance' => 'required|numeric|min:0',
            'note' => 'required|string',
        ], [
            'required' => __('required'),
            'validate_file' => __('validate_file'),
            'mimes' => __('mimes'),
            'string' => __('string'),
            'regex' => __('regex'),
            'numeric' => __('numeric'),
        ]);
        $employeeholidays->update($validatedData);
        EmployeeJobInfo::where('employee_id', $request->employee_id)->update([
            'vacation_days' => $employeeholidays->remaining_balance,
        ]);
        return redirect()->back()
            ->with('success', __('updated successfully'));
    }
    public function destroy($id)
    {
        $employeeholidays = EmployeeHoliday::findOrfail($id);
        $vacation_days = $employeeholidays->spent_balance + $employeeholidays->remaining_balance;
        EmployeeJobInfo::where('employee_id', $employeeholidays->employee_id)->update([
            'vacation_days' => $vacation_days,
        ]);
        $employeeholidays->delete();
        return redirect()->back()
            ->with('success', __('deleted successfully'));
    }

   public function acceptHoliday($id){
    $employeeholidays = EmployeeHoliday::findOrfail($id);
    $employeeholidays->status = "accepted";
    $employeeholidays->save();

    // Notify the employee
    $employee = Employee::find($employeeholidays->employee_id);
    if ($employee && $employee->user) {
        NotificationService::send($employee->user, [
            'type' => 'holiday',
            'title' => __('Holiday Request Accepted'),
            'message' => __('Your holiday request from :start to :end has been accepted', [
                'start' => $employeeholidays->start_date,
                'end' => $employeeholidays->end_date
            ]),
            'link' => route('EmployeeHolidays.index'),
            'icon' => 'fa-check-circle',
            'priority' => 'normal',
        ]);
    }

    return redirect()->back()
            ->with('success', __('updated successfully'));
   }

   public function rejectHoliday($id){
    $employeeholidays = EmployeeHoliday::findOrfail($id);
    $employeeholidays->status = "rejected";
    $employeeholidays->save();

    // Notify the employee
    $employee = Employee::find($employeeholidays->employee_id);
    if ($employee && $employee->user) {
        NotificationService::send($employee->user, [
            'type' => 'holiday',
            'title' => __('Holiday Request Rejected'),
            'message' => __('Your holiday request from :start to :end has been rejected', [
                'start' => $employeeholidays->start_date,
                'end' => $employeeholidays->end_date
            ]),
            'link' => route('EmployeeHolidays.index'),
            'icon' => 'fa-times-circle',
            'priority' => 'high',
        ]);
    }

    return redirect()->back()
            ->with('success', __('updated successfully'));
   }
}