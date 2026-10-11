<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seeder مخصص لضمان حصول دور admin على جميع الصلاحيات
 * يتم تشغيله في النهاية بعد جميع الـ seeders الأخرى
 */
class AdminPermissionsSyncSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔒 بدء مزامنة صلاحيات admin...');

        // الحصول على جميع الصلاحيات مع guard_name = 'web'
        $allPermissions = Permission::where('guard_name', 'web')->get();

        if ($allPermissions->isEmpty()) {
            $this->command->warn('⚠️ لا توجد صلاحيات في قاعدة البيانات!');
            return;
        }

        // البحث عن دور admin
        $admin = Role::where('name', 'admin')->where('guard_name', 'web')->first();

        if (!$admin) {
            $this->command->error('❌ دور admin غير موجود!');
            return;
        }

        // مزامنة جميع الصلاحيات مع دور admin
        $admin->syncPermissions($allPermissions);

        $this->command->info("✅ تم مزامنة {$allPermissions->count()} صلاحية مع دور admin");

        // نفس الشيء لـ super-admin
        $superAdmin = Role::where('name', 'super-admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions($allPermissions);
            $this->command->info("✅ تم مزامنة {$allPermissions->count()} صلاحية مع دور super-admin");
        }
    }
}
