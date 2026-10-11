<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionsSeeder extends Seeder
{
    protected string $guard = 'web';

    public function run(): void
    {
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => $this->guard,
        ]);

        $employee = Role::firstOrCreate([
            'name' => 'employee',
            'guard_name' => $this->guard,
        ]);

        // Keep admin aligned with the full current permission set.
        $admin->syncPermissions(Permission::where('guard_name', $this->guard)->pluck('name')->all());

        // Employee retains the previous read-only/default operational subset,
        // but now resolves permissions by name instead of brittle numeric IDs.
        $employeePermissions = [
            'index categories',
            'index bonus',
            'show employees',
            'index jobs',
            'index departments',
            'index sections',
            'index branches',
            'index departmentdirector',
            'index sectiondirector',
            'index branchdirector',
            'index employees',
            'index holidays',
            'index mokalafs',
            'index requests',
            'show requests',
            'index reservations',
            'show reservations',
            'index notices',
            'show notices',
            'index imprisonment_orders',
            'show imprisonment_orders',
            'index documents',
            'show documents',
            'download documents',
            'index real_time_dashboards',
            'view realtime stats',
            'download request attachments',
            'download reservation attachments',
            'download notice attachments',
            'download imprisonment order attachments',
            'external-collection-view',
        ];

        $availablePermissions = Permission::where('guard_name', $this->guard)
            ->whereIn('name', $employeePermissions)
            ->pluck('name')
            ->all();

        $employee->syncPermissions($availablePermissions);
    }
}
