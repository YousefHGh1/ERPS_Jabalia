<?php

namespace App\Policies;

use App\Models\Notice;
use App\Models\User;

class NoticePolicy
{
    /**
     * Determine whether the user can view any notices.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('index notices');
    }

    /**
     * Determine whether the user can view the notice.
     */
    public function view(User $user, Notice $notice): bool
    {
        return $user->can('index notices');
    }

    /**
     * Determine whether the user can create notices.
     */
    public function create(User $user): bool
    {
        return $user->can('store notices');
    }

    /**
     * Determine whether the user can update the notice.
     */
    public function update(User $user, Notice $notice): bool
    {
        return $user->can('update notices');
    }

    /**
     * Determine whether the user can delete the notice.
     */
    public function delete(User $user, Notice $notice): bool
    {
        return $user->can('delete notices');
    }
}
