<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    /**
     * Get employees with relationships (eager loading)
     */
    public function getAllWithRelations(): Collection
    {
        return $this->model->with([
            'department',
            'section',
            'branch',
            'job',
            'user'
        ])->get();
    }

    /**
     * Get paginated employees with relations
     */
    public function paginateWithRelations(int $perPage = 15)
    {
        return $this->model->with([
            'department',
            'section',
            'branch',
            'job'
        ])->paginate($perPage);
    }

    /**
     * Search employees
     */
    public function search(string $query, int $perPage = 15)
    {
        return $this->model->with(['department', 'job'])
            ->where('name', 'like', "%{$query}%")
            ->orWhere('emp_number', 'like', "%{$query}%")
            ->orWhere('national_id', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    /**
     * Get employees by department
     */
    public function getByDepartment(int $departmentId): Collection
    {
        return $this->model->with('job')
            ->where('department_id', $departmentId)
            ->get();
    }

    /**
     * Get employees by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }
}
