<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface EmployeeRepositoryInterface extends RepositoryInterface
{
    /**
     * Get employees with relationships (eager loading)
     */
    public function getAllWithRelations(): Collection;

    /**
     * Get paginated employees with relations
     */
    public function paginateWithRelations(int $perPage = 15);

    /**
     * Search employees
     */
    public function search(string $query, int $perPage = 15);

    /**
     * Get employees by department
     */
    public function getByDepartment(int $departmentId): Collection;

    /**
     * Get employees by status
     */
    public function getByStatus(string $status): Collection;
}
