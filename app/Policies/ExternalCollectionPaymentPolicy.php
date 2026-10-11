<?php

namespace App\Policies;

use App\Models\ExternalCollectionPayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExternalCollectionPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('external-collection-view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExternalCollectionPayment $payment): bool
    {
        return $user->can('external-collection-view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('external-collection-payment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExternalCollectionPayment $payment): bool
    {
        return $user->can('external-collection-payment') && $payment->status === 'paid';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExternalCollectionPayment $payment): bool
    {
        return false; // Payments should not be deleted, only cancelled
    }

    /**
     * Determine whether the user can cancel the payment.
     */
    public function cancel(User $user, ExternalCollectionPayment $payment): bool
    {
        return $user->can('external-collection-payment') && $payment->status === 'paid';
    }
}
