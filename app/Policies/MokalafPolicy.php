<?php

namespace App\Policies;

use App\Models\CraftLicense;
use App\Models\Engineering;
use App\Models\ImprisonmentOrder;
use App\Models\Mokalaf;
use App\Models\Notice;
use App\Models\Request as RequestModel;
use App\Models\Reservation;
use App\Models\User;

class MokalafPolicy
{
    /**
     * Determine whether the user can view any mokalafs.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('index mokalafs');
    }

    /**
     * Determine whether the user can view the mokalaf.
     */
    public function view(User $user, Mokalaf $mokalaf): bool
    {
        return $user->can('index mokalafs');
    }

    /**
     * Determine whether the user can create mokalafs.
     */
    public function create(User $user): bool
    {
        return $user->can('store mokalafs');
    }

    /**
     * Determine whether the user can update the mokalaf.
     */
    public function update(User $user, Mokalaf $mokalaf): bool
    {
        return $user->can('update mokalafs');
    }

    /**
     * Determine whether the user can delete the mokalaf.
     * لا يجوز حذف مكلف لديه أي سجلات مرتبطة (طلبات، تراخيص، ملفات هندسة، أوامر حبس، حجوزات، إخطارات).
     */
    public function delete(User $user, Mokalaf $mokalaf): bool
    {
        if (!$user->can('delete mokalafs')) {
            return false;
        }

        $identity = $mokalaf->identity_number;
        $license = $mokalaf->license_number;

        if (RequestModel::where('mokalaf_id', $mokalaf->id)->exists()) {
            return false;
        }

        if ($identity || $license) {
            if (CraftLicense::query()
                ->where(function ($q) use ($identity, $license) {
                    if ($identity) $q->orWhere('id_number', $identity);
                    if ($license) {
                        $q->orWhere('id_number', $license)
                            ->orWhere('file_number', $license);
                    }
                })
                ->exists()) {
                return false;
            }
        }

        if ($identity && Engineering::where('national_id', $identity)->exists()) {
            return false;
        }

        if ($identity && ImprisonmentOrder::where('defendant_identity', $identity)->exists()) {
            return false;
        }

        if ($identity && Reservation::query()
            ->whereHas('defendant', fn ($q) => $q->where('identity_number', $identity))
            ->orWhereHas('summoner', fn ($q) => $q->where('identity_number', $identity))
            ->exists()) {
            return false;
        }

        if ($identity && Notice::where('identity_number', $identity)->exists()) {
            return false;
        }

        return true;
    }
}
