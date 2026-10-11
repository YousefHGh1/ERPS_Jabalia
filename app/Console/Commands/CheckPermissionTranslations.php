<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CheckPermissionTranslations extends Command
{
    protected $signature = 'permissions:translate-check
        {--missing : عرض فقط الصلاحيات والأدوار غير المترجمة}';

    protected $description = 'طباعة جميع الصلاحيات والأدوار مع الترجمة العربية للتأكد';

    public function handle(): int
    {
        $this->info('🔍 بدء فحص الترجمات العربية...');
        $this->newLine();

        // عرض الصلاحيات
        $this->info('═══════════════════════════════════════════');
        $this->info('📝 الصلاحيات (Permissions)');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        $permissions = Permission::orderBy('name')->get();
        $missingPerms = 0;
        $translatedPerms = 0;

        $this->table(
            ['الاسم الإنجليزي', 'الترجمة العربية', 'الحالة'],
            $permissions->map(function ($perm) use (&$missingPerms, &$translatedPerms) {
                $ar = perm_name_ar($perm->name);
                $isTranslated = $ar !== $perm->name;
                if ($isTranslated) {
                    $translatedPerms++;
                } else {
                    $missingPerms++;
                }

                if ($this->option('missing') && $isTranslated) {
                    return null;
                }

                return [
                    $perm->name,
                    $ar,
                    $isTranslated ? '✅ مترجم' : '❌ غير مترجم',
                ];
            })->filter()->toArray()
        );

        $this->newLine();
        $this->info("إجمالي الصلاحيات: {$permissions->count()} | مترجمة: {$translatedPerms} | غير مترجمة: {$missingPerms}");
        $this->newLine();

        // عرض الأدوار
        $this->info('═══════════════════════════════════════════');
        $this->info('👥 الأدوار (Roles)');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        $roles = Role::orderBy('name')->get();
        $missingRoles = 0;
        $translatedRoles = 0;

        $this->table(
            ['الاسم الإنجليزي', 'الترجمة العربية', 'عدد الصلاحيات', 'الحالة'],
            $roles->map(function ($role) use (&$missingRoles, &$translatedRoles) {
                $ar = role_name_ar($role->name);
                $isTranslated = $ar !== $role->name;
                if ($isTranslated) {
                    $translatedRoles++;
                } else {
                    $missingRoles++;
                }

                if ($this->option('missing') && $isTranslated) {
                    return null;
                }

                return [
                    $role->name,
                    $ar,
                    $role->permissions->count(),
                    $isTranslated ? '✅ مترجم' : '❌ غير مترجم',
                ];
            })->filter()->toArray()
        );

        $this->newLine();
        $this->info("إجمالي الأدوار: {$roles->count()} | مترجمة: {$translatedRoles} | غير مترجمة: {$missingRoles}");
        $this->newLine();

        // ملخص نهائي
        $this->info('═══════════════════════════════════════════');
        $this->info('📊 ملخص الترجمة');
        $this->info('═══════════════════════════════════════════');
        $this->info("✅ صلاحيات مترجمة: {$translatedPerms} / {$permissions->count()}");
        $this->info("✅ أدوار مترجمة: {$translatedRoles} / {$roles->count()}");

        if ($missingPerms > 0 || $missingRoles > 0) {
            $this->warn("⚠️  هناك عناصر غير مترجمة. قم بتحديث lang/ar/permissions.php");
        } else {
            $this->info('🎉 جميع العناصر مترجمة بنجاح!');
        }

        return self::SUCCESS;
    }
}
