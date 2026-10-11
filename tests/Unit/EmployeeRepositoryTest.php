<?php

namespace Tests\Unit;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmployeeRepository(new Employee());
    }

    public function test_can_create_employee(): void
    {
        $data = [
            'name' => 'Test Employee',
            'emp_number' => 'EMP001',
            'national_id' => '1234567890',
            'mobile' => '0599999999',
            'gender' => 'male',
            'status' => 'active',
        ];

        $employee = $this->repository->create($data);

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Test Employee', $employee->name);
    }

    public function test_can_find_employee_by_id(): void
    {
        $employee = Employee::factory()->create();
        $found = $this->repository->find($employee->id);

        $this->assertInstanceOf(Employee::class, $found);
        $this->assertEquals($employee->id, $found->id);
    }

    public function test_can_update_employee(): void
    {
        $employee = Employee::factory()->create();
        $updated = $this->repository->update($employee->id, ['name' => 'Updated Name']);

        $this->assertEquals('Updated Name', $updated->name);
    }

    public function test_can_delete_employee(): void
    {
        $employee = Employee::factory()->create();
        $result = $this->repository->delete($employee->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_can_get_employees_with_relations(): void
    {
        Employee::factory()->count(5)->create();
        $employees = $this->repository->getAllWithRelations();

        $this->assertGreaterThanOrEqual(5, $employees->count());
    }

    public function test_can_search_employees(): void
    {
        Employee::factory()->create(['name' => 'John Doe']);
        Employee::factory()->create(['name' => 'Jane Smith']);

        $results = $this->repository->search('John');

        $this->assertGreaterThanOrEqual(1, $results->count());
    }
}
