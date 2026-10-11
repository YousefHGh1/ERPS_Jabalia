<?php

namespace App\Console;

use App\Console\Commands\CloseDailyBatches;
use App\Models\Employee;
use App\Models\EmployeeLateHistory;
use App\Models\User;
use App\Models\ZKData;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Date;
use Rats\Zkteco\Lib\ZKTeco;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function (\App\Services\AttendanceService $attendanceService) {
            $attendanceService->syncAttendance();
        })->everyFiveMinutes()->withoutOverlapping();

        $schedule->call(function () {
            $employees = Employee::whereHas('EmployeeJobInfo', function ($q) {
                $q->where('type', 'مصنف' || 'غير مصنف');
            })->get();
            foreach ($employees as $employee) {
                $oneYearLater = Date::parse($employee->created_at)->addYears(5);
                if (now() >= $oneYearLater) {
                    if ($employee->EmployeeJobInfo->current_degree != $employee->job->category->end_degree) {
                        $employee->EmployeeJobInfo->current_degree = $employee->job->category->start_degree - 1;
                        $employee->EmployeeJobInfo->save();
                    }
                }
            }
        })->monthly();

        $schedule->call(function () {
            $employees = Employee::whereHas('EmployeeJobInfo', function ($q) {
                $q->where('type', 'مصنف' || 'غير مصنف');
            })->get();
            foreach ($employees as $employee) {
                $oneYearLater = Date::parse($employee->created_at)->addYear();
                if (now() >= $oneYearLater) {
                    if ($employee->EmployeeJobInfo->periodic_bonus != 4) {
                        $employee->EmployeeJobInfo->periodic_bonus = $employee->EmployeeJobInfo->periodic_bonus + 1;
                        $employee->EmployeeJobInfo->save();
                    } else {
                        $employee->EmployeeJobInfo->periodic_bonus = 0;
                        $employee->EmployeeJobInfo->save();
                    }
                }
            }
        })->monthly();

        // تنبيهات الحرف المتأخرة عن الدفع
        $schedule->call(function () {
            $licenses = \App\Models\CraftLicense::where('remaining', '>', 0)->get();
            if ($licenses->count() > 0) {
                \App\Services\NotificationService::notifyAdmins([
                    'type' => 'finance',
                    'title' => 'مطالبات مالية متأخرة',
                    'message' => 'هناك ' . $licenses->count() . ' رخصة حرفة لديها مبالغ متبقية لم تدفع بعد.',
                    'link' => route('craft_licenses.index', ['status' => 'unpaid']), // Assuming unpadi filter logic
                    'icon' => 'flaticon-price-tag',
                    'priority' => 'high'
                ]);
            }
        })->weekly();

        // ترصيد رسوم رخص الحرف سنوياً
        // يمكن تفعيله عبر ضبط الإعداد المناسب في config/services.php
        $schedule->call(function (\App\Services\CraftLicenseService $craftLicenseService) {
            if (config('services.craft_licenses.auto_accrue', false) && ! now()->isFriday()) {
                $craftLicenseService->accrueAnnualFeesForCurrentYear();
            }
        })->dailyAt('02:00');

        // إغلاق الرزم اليومية تلقائياً في نهاية يوم العمل
        $schedule->command('batches:close-daily')
            ->weekdays() // أيام العمل فقط (الإثنين - الجمعة)
            ->dailyAt('18:00') // الساعة 6 مساءً
            ->withoutOverlapping()
            ->runInBackground();

        // $schedule->call(function () {
        //     $employees = Employee::whereHas('EmployeeJobInfo')->get();
        //     foreach ($employees as $employee) {
        //         $existingData = EmployeeLateHistory::where('emp_id', $employee->EmployeeJobInfo->emp_number)->get();
        //         if (!$existingData) {
        //             $emp_number = $employee->EmployeeJobInfo->emp_number;
        //             $shifts = $employee->EmployeeJobInfo->shifts;
        //             $total_late_minutes = 0;
        //             foreach ($shifts as $shift) {
        //                 $dataQuery = ZKData::where('emp_id', $emp_number)
        //                     ->where('type', 0)
        //                     ->get();
        //                 foreach ($dataQuery as $dd) {
        //                     $carbonDate = Carbon::parse($dd->arrival_time);
        //                     $dayOfWeek = $carbonDate->format('l');
        //                     if (in_array($dayOfWeek, $shift->days)) {

        //                         $date1 = Carbon::parse($dd->arrival_time)->setDate(now()->year, now()->month, now()->day);
        //                         $date2 = Carbon::parse($shift->getLateStartTimestamp())->setDate(now()->year, now()->month, now()->day);
        //                         $late_difference = $date1->diff($date2);

        //                         $minutesDifference = ($date1->format('H:i') > $date2->format('H:i')) ? $late_difference->h * 60 + $late_difference->i : 0;

        //                         if ($minutesDifference > 0) {
        //                             $total_late_minutes += $minutesDifference;
        //                             $total_late_minutes += $shift->start_late;
        //                         }
        //                     }
        //                 }
        //             }
        //             if ($total_late_minutes > 0) {
        //                 $employee['total_lates'] = $total_late_minutes / 60;
        //                 EmployeeLateHistory::create([
        //                     'emp_name' => $employee->name,
        //                     'emp_id' => $emp_number,
        //                     'late_hours' => $employee['total_lates'],
        //                 ]);
        //             }
        //         }
        //     }
        // })->everySecond();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
