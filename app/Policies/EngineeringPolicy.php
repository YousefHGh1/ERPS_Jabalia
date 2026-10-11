<?php

namespace App\Policies;

use App\Models\Engineering;
use App\Models\User;

class EngineeringPolicy
{
    /**
     * Determine whether the user can view any engineerings.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('index engineering');
    }

    /**
     * Determine whether the user can view the engineering.
     */
    public function view(User $user, Engineering $engineering): bool
    {
        return $user->can('index engineering');
    }

    /**
     * Determine whether the user can create engineerings.
     */
    public function create(User $user): bool
    {
        return $user->can('store engineering');
    }

    /**
     * Determine whether the user can update the engineering.
     */
    public function update(User $user, Engineering $engineering): bool
    {
        return $user->can('edit engineering');
    }

    /**
     * Determine whether the user can delete the engineering.
     */
    public function delete(User $user, Engineering $engineering): bool
    {
        return $user->can('delete engineering');
    }
}
