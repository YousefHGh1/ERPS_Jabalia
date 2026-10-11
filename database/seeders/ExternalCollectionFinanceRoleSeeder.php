<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExternalCollectionFinanceRoleSeeder extends Seeder
{
    protected string $guard = 'web';

    public function run(): void
    {
        // إنشاء دور المحاسب المالي للتحصيل الخارجي
        $financeRole = Role::firstOrCreate([
            'name' => 'finance_external_collection',
            'guard_name' => $this->guard,
        ]);

        // صلاحيات المحاسب المالي
        $financePermissions = [
            'external-collection-view',
            'external-collection-create',
            'external-collection-edit',
            'external-collection-payment',
        ];

        // منح الصلاحيات لدور المحاسب
        $availablePermissions = Permission::where('guard_name', $this->guard)
            ->whereIn('name', $financePermissions)
            ->pluck('name')
            ->all();

        $financeRole->syncPermissions($availablePermissions);

        $this->command->info('Finance External Collection role seeded successfully!');
        $this->command->info('Role: finance_external_collection');
        $this->command->info('Permissions: View, Create, Edit, and Payment processing');
    }
}
