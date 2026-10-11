<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLateHistory;
use App\Models\ZKData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index employee_reports', ['only' => ['employee_reports', 'getEmployeeReport']]);
    }

    public function employee_reports()
    {
        return view('employee.employee_reports', [
            'employees' => Employee::get(['id', 'name']),
        ]);
    }

    public function getEmployeeReport(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required',
            'report_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'required' => __('required'),
        ]);

        if ($request->report_type == 'dept_summary') {
            $summary = $this->processDeptSummary($request);
            return view('employee.employee_reports', [
                'dept_summary' => $summary,
                'employees' => Employee::get(['id', 'name']),
            ]);
        }

        if ($request->employee_id == 'all') {
            $employees = Employee::whereHas('EmployeeJobInfo')
                ->with(['EmployeeJobInfo.shifts'])
                ->get();
            $late_employee = $this->processLateReportForAllEmployees($request, $employees);

            return view('employee.employee_reports', [
                'late_employee' => $late_employee,
                'employees' => Employee::get(['id', 'name']),
            ]);
        }

        $employee = Employee::where('id', $request->employee_id)->first();
        $late_array = $this->processLateReportForEmployee($request, $employee);

        return view('employee.employee_reports', [
            'late_array' => $late_array,
            'employee' => $employee,
            'employees' => Employee::get(['id', 'name']),
            'total' => $this->calculateTotalLateMinutes($late_array) / 60,
        ]);
    }

    private function processDeptSummary($request)
    {
        $departments = \App\Models\Department::with('employees.EmployeeJobInfo')->get();
        $summary = [];

        foreach ($departments as $dept) {
            $total_late = 0;
            $emp_count = $dept->employees->count();
            
            foreach ($dept->employees as $employee) {
                if ($employee->EmployeeJobInfo) {
                    $late_array = $this->processLateReportForEmployee($request, $employee);
                    $total_late += $this->calculateTotalLateMinutes($late_array);
                }
            }

            $summary[] = [
                'name' => $dept->name,
                'emp_count' => $emp_count,
                'total_late_hours' => round($total_late / 60, 2)
            ];
        }

        return $summary;
    }

    private function processLateReportForAllEmployees($request, $employees)
    {
        $late_employee = [];
        $empNumbers = $employees->pluck('EmployeeJobInfo.emp_number')->toArray();
        
        // جلب كل البيانات بطلب واحد بدلاً من الطلب داخل الحلقة
        $allZkData = ZKData::whereIn('emp_id', $empNumbers)
            ->where('type', 0)
            ->whereBetween('arrival_time', [$request->start_date, $request->end_date])
            ->get()
            ->groupBy('emp_id');

        foreach ($employees as $employee) {
            // Skip employees without EmployeeJobInfo
            if (!$employee->EmployeeJobInfo) {
                continue;
            }

            $emp_info = $employee->EmployeeJobInfo;
            $emp_number = $emp_info->emp_number;
            $shifts = $emp_info->shifts;
            $total_late_minutes = 0;
            
            $dataQuery = $allZkData->get($emp_number) ?? collect();

            if ($request->report_type == 'late_report') {
                foreach ($shifts as $shift) {
                    foreach ($dataQuery as $dd) {
                        $carbonDate = Carbon::parse($dd->arrival_time);
                        $dayOfWeek = $carbonDate->format('l');
                        if (in_array($dayOfWeek, $shift->days)) {
                            $minutesDifference = $this->calculateMinutesDifference($dd->arrival_time, $shift->getLateStartTimestamp());
                            if ($minutesDifference > 0) {
                                $total_late_minutes += $minutesDifference;
                                $total_late_minutes += $shift->start_late;
                            }
                        }
                    }
                }
                
                if ($total_late_minutes > 0) {
                    $employee['total_lates'] = $total_late_minutes / 60;
                    EmployeeLateHistory::create([
                        'emp_name' => $employee->name,
                        'emp_id' => $emp_number,
                        'late_hours' => $employee['total_lates'],
                    ]);
                    $late_employee[] = $employee;
                }
            }
        }
        
        return $late_employee;
    }

    // private function processLateReportForEmployee($request, $employee)
    // {
    //     $emp_number = $employee->EmployeeJobInfo->emp_number;
    //     $shift = $employee->EmployeeJobInfo->shift;
    //     $late_array = [];
    //     $total_late_minutes = 0;

    //     if ($request->report_type == 'late_report') {
    //         $dataQuery = ZKData::where('emp_id', $emp_number)
    //             ->where('type', 0)
    //             ->whereBetween('arrival_time', [$request->start_date, $request->end_date])
    //             ->get();

    //         foreach ($dataQuery as $dd) {
    //             $minutesDifference = $this->calculateMinutesDifference($dd->arrival_time, $shift->getLateStartTimestamp());

    //             if ($minutesDifference > 0) {
    //                 $dd['minutesDifference'] = $minutesDifference;
    //                 array_push($late_array, $dd);
    //                 $total_late_minutes += $minutesDifference;
    //             }
    //         }
    //     }

    //     return $late_array;
    // }
    private function processLateReportForEmployee($request, $employee)
    {
        // Check if employee has EmployeeJobInfo
        if (!$employee->EmployeeJobInfo) {
            return [];
        }

        $emp_number = $employee->EmployeeJobInfo->emp_number;
        $shifts = $employee->EmployeeJobInfo->shifts;
        $late_array = [];
        $total_late_minutes = 0;
    
        if ($request->report_type == 'late_report') {
            $dataQuery = ZKData::where('emp_id', $emp_number)
                ->where('type', 0)
                ->whereBetween('arrival_time', [$request->start_date, $request->end_date])
                ->get();
    
            foreach ($dataQuery as $dd) {
                $carbonDate = Carbon::parse($dd->arrival_time);
                $dayOfWeek = $carbonDate->format('l');
    
                foreach ($shifts as $shift) {
                    if (in_array($dayOfWeek, $shift->days)) {
                        $minutesDifference = $this->calculateMinutesDifference($dd->arrival_time, $shift->getLateStartTimestamp());
    
                        if ($minutesDifference > 0) {
                            $dd['minutesDifference'] = $minutesDifference;
                            array_push($late_array, $dd);
                            $total_late_minutes += $minutesDifference;
                        }
                    }
                }
            }
        }
    
        if ($total_late_minutes > 0) {
            $employee['total_lates'] = $total_late_minutes / 60; // Convert to hours if needed
        }
    
        return $late_array;
    }
    
    private function calculateMinutesDifference($arrivalTime, $lateStartTime)
    {
        $date1 = Carbon::parse($arrivalTime)->setDate(now()->year, now()->month, now()->day);
        $date2 = Carbon::parse($lateStartTime)->setDate(now()->year, now()->month, now()->day);
        $late_difference = $date1->diff($date2);

        return ($date1->format('H:i') > $date2->format('H:i')) ? $late_difference->h * 60 + $late_difference->i : 0;
    }

    private function calculateTotalLateMinutes($late_array)
    {
        $total_late_minutes = 0;

        foreach ($late_array as $dd) {
            $total_late_minutes += $dd['minutesDifference'];
        }

        return $total_late_minutes;
    }
}
