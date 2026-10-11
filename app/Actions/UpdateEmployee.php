<?php
namespace App\Actions;

use App\Models\Employee;
use App\Models\EmployeeJobHistory;
use Illuminate\Support\Facades\DB;

class UpdateEmployee
{
    /**
     * Update an employee and create job history if needed.
     */
    public function execute(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $oldValues = $employee->only(['department_id', 'section_id', 'branch_id', 'job_id']);

            $employee->update($data);

            // Check if job-related fields changed
            $newValues = $employee->only(['department_id', 'section_id', 'branch_id', 'job_id']);

            if ($this->jobFieldsChanged($oldValues, $newValues)) {
                $employee->load(['job.category', 'section', 'department', 'branch']);
                $this->createJobHistory($employee, 'updated');
            }

            return $employee->fresh();
        });
    }

    /**
     * Check if job-related fields changed.
     */
    protected function jobFieldsChanged(array $old, array $new): bool
    {
        return $old['department_id'] != $new['department_id'] ||
            $old['section_id'] != $new['section_id'] ||
            $old['branch_id'] != $new['branch_id'] ||
            $old['job_id'] != $new['job_id'];
    }

    /**
     * Create job history for the employee.
     */
    protected function createJobHistory(Employee $employee, string $type = 'assigned'): void
    {
        $category           = $employee->job->category;
        $employeeJobHistory = [
            'type'          => $type,
            'status'        => 'Active',
            'employee_id'   => $employee->id,
            'job'           => $employee->job->name,
            'category'      => optional($category)->name,
            'degree'        => optional($category)->start_degree,
            'section'       => $employee->section?->name,
            'department'    => $employee->department?->name,
            'branch'        => $employee->branch?->name,
            'bonus_percent' => optional($category)->bonus_percent ?? '0',
            'date'          => now()->format('Y-m-d'),
        ];

        EmployeeJobHistory::create($employeeJobHistory);
    }
}
