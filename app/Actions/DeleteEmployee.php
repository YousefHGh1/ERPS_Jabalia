<?php

namespace App\Actions;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class DeleteEmployee
{
    /**
     * Delete an employee and all related records.
     */
    public function execute(Employee $employee): bool
    {
        return DB::transaction(function () use ($employee) {
            // Delete related records
            $employee->employeeDependents()->delete();
            $employee->employeeJobHistories()->delete();
            $employee->employeeBonus()->delete();
            $employee->employeeHoliday()->delete();
            $employee->employeePermissions()->delete();
            $employee->employeeMainHoliday()->delete();
            $employee->attachments()->delete();

            // Delete user account
            if ($employee->user) {
                $employee->user->delete();
            }

            // Delete employee
            return $employee->delete();
        });
    }
}
