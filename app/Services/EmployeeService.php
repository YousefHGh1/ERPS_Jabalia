<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class EmployeeService
{
    protected EmployeeRepository $repository;

    public function __construct(EmployeeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): Collection
    {
        return $this->repository->getAllWithRelations();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateWithRelations($perPage);
    }

    public function search(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = $request->get('q', '');
        return $this->repository->search($query, $perPage);
    }

    public function find(int $id): ?Employee
    {
        return $this->repository->with([
            'department',
            'section',
            'branch',
            'job',
            'dependents',
            'bonuses',
            'holidays'
        ])->find($id);
    }

    public function create(array $data): Employee
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Employee
    {
        // Hash password if provided and not empty
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getByDepartment(int $departmentId): Collection
    {
        return $this->repository->getByDepartment($departmentId);
    }

    public function getStats(): array
    {
        return [
            'total' => $this->repository->model->count(),
            'active' => $this->repository->model->where('status', 'active')->count(),
            'inactive' => $this->repository->model->where('status', 'inactive')->count(),
        ];
    }
}
