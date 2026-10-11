<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ModelRolesSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(1);
        if (!$user) {
            return;
        }

        $roleNames = ['admin', 'employee'];
        $roles = Role::whereIn('name', $roleNames)->pluck('name')->all();

        if ($roles === []) {
            return;
        }

        $user->syncRoles($roles);
    }
}
