<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

/**
 * Role Seeder with Arabic Permissions
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 بدء إنشاء الأدوار...');

        // إنشاء الأدوار الأساسية
        $roles = [
            'admin' => 'مدير النظام',
            'super-admin' => 'مدير العليا',
            'super_user' => 'مستخدم متقدم',
            'employee' => 'موظف',
        ];

        foreach ($roles as $name => $displayName) {
            Role::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['name' => $name, 'guard_name' => 'web']
            );
            $this->command->line("   ✅ دور: {$name}");
        }

        // إنشاء أدوار الأقسام
        $sectionRoles = [
            // أدوار الشؤون الإدارية (بدون الإعدادات)
            'administrative_affairs_manager' => 'مدير الشؤون الإدارية',
            'categories_manager' => 'مدير التصنيفات',
            'employees_manager' => 'مدير الموظفين',
            'bonus_manager' => 'مدير المكافآت',
            'jobs_manager' => 'مدير الوظائف',
            'departments_manager' => 'مدير الأقسام',
            'holidays_manager' => 'مدير العطلات',
            'shift_manager' => 'مدير الورديات',
            'vehicle_manager' => 'مدير المركبات',
            'attendance_manager' => 'مدير الحضور والانصراف',

            // أدوار الصندوق المركزي
            'central_treasury_manager' => 'مدير الصندوق المركزي',

            // أدوار الحرف والصناعات
            'crafts_manager' => 'مدير الحرف والصناعات',
            'craft_licenses_manager' => 'مدير تراخيص الحرف',

            // أدوار الهندسة والتنظيم
            'engineering_manager' => 'مدير الهندسة والتنظيم',
            'engineering_files_manager' => 'مدير الملفات الهندسية',

            // أدوار الشؤون القانونية
            'legal_affairs_manager' => 'مدير الشؤون القانونية',
            'notices_manager' => 'مدير الإخطارات',
            'imprisonment_orders_manager' => 'مدير أوامر الحبس',
            'legal_affairs_employee' => 'موظف الشؤون القانونية',

            // أدوار قلم الجمهور
            'republic_pen_manager' => 'مدير قلم الجمهور',
            'public_relations' => 'قلم الجمهور',

            // أدوار التحصيل الخارجي
            'external_collection_manager' => 'مدير التحصيل الخارجي',
        ];

        foreach ($sectionRoles as $name => $displayName) {
            Role::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['name' => $name, 'guard_name' => 'web']
            );
            $this->command->line("   ✅ دور قسم: {$name}");
        }

        // ربط الأدوار بالصلاحيات
        $this->assignPermissionsToRoles();

        $this->command->info('✅ انتهى إنشاء الأدوار');
    }

    /**
     * ربط الصلاحيات بالأدوار
     */
    private function assignPermissionsToRoles(): void
    {
        $this->command->info('🔗 بدء ربط الصلاحيات بالأدوار...');

        // ربط admin و super-admin بجميع الصلاحيات
        $allPermissions = Permission::all();

        $admin = Role::findByName('admin', 'web');
        $admin->syncPermissions($allPermissions);
        $this->command->info("   🔑 admin حصل على {$allPermissions->count()} صلاحية");

        $superAdmin = Role::findByName('super-admin', 'web');
        $superAdmin->syncPermissions($allPermissions);
        $this->command->info("   🔑 super-admin حصل على {$allPermissions->count()} صلاحية");

        // employee - صلاحيات القراءة فقط
        $employee = Role::findByName('employee', 'web');
        $readPermissions = Permission::where('name', 'like', 'index %')->get();
        $employee->syncPermissions($readPermissions);
        $this->command->info("   🔑 employee حصل على {$readPermissions->count()} صلاحية عرض فقط");

        // أدوار الشؤون الإدارية
        $this->assignAdministrativeAffairsPermissions();

        // أدوار الصندوق المركزي
        $this->assignCentralTreasuryPermissions();

        // أدوار الحرف والصناعات
        $this->assignCraftsPermissions();

        // أدوار الهندسة والتنظيم
        $this->assignEngineeringPermissions();

        // أدوار الشؤون القانونية
        $this->assignLegalAffairsPermissions();

        // أدوار التحصيل الخارجي
        $this->assignExternalCollectionPermissions();

        // أدوار قلم الجمهور
        $this->assignRepublicPenPermissions();
        $this->assignPublicRelationsPermissions();

        $this->command->info('✅ انتهى ربط الصلاحيات بالأدوار');
    }

    /**
     * صلاحيات الشؤون الإدارية
     */
    private function assignAdministrativeAffairsPermissions(): void
    {
        // مدير الشؤون الإدارية - جميع صلاحيات الشؤون الإدارية (بدون الإعدادات)
        $adminAffairsManager = Role::findByName('administrative_affairs_manager', 'web');
        $adminAffairsPermissions = Permission::where('name', 'like', '%categories%')
            ->orWhere('name', 'like', '%employees%')
            ->orWhere('name', 'like', '%bonus%')
            ->orWhere('name', 'like', '%jobs%')
            ->orWhere('name', 'like', '%departments%')
            ->orWhere('name', 'like', '%sections%')
            ->orWhere('name', 'like', '%branches%')
            ->orWhere('name', 'like', '%holidays%')
            ->orWhere('name', 'like', '%shift%')
            ->orWhere('name', 'like', '%vehicle%')
            ->orWhere('name', 'like', '%zk%')
            ->orWhere('name', 'like', '%attendance%')
            ->get();
        $adminAffairsManager->syncPermissions($adminAffairsPermissions);
        $this->command->info("   🔑 مدير الشؤون الإدارية حصل على {$adminAffairsPermissions->count()} صلاحية");

        // أدوار فرعية لكل كونترول
        $this->assignControllerPermissions('categories_manager', 'categories');
        $this->assignControllerPermissions('employees_manager', 'employees');
        $this->assignControllerPermissions('bonus_manager', 'bonus');
        $this->assignControllerPermissions('jobs_manager', 'jobs');
        $this->assignControllerPermissions('departments_manager', 'departments');
        $this->assignControllerPermissions('holidays_manager', 'holidays');
        $this->assignControllerPermissions('shift_manager', 'shift');
        $this->assignControllerPermissions('vehicle_manager', 'vehicle');
        $this->assignControllerPermissions('attendance_manager', ['zk', 'attendance']);
    }

    /**
     * صلاحيات الصندوق المركزي
     */
    private function assignCentralTreasuryPermissions(): void
    {
        $centralTreasuryManager = Role::findByName('central_treasury_manager', 'web');
        $centralTreasuryPermissions = Permission::where('name', 'like', '%central_treasury%')
            ->orWhere('name', 'like', '%revenue%')
            ->orWhere('name', 'like', '%payment%')
            ->get();
        $centralTreasuryManager->syncPermissions($centralTreasuryPermissions);
        $this->command->info("   🔑 مدير الصندوق المركزي حصل على {$centralTreasuryPermissions->count()} صلاحية");
    }

    /**
     * صلاحيات الحرف والصناعات
     */
    private function assignCraftsPermissions(): void
    {
        // مدير الحرف والصناعات - جميع صلاحيات الحرف
        $craftsManager = Role::findByName('crafts_manager', 'web');
        $craftsPermissions = Permission::where('name', 'like', '%craft%')
            ->orWhere('name', 'like', '%mokalaf%')
            ->get();
        $craftsManager->syncPermissions($craftsPermissions);
        $this->command->info("   🔑 مدير الحرف والصناعات حصل على {$craftsPermissions->count()} صلاحية");

        // مدير تراخيص الحرف
        $this->assignControllerPermissions('craft_licenses_manager', 'craft_licenses');
    }

    /**
     * صلاحيات الهندسة والتنظيم
     */
    private function assignEngineeringPermissions(): void
    {
        // مدير الهندسة والتنظيم - جميع صلاحيات الهندسة
        $engineeringManager = Role::findByName('engineering_manager', 'web');
        $engineeringPermissions = Permission::where('name', 'like', '%engineering%')
            ->get();
        $engineeringManager->syncPermissions($engineeringPermissions);
        $this->command->info("   🔑 مدير الهندسة والتنظيم حصل على {$engineeringPermissions->count()} صلاحية");

        // مدير الملفات الهندسية
        $this->assignControllerPermissions('engineering_files_manager', 'engineering');
    }

    /**
     * صلاحيات الشؤون القانونية
     */
    private function assignLegalAffairsPermissions(): void
    {
        // مدير الشؤون القانونية - جميع صلاحيات الشؤون القانونية
        $legalAffairsManager = Role::findByName('legal_affairs_manager', 'web');
        $legalAffairsPermissions = Permission::where('name', 'like', '%notice%')
            ->orWhere('name', 'like', '%imprisonment%')
            ->get();
        $legalAffairsManager->syncPermissions($legalAffairsPermissions);
        $this->command->info("   🔑 مدير الشؤون القانونية حصل على {$legalAffairsPermissions->count()} صلاحية");

        // موظف الشؤون القانونية - الأقسام الثلاثة فقط (الإخطارات، الحجوزات، أوامر الحبس)
        // يُمنح لمن يحتاج هذه الأقسام بعد إزالتها من دور «قلم الجمهور»
        $legalAffairsEmployee = Role::findByName('legal_affairs_employee', 'web');
        $legalAffairsEmployeePermissions = Permission::where('name', 'like', '%notice%')
            ->orWhere('name', 'like', '%reservation%')
            ->orWhere('name', 'like', '%imprisonment%')
            ->get();
        $legalAffairsEmployee->syncPermissions($legalAffairsEmployeePermissions);
        $this->command->info("   🔑 موظف الشؤون القانونية حصل على {$legalAffairsEmployeePermissions->count()} صلاحية");

        // مدير الإخطارات
        $this->assignControllerPermissions('notices_manager', 'notices');

        // مدير أوامر الحبس
        $this->assignControllerPermissions('imprisonment_orders_manager', 'imprisonment_orders');
    }

    /**
     * صلاحيات التحصيل الخارجي
     */
    private function assignExternalCollectionPermissions(): void
    {
        $externalCollectionManager = Role::findByName('external_collection_manager', 'web');
        $externalCollectionPermissions = Permission::where('name', 'like', '%external_collections%')
            ->get();
        $externalCollectionManager->syncPermissions($externalCollectionPermissions);
        $this->command->info("   🔑 مدير التحصيل الخارجي حصل على {$externalCollectionPermissions->count()} صلاحية");
    }

    /**
     * صلاحيات قلم الجمهور (المكلفين والطلبات)
     *
     * مدير قلم الجمهور مسؤول عن المكلفين والطلبات فقط، ولا تُمنح له صلاحيات
     * أوامر الحبس/الحجوزات/الإخطارات (تلك تخص الشؤون القانونية ودور قلم الجمهور العام).
     */
    private function assignRepublicPenPermissions(): void
    {
        $republicPenManager = Role::findByName('republic_pen_manager', 'web');
        // صلاحيات المكلفين والطلبات (مع استبعاد أوامر الحبس والحجوزات والإخطارات)
        $republicPenPermissions = Permission::where('name', 'like', '%mokalaf%')
            ->orWhere('name', 'like', '%request%')
            ->orWhere('name', 'like', '%citizen%')
            ->get()
            ->reject(fn (Permission $permission) => preg_match('/notice|reservation|imprisonment/i', $permission->name));
        $republicPenManager->syncPermissions($republicPenPermissions);
        $this->command->info("   🔑 مدير قلم الجمهور حصل على {$republicPenPermissions->count()} صلاحية");
    }

    /**
     * صلاحيات دور قلم الجمهور (المكلفين والطلبات فقط)
     *
     * أُزيلت صلاحيات أوامر الحبس/الحجوزات/الإخطارات من هذا الدور،
     * وأُسندت إلى دور «legal_affairs_employee» (موظف الشؤون القانونية).
     */
    private function assignPublicRelationsPermissions(): void
    {
        $publicRelations = Role::findByName('public_relations', 'web');
        $publicRelationsPermissions = Permission::where('name', 'like', '%request%')
            ->orWhere('name', 'like', '%mokalaf%')
            ->get();
        $publicRelations->syncPermissions($publicRelationsPermissions);
        $this->command->info("   🔑 قلم الجمهور حصل على {$publicRelationsPermissions->count()} صلاحية (المكلفين والطلبات فقط)");
    }

    /**
     * ربط صلاحيات كونترول معين بدور
     */
    private function assignControllerPermissions(string $roleName, string|array $resource): void
    {
        $role = Role::findByName($roleName, 'web');
        $resources = is_array($resource) ? $resource : [$resource];

        $permissions = collect();
        foreach ($resources as $res) {
            $resourcePermissions = Permission::where('name', 'like', "%{$res}%")->get();
            $permissions = $permissions->merge($resourcePermissions);
        }

        $role->syncPermissions($permissions);
        $this->command->info("   🔑 {$roleName} حصل على {$permissions->count()} صلاحية");
    }
}