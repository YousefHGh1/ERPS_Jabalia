<?php
namespace App\Policies;

use App\Models\Request as RequestModel;
use App\Models\User;

class RequestPolicy
{
    /**
     * Determine whether the user can view any requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('index requests');
    }

    /**
     * Determine whether the user can view the request.
     */
    public function view(User $user, RequestModel $request): bool
    {
        return $user->can('show requests');
    }

    /**
     * Determine whether the user can create requests.
     */
    public function create(User $user): bool
    {
        return $user->can('create requests') || $user->can('store requests');
    }

    /**
     * Determine whether the user can update the request.
     * منشئ الطلب أو دور admin/super-admin يمكنهم التعديل.
     */
    public function update(User $user, RequestModel $request): bool
    {
        if (! $user->can('update requests')) {
            return false;
        }

        return $request->created_by === (int) $user->id || $user->hasRole('admin') || $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can delete the request.
     * منشئ الطلب أو دور admin/super-admin يمكنهم الحذف.
     */
    public function delete(User $user, RequestModel $request): bool
    {
        if (! $user->can('delete requests')) {
            return false;
        }

        return $request->created_by === (int) $user->id || $user->hasRole('admin') || $user->hasRole('super-admin');
    }
}
