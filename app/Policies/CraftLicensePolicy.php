<?php

namespace App\Policies;

use App\Models\CraftLicense;
use App\Models\User;

class CraftLicensePolicy
{
    /**
     * Determine whether the user can view the craft license.
     */
    public function view(User $user, CraftLicense $craftLicense): bool
    {
        return $user->can('show craft_licenses');
    }

    /**
     * Determine whether the user can update the craft license.
     * يمكن إضافة قواعد ملكية لاحقاً عند وجود حقل created_by في الجدول.
     */
    public function update(User $user, CraftLicense $craftLicense): bool
    {
        return $user->can('edit craft_licenses');
    }

    /**
     * Determine whether the user can delete the craft license.
     */
    public function delete(User $user, CraftLicense $craftLicense): bool
    {
        return $user->can('delete craft_licenses');
    }
}
