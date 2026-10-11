<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * ينشئ دوراً واحداً لكل وحدة (كنترولر) ويربطها بجميع صلاحيات تلك الوحدة.
 * مثال: دور "مدير الفئات" يحتوي على index/store/update/delete categories.
 */
class ControllerRolesSeeder extends Seeder
{
    protected string $guard = 'web';

    /**
     * ربط المورد (من اسم الصلاحية) باسم الدور بالعربية.
     * المورد يُستخرج من اسم الصلاحية: إما الجزء قبل النقطة (mail.view → mail)
     * أو الجزء بعد أول مسافة (index categories → categories).
     */
    protected function getResourceToRoleArabic(): array
    {
        return [
            'categories' => 'مدير الفئات',
            'bonus' => 'مدير المكافآت',
            'jobs' => 'مدير الوظائف',
            'departments' => 'مدير الأقسام',
            'sections' => 'مدير الشعب',
            'branches' => 'مدير الفروع',
            'departmentdirector' => 'مدير مديري الأقسام',
            'sectiondirector' => 'مدير مديري الشعب',
            'branchdirector' => 'مدير مديري الفروع',
            'employees' => 'مدير الموظفين',
            'holidays' => 'مدير الإجازات',
            'EmployeePermissions' => 'مدير إذونات الموظفين',
            'permissions' => 'مدير الصلاحيات',
            'roles' => 'مدير الأدوار',
            'users' => 'مدير المستخدمين',
            'Shift' => 'مدير الورديات',
            'employee_holiday' => 'مدير إجازات الموظفين',
            'main_holiday' => 'مدير الإجازات الرئيسية',
            'zk' => 'مدير الحضور',
            'employee_bonus' => 'مدير مكافآت الموظفين',
            'employee_dependent' => 'مدير المعالين',
            'job_history' => 'مدير سجل الوظائف',
            'attachment' => 'مدير المرفقات',
            'job_info' => 'مدير معلومات الوظيفة',
            'employee_portal' => 'مدير بوابة الموظفين',
            'attendance_calendar' => 'مدير بوابة الموظفين',
            'portal_requests' => 'مدير بوابة الموظفين',
            'employee_reports' => 'مدير بوابة الموظفين',
            'craft_licenses' => 'مدير تراخيص الحرف',
            'analytics' => 'مدير تراخيص الحرف', // view analytics
            'notifications' => 'مدير الإشعارات',
            'vehicle' => 'مدير المركبات',
            'engineerings' => 'مدير الملفات الهندسية',
            'analytics engineerings' => 'مدير الملفات الهندسية',
            'mokalafs' => 'مدير المكلفين',
            'requests' => 'مدير الطلبات',
            'request attachments' => 'مدير الطلبات',
            'reservations' => 'مدير الحجوزات',
            'reservation attachments' => 'مدير الحجوزات',
            'notices' => 'مدير الإخطارات',
            'notice attachments' => 'مدير الإخطارات',
            'imprisonment_orders' => 'مدير أوامر الحبس',
            'imprisonment order attachments' => 'مدير أوامر الحبس',
            'documents' => 'مدير المستندات',
            'realtime dashboard' => 'مدير لوحة المتابعة اللحظية',
            'realtime stats' => 'مدير لوحة المتابعة اللحظية',
            'live updates' => 'مدير لوحة المتابعة اللحظية',
            'request_types' => 'مدير أنواع الطلبات',
            'request_workflows' => 'مدير مسارات الطلبات',
            'mail' => 'مدير بريد الطلبات',
            'central_treasury' => 'مدير الصندوق المركزي',
        ];
    }

    /**
     * استخراج "المورد" من اسم الصلاحية لتحديد الوحدة.
     */
    protected function getResourceFromPermissionName(string $name): ?string
    {
        $name = trim($name);
        if (str_contains($name, '.')) {
            return explode('.', $name)[0];
        }
        $parts = explode(' ', $name, 2);
        return $parts[1] ?? null;
    }

    public function run(): void
    {
        $resourceToRole = $this->getResourceToRoleArabic();
        $permissions = Permission::where('guard_name', $this->guard)->get();

        /** @var array<string, Role> أدوار تم إنشاؤها مسبقاً (اسم الدور => الموديل) */
        $rolesCache = [];

        foreach ($permissions as $permission) {
            $resource = $this->getResourceFromPermissionName($permission->name);
            if ($resource === null) {
                continue;
            }

            $roleNameAr = $resourceToRole[$resource] ?? null;
            if ($roleNameAr === null) {
                continue;
            }

            if (!isset($rolesCache[$roleNameAr])) {
                $rolesCache[$roleNameAr] = Role::firstOrCreate(
                    ['name' => $roleNameAr, 'guard_name' => $this->guard]
                );
            }

            $role = $rolesCache[$roleNameAr];
            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }

        // تقرير تحقق: عدد الصلاحيات لكل دور (كل دور يمتلك جميع صلاحيات كنترولره)
        $this->command->info('تم إنشاء أدوار الوحدات وربط الصلاحيات بنجاح. عدد الأدوار: ' . count($rolesCache));
        foreach ($rolesCache as $roleNameAr => $role) {
            $count = $role->permissions()->count();
            $this->command->line("  - {$roleNameAr}: {$count} صلاحية");
        }
    }
}
