<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Sync Admin Role With All Permissions Command
 * 
 * Links the Admin role with ALL permissions in the system,
 * preventing duplicates using Laravel's syncPermissions method.
 */
class SyncAdminPermissions extends Command
{
    protected $signature = 'permissions:sync-admin';

    protected $description = 'Sync Admin role with all permissions (prevent duplicates)';

    public function handle(): int
    {
        $this->info('🔄 بدء مزامنة Admin مع جميع الصلاحيات...');
        $this->newLine();

        // البحث عن دور admin
        $admin = Role::where('name', 'admin')
            ->where('guard_name', 'web')
            ->first();

        if (!$admin) {
            // محاولة إيجاد دور مشابه
            $admin = Role::where('name', 'like', '%admin%')
                ->where('guard_name', 'web')
                ->first();
        }

        if (!$admin) {
            $this->error('❌ لم يتم العثور على دور Admin!');
            return self::FAILURE;
        }

        $this->info("✅ تم العثور على دور: {$admin->name}");

        // الحصول على جميع الصلاحيات
        $allPermissions = Permission::where('guard_name', 'web')->get();
        $totalCount = $allPermissions->count();

        if ($totalCount === 0) {
            $this->warn('⚠️ لا توجد صلاحيات في النظام!');
            return self::SUCCESS;
        }

        // مزامنة Admin مع جميع الصلاحيات (syncPermissions يمنع التكرار)
        $admin->syncPermissions($allPermissions);

        // التحقق من عدد الصلاحيات المرتبطة بـ Admin
        $adminPermissionsCount = $admin->permissions()->count();

        $this->newLine();
        $this->info('═══════════════════════════════════════════');
        $this->info("✅ تمت المزامنة بنجاح:");
        $this->info("   👤 الدور: {$admin->name}");
        $this->info("   📝 إجمالي الصلاحيات في النظام: {$totalCount}");
        $this->info("   🔑 الصلاحيات المرتبطة بـ Admin: {$adminPermissionsCount}");
        $this->info('═══════════════════════════════════════════');

        // عرض ملخص الصلاحيات الجديدة (إن وجدت)
        $existingCount = min($totalCount, $adminPermissionsCount);
        $newPermissions = $totalCount - $existingCount;

        if ($newPermissions > 0) {
            $this->comment("   ➕ صلاحيات جديدة مضافة: {$newPermissions}");
        }

        return self::SUCCESS;
    }
}