<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExternalCollectionRoleSeeder extends Seeder
{
    protected string $guard = 'web';

    public function run(): void
    {
        // الحصول على الأدوار الرئيسية
        $admin = Role::where('name', 'admin')->where('guard_name', $this->guard)->first();
        $employee = Role::where('name', 'employee')->where('guard_name', $this->guard)->first();
        
        if (!$admin || !$employee) {
            $this->command->error('Admin or Employee role not found. Please run RolePermissionsSeeder first.');
            return;
        }

        // صلاحيات التحصيل الخارجي
        $externalCollectionPermissions = [
            'external-collection-view',
            'external-collection-create', 
            'external-collection-edit',
            'external-collection-delete',
            'external-collection-payment',
        ];

        // منح جميع صلاحيات التحصيل الخارجي للمسؤول
        $availablePermissions = Permission::where('guard_name', $this->guard)
            ->whereIn('name', $externalCollectionPermissions)
            ->pluck('name')
            ->all();

        foreach ($availablePermissions as $permission) {
            if (!$admin->hasPermissionTo($permission)) {
                $admin->givePermissionTo($permission);
            }
        }

        // منح صلاحية العرض فقط للموظف
        $viewPermission = Permission::where('name', 'external-collection-view')
            ->where('guard_name', $this->guard)
            ->first();

        if ($viewPermission && !$employee->hasPermissionTo($viewPermission)) {
            $employee->givePermissionTo($viewPermission);
        }

        $this->command->info('External Collection permissions seeded successfully!');
        $this->command->info('Admin role: All external collection permissions');
        $this->command->info('Employee role: View external collections only');
    }
}
