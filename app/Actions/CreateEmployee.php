<?php
namespace App\Actions;

use App\Models\Employee;
use App\Models\EmployeeDependent;
use App\Models\EmployeeJobHistory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateEmployee
{
    /**
     * Create a new employee with associated records.
     */
    public function execute(array $data, array $relativesData = []): Employee
    {
        $employee = Employee::create($data);

        // Load relationships to avoid N+1 queries
        $employee->load(['job.category', 'section', 'department', 'branch']);

        // Create job history
        $this->createJobHistory($employee);

        // Create user account
        $this->createUserAccount($employee);

        // Create dependents/relatives
        if (! empty($relativesData)) {
            $this->createDependents($employee, $relativesData);
        }

        return $employee->fresh();
    }

    /**
     * Create job history for the employee.
     */
    protected function createJobHistory(Employee $employee): void
    {
        $category           = $employee->job->category;
        $employeeJobHistory = [
            'type'          => 'assigned',
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

    /**
     * Create user account for the employee.
     */
    protected function createUserAccount(Employee $employee): void
    {
        $employeeNumber = $employee->emp_number ?? $employee->czn_id ?? $employee->id;
        $email          = $employee->email ?? ($employeeNumber . '@jabalia.gov.ps');

        User::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'name'            => $employee->name,
                'email'           => $email,
                'employee_number' => (string) $employeeNumber,
                'password'        => Hash::make($employeeNumber),
            ]
        );
    }

    /**
     * Create dependents for the employee.
     */
    protected function createDependents(Employee $employee, array $relativesData): void
    {
        foreach ($relativesData as $relative) {
            $birthdate     = $relative['BIRTH_DT'] ?? null;
            $formattedDate = null;

            if ($birthdate) {
                $birthdateParts = explode('/', $birthdate);
                if (count($birthdateParts) === 3) {
                    $formattedDate = $birthdateParts[2] . '-' . $birthdateParts[1] . '-' . $birthdateParts[0];
                }
            }

            EmployeeDependent::create([
                'name'        => $relative['FULLNAME'] ?? '',
                'czn_id'      => $relative['IDNO_RELATIVE'] ?? '',
                'gender'      => $relative['SEX'] ?? '',
                'birth'       => $formattedDate,
                'relation'    => $relative['RELATIVE_DESC'] ?? '',
                'status'      => 'Active',
                'employee_id' => $employee->id,
            ]);
        }
    }
}
