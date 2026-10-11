<?php

namespace Tests\Unit\Actions;

use App\Actions\CreateEmployee;
use App\Models\Employee;
use App\Models\EmployeeJobHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateEmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_employee_with_action(): void
    {
        $data = [
            'name' => 'Test Employee',
            'emp_number' => 'EMP001',
            'national_id' => '1234567890',
            'mobile' => '0599999999',
            'gender' => 'male',
            'status' => 'active',
            'department_id' => 1,
            'job_id' => 1,
        ];

        $action = new CreateEmployee();
        $employee = $action->execute($data);

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertDatabaseHas('employees', ['emp_number' => 'EMP001']);
        $this->assertDatabaseHas('employee_job_histories', ['employee_id' => $employee->id]);
        $this->assertDatabaseHas('users', ['employee_id' => $employee->id]);
    }
}
