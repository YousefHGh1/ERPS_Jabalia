<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('index employees');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Employee $employee): bool
    {
        return $user->can('view employees');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('store employees');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->can('update employees');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employee $employee): bool
    {
        return $user->can('delete employees');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Employee $employee): bool
    {
        return $user->can('restore employees');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->can('force delete employees');
    }

    /**
     * Determine whether the user can manage employee job info.
     */
    public function manageJobInfo(User $user, Employee $employee): bool
    {
        return $user->can('manage employee job info');
    }

    /**
     * Determine whether the user can manage employee holidays.
     */
    public function manageHolidays(User $user, Employee $employee): bool
    {
        return $user->can('manage employee holidays');
    }

    /**
     * Determine whether the user can manage employee permissions.
     */
    public function managePermissions(User $user, Employee $employee): bool
    {
        return $user->can('manage employee permissions');
    }
}
