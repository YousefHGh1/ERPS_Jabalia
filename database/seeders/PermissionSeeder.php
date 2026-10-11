<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Permissions Seeder
 * 
 * Seeds all permissions matching sidebar config (English format: action resource)
 * and creates default roles with appropriate permissions.
 */
class PermissionSeeder extends Seeder
{
    /**
     * Permission groups in English format (matching sidebar_permissions.php)
     * Format: [action, resource] -> creates "action resource"
     */
    protected const PERMISSIONS = [
        // التصنيفات
        ['index', 'categories'],
        ['store', 'categories'],
        ['update', 'categories'],
        ['delete', 'categories'],

        // المكافآت
        ['index', 'bonus'],
        ['store', 'bonus'],
        ['update', 'bonus'],
        ['delete', 'bonus'],

        // الوظائف
        ['index', 'jobs'],
        ['store', 'jobs'],
        ['update', 'jobs'],
        ['delete', 'jobs'],

        // الأقسام
        ['index', 'departments'],
        ['store', 'departments'],
        ['update', 'departments'],
        ['delete', 'departments'],

        // الشعب
        ['index', 'sections'],
        ['store', 'sections'],
        ['update', 'sections'],
        ['delete', 'sections'],

        // الفروع
        ['index', 'branches'],
        ['store', 'branches'],
        ['update', 'branches'],
        ['delete', 'branches'],

        // الموظفين
        ['index', 'employees'],
        ['store', 'employees'],
        ['update', 'employees'],
        ['delete', 'employees'],

        // مدير القسم
        ['index', 'departmentdirector'],
        ['store', 'departmentdirector'],
        ['update', 'departmentdirector'],
        ['delete', 'departmentdirector'],

        // مدير الشعبة
        ['index', 'sectiondirector'],
        ['store', 'sectiondirector'],
        ['update', 'sectiondirector'],
        ['delete', 'sectiondirector'],

        // مدير الفرع
        ['index', 'branchdirector'],
        ['store', 'branchdirector'],
        ['update', 'branchdirector'],
        ['delete', 'branchdirector'],

        // العطلات
        ['index', 'holidays'],
        ['store', 'holidays'],
        ['update', 'holidays'],
        ['delete', 'holidays'],

        // العطلة الرئيسية
        ['index', 'main_holiday'],
        ['store', 'main_holiday'],
        ['update', 'main_holiday'],
        ['delete', 'main_holiday'],

        // إجازات الموظفين
        ['index', 'employee_holiday'],
        ['store', 'employee_holiday'],
        ['update', 'employee_holiday'],
        ['delete', 'employee_holiday'],

        // صلاحيات الموظفين
        ['index', 'EmployeePermissions'],
        ['store', 'EmployeePermissions'],
        ['update', 'EmployeePermissions'],
        ['delete', 'EmployeePermissions'],

        // الورديات
        ['index', 'Shift'],
        ['store', 'Shift'],
        ['update', 'Shift'],
        ['delete', 'Shift'],

        // الحضور والانصراف
        ['index', 'zk'],
        ['store', 'zk'],
        ['update', 'zk'],
        ['delete', 'zk'],

        // المركبات
        ['index', 'vehicle'],
        ['store', 'vehicle'],
        ['update', 'vehicle'],
        ['delete', 'vehicle'],

        // إدارة الصلاحيات
        ['index', 'permissions'],
        ['update', 'permissions'],
        ['delete', 'permissions'],

        // الأدوار
        ['index', 'roles'],
        ['update', 'roles'],
        ['delete', 'roles'],

        // المستخدمين
        ['index', 'users'],
        ['update', 'users'],
        ['delete', 'users'],

        // المكلفين
        ['index', 'mokalafs'],
        ['store', 'mokalafs'],
        ['update', 'mokalafs'],
        ['delete', 'mokalafs'],

        // أنواع الطلبات
        ['index', 'request_types'],
        ['store', 'request_types'],
        ['update', 'request_types'],
        ['delete', 'request_types'],

        // الطلبات
        ['index', 'requests'],
        ['create', 'requests'],
        ['store', 'requests'],
        ['update', 'requests'],
        ['delete', 'requests'],
        ['show', 'requests'],

        // تراخيص الحرف
        ['index', 'craft_licenses'],
        ['store', 'craft_licenses'],
        ['update', 'craft_licenses'],
        ['delete', 'craft_licenses'],

        // الملفات الهندسية
        ['index', 'engineerings'],
        ['store', 'engineerings'],
        ['update', 'engineerings'],
        ['delete', 'engineerings'],

        // الملفات الهندسية القديمة
        ['index', 'legacy_engineerings'],
        ['store', 'legacy_engineerings'],
        ['update', 'legacy_engineerings'],
        ['delete', 'legacy_engineerings'],

        // الحجوزات
        ['index', 'reservations'],
        ['store', 'reservations'],
        ['update', 'reservations'],
        ['delete', 'reservations'],

        // الإخطارات
        ['index', 'notices'],
        ['store', 'notices'],
        ['update', 'notices'],
        ['delete', 'notices'],

        // أوامر الحبس
        ['index', 'imprisonment_orders'],
        ['store', 'imprisonment_orders'],
        ['update', 'imprisonment_orders'],
        ['delete', 'imprisonment_orders'],

        // الصندوق المركزي
        ['view', 'central_treasury'],
        ['manage', 'central_treasury'],

        // التحصيل الخارجي
        ['view', 'external_collections'],
        ['create', 'external_collections'],
        ['edit', 'external_collections'],
        ['delete', 'external_collections'],
        ['payment', 'external_collections'],

        // صلاحية تقارير الموظفين
        ['index', 'employee_reports'],

        // مكافآت الموظفين
        ['index', 'employee_bonus'],
        ['store', 'employee_bonus'],
        ['update', 'employee_bonus'],
        ['delete', 'employee_bonus'],

        // معالين الموظفين
        ['index', 'employee_dependent'],
        ['store', 'employee_dependent'],
        ['update', 'employee_dependent'],
        ['delete', 'employee_dependent'],

        // سجل الوظائف
        ['index', 'job_history'],
        ['store', 'job_history'],
        ['update', 'job_history'],
        ['delete', 'job_history'],

        // معلومات الوظيفة
        ['index', 'job_info'],
        ['store', 'job_info'],
        ['update', 'job_info'],
        ['delete', 'job_info'],

        // المرفقات
        ['index', 'attachment'],
        ['store', 'attachment'],
        ['update', 'attachment'],
        ['delete', 'attachment'],

        // المستندات
        ['index', 'documents'],
        ['store', 'documents'],
        ['update', 'documents'],
        ['delete', 'documents'],

        // مسارات الطلبات
        ['index', 'request_workflows'],
        ['store', 'request_workflows'],
        ['update', 'request_workflows'],
        ['delete', 'request_workflows'],

        // تراخيص الحرف - صلاحيات إضافية
        ['cancel_payment', 'craft_licenses'],
        ['edit', 'craft_licenses'],
        ['print', 'craft_licenses'],
        ['renew', 'craft_licenses'],

        // مراقبة الأداء
        ['monitor', 'performance'],

        // لوحة التحكم التفاعلية (الوقت الفعلي)
        ['index', 'real_time_dashboards'],
        ['view', 'realtime_stats'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 بدء إنشاء الصلاحيات والأدوار...');

        // إنشاء الصلاحيات
        $createdCount = 0;
        foreach (self::PERMISSIONS as $perm) {
            if (is_array($perm)) {
                $name = "{$perm[0]} {$perm[1]}";
            } else {
                $name = $perm;
            }

            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['name' => $name, 'guard_name' => 'web']
            );
            $createdCount++;
        }

        $this->command->info("✅ تم إنشاء {$createdCount} صلاحية");

        // إنشاء الأدوار الأساسية
        $this->createDefaultRoles();

        // ربط الأدوار بصلاحياتها
        $this->assignPermissionsToRoles();

        $this->command->info('✅ انتهى إنشاء الصلاحيات والأدوار');
    }

    /**
     * Create default roles
     */
    protected function createDefaultRoles(): void
    {
        $roles = [
            'admin' => 'مدير النظام',
            'super-admin' => 'مدير العليا',
            'super_user' => 'مستخدم متقدم',
            'employee' => 'موظف',

            // أدوار الأقسام
            'departments_manager' => 'مدير الأقسام',
            'employees_manager' => 'مدير الموظفين',
            'holidays_manager' => 'مدير العطلات',
            'attendance_manager' => 'مدير الحضور',

            // أدوار الوحدات
            'mokalafs_manager' => 'مدير المكلفين',
            'requests_manager' => 'مدير الطلبات',
            'craft_licenses_manager' => 'مدير تراخيص الحرف',
            'engineerings_manager' => 'مدير الملفات الهندسية',
            'legal_manager' => 'مدير الشؤون القانونية',

            // أدوار مالية
            'treasury_manager' => 'مدير الصندوق',
            'external_collections_manager' => 'مدير التحصيل الخارجي',

            // أدوار مجمعة جديدة
            'all_sections' => 'جميع الأقسام',
            'administrative_affairs' => 'الشؤون الإدارية',
            'central_treasury_full' => 'الصندوق المركزي',
            'public_relations' => 'قلم الجمهور',
            'crafts_full' => 'الحرف',
            'engineering_full' => 'الهندسة',
            'legal_affairs' => 'الشؤون القانونية',
            'legal_affairs_employee' => 'موظف الشؤون القانونية',
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['name' => $name, 'guard_name' => 'web']
            );
        }

        $this->command->info('✅ تم إنشاء الأدوار الأساسية');
    }

    /**
     * Assign permissions to roles
     */
    protected function assignPermissionsToRoles(): void
    {
        // admin - جميع الصلاحيات (بما فيها صلاحيات الكنترولرات الجديدة)
        $admin = Role::findByName('admin', 'web');
        $admin->syncPermissions(Permission::all());

        // super-admin - جميع الصلاحيات
        $superAdmin = Role::findByName('super-admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        // employee - صلاحيات index فقط
        $employee = Role::findByName('employee', 'web');
        $indexPermissions = Permission::where('name', 'like', 'index %')->get();
        $employee->syncPermissions($indexPermissions);

        // all_sections - جميع الصلاحيات
        $allSections = Role::findByName('all_sections', 'web');
        $allSections->syncPermissions(Permission::all());

        // administrative_affairs - الشؤون الإدارية (التصنيفات، المكافآت، الوظائف، الهيكل، الموظفين، العطلات، صلاحيات الموظفين، الورديات، الحضور، المركبات)
        $adminAffairs = Role::findByName('administrative_affairs', 'web');
        $adminAffairsPermissions = Permission::where(function ($query) {
            $query->where('name', 'like', 'index categories')
                ->orWhere('name', 'like', 'store categories')
                ->orWhere('name', 'like', 'update categories')
                ->orWhere('name', 'like', 'delete categories')
                ->orWhere('name', 'like', 'index bonus')
                ->orWhere('name', 'like', 'store bonus')
                ->orWhere('name', 'like', 'update bonus')
                ->orWhere('name', 'like', 'delete bonus')
                ->orWhere('name', 'like', 'index jobs')
                ->orWhere('name', 'like', 'store jobs')
                ->orWhere('name', 'like', 'update jobs')
                ->orWhere('name', 'like', 'delete jobs')
                ->orWhere('name', 'like', 'index departments')
                ->orWhere('name', 'like', 'store departments')
                ->orWhere('name', 'like', 'update departments')
                ->orWhere('name', 'like', 'delete departments')
                ->orWhere('name', 'like', 'index sections')
                ->orWhere('name', 'like', 'store sections')
                ->orWhere('name', 'like', 'update sections')
                ->orWhere('name', 'like', 'delete sections')
                ->orWhere('name', 'like', 'index branches')
                ->orWhere('name', 'like', 'store branches')
                ->orWhere('name', 'like', 'update branches')
                ->orWhere('name', 'like', 'delete branches')
                ->orWhere('name', 'like', 'index employees')
                ->orWhere('name', 'like', 'store employees')
                ->orWhere('name', 'like', 'update employees')
                ->orWhere('name', 'like', 'delete employees')
                ->orWhere('name', 'like', 'index departmentdirector')
                ->orWhere('name', 'like', 'store departmentdirector')
                ->orWhere('name', 'like', 'update departmentdirector')
                ->orWhere('name', 'like', 'delete departmentdirector')
                ->orWhere('name', 'like', 'index sectiondirector')
                ->orWhere('name', 'like', 'store sectiondirector')
                ->orWhere('name', 'like', 'update sectiondirector')
                ->orWhere('name', 'like', 'delete sectiondirector')
                ->orWhere('name', 'like', 'index branchdirector')
                ->orWhere('name', 'like', 'store branchdirector')
                ->orWhere('name', 'like', 'update branchdirector')
                ->orWhere('name', 'like', 'delete branchdirector')
                ->orWhere('name', 'like', 'index holidays')
                ->orWhere('name', 'like', 'store holidays')
                ->orWhere('name', 'like', 'update holidays')
                ->orWhere('name', 'like', 'delete holidays')
                ->orWhere('name', 'like', 'index main_holiday')
                ->orWhere('name', 'like', 'store main_holiday')
                ->orWhere('name', 'like', 'update main_holiday')
                ->orWhere('name', 'like', 'delete main_holiday')
                ->orWhere('name', 'like', 'index employee_holiday')
                ->orWhere('name', 'like', 'store employee_holiday')
                ->orWhere('name', 'like', 'update employee_holiday')
                ->orWhere('name', 'like', 'delete employee_holiday')
                ->orWhere('name', 'like', 'index EmployeePermissions')
                ->orWhere('name', 'like', 'store EmployeePermissions')
                ->orWhere('name', 'like', 'update EmployeePermissions')
                ->orWhere('name', 'like', 'delete EmployeePermissions')
                ->orWhere('name', 'like', 'index Shift')
                ->orWhere('name', 'like', 'store Shift')
                ->orWhere('name', 'like', 'update Shift')
                ->orWhere('name', 'like', 'delete Shift')
                ->orWhere('name', 'like', 'index zk')
                ->orWhere('name', 'like', 'store zk')
                ->orWhere('name', 'like', 'update zk')
                ->orWhere('name', 'like', 'delete zk')
                ->orWhere('name', 'like', 'index vehicle')
                ->orWhere('name', 'like', 'store vehicle')
                ->orWhere('name', 'like', 'update vehicle')
                ->orWhere('name', 'like', 'delete vehicle')
                ->orWhere('name', 'like', 'index employee_reports')
                ->orWhere('name', 'like', 'index employee_bonus')
                ->orWhere('name', 'like', 'store employee_bonus')
                ->orWhere('name', 'like', 'update employee_bonus')
                ->orWhere('name', 'like', 'delete employee_bonus')
                ->orWhere('name', 'like', 'index employee_dependent')
                ->orWhere('name', 'like', 'store employee_dependent')
                ->orWhere('name', 'like', 'update employee_dependent')
                ->orWhere('name', 'like', 'delete employee_dependent')
                ->orWhere('name', 'like', 'index job_history')
                ->orWhere('name', 'like', 'store job_history')
                ->orWhere('name', 'like', 'update job_history')
                ->orWhere('name', 'like', 'delete job_history')
                ->orWhere('name', 'like', 'index job_info')
                ->orWhere('name', 'like', 'store job_info')
                ->orWhere('name', 'like', 'update job_info')
                ->orWhere('name', 'like', 'delete job_info')
                ->orWhere('name', 'like', 'index attachment')
                ->orWhere('name', 'like', 'store attachment')
                ->orWhere('name', 'like', 'update attachment')
                ->orWhere('name', 'like', 'delete attachment')
                ->orWhere('name', 'like', 'index documents')
                ->orWhere('name', 'like', 'store documents')
                ->orWhere('name', 'like', 'update documents')
                ->orWhere('name', 'like', 'delete documents');
        })->get();
        $adminAffairs->syncPermissions($adminAffairsPermissions);

        // central_treasury_full - الصندوق المركزي بجميع أقسامه
        $centralTreasury = Role::findByName('central_treasury_full', 'web');
        $centralTreasuryPermissions = Permission::where('name', 'like', '%central_treasury%')
            ->orWhere('name', 'like', '%payment_receipt%')
            ->orWhere('name', 'like', '%citizen_invoice%')
            ->get();
        $centralTreasury->syncPermissions($centralTreasuryPermissions);

        // public_relations - قلم الجمهور (المكلفين والطلبات فقط)
        // أُزيلت صلاحيات الحجوزات والإخطارات وأوامر الحبس → دور legal_affairs_employee
        $publicRelations = Role::findByName('public_relations', 'web');

        $publicRelationsPermissions = Permission::where('name', 'like', '%request%')
            ->orWhere('name', 'like', '%mokalaf%')
            ->get();
        $publicRelations->syncPermissions($publicRelationsPermissions);
        $this->command->info("   🔑 public_relations حصل على {$publicRelationsPermissions->count()} صلاحية (المكلفين والطلبات فقط)");

        // crafts_full - الحرف بجميع أقسامه
        $craftsFull = Role::findByName('crafts_full', 'web');
        $craftsFullPermissions = Permission::where('name', 'like', '%craft_license%')
            ->orWhere('name', 'like', '%craft%')
            ->get();
        $craftsFull->syncPermissions($craftsFullPermissions);

        // engineering_full - الهندسة بجميع أقسامه
        $engineeringFull = Role::findByName('engineering_full', 'web');
        $engineeringFullPermissions = Permission::where('name', 'like', '%engineering%')
            ->orWhere('name', 'like', '%legacy_engineering%')
            ->get();
        $engineeringFull->syncPermissions($engineeringFullPermissions);

        // legal_affairs - الشؤون القانونية بجميع أقسامه
        $legalAffairs = Role::findByName('legal_affairs', 'web');
        $legalAffairsPermissions = Permission::where('name', 'like', '%notice%')
            ->orWhere('name', 'like', '%imprisonment%')
            ->orWhere('name', 'like', '%external_collection%')
            ->get();
        $legalAffairs->syncPermissions($legalAffairsPermissions);

        // legal_affairs_employee - موظف الشؤون القانونية (الإخطارات، الحجوزات، أوامر الحبس)
        $legalAffairsEmployee = Role::findByName('legal_affairs_employee', 'web');
        $legalAffairsEmployeePermissions = Permission::where('name', 'like', '%notice%')
            ->orWhere('name', 'like', '%reservation%')
            ->orWhere('name', 'like', '%imprisonment%')
            ->get();
        $legalAffairsEmployee->syncPermissions($legalAffairsEmployeePermissions);
    }

    /**
     * Sync Admin role with ALL permissions (including controller-generated)
     * Call this after running sync-controllers command
     */
    public static function syncAdminWithAllPermissions(): void
    {
        $admin = Role::findByName('admin', 'web');
        if ($admin) {
            $admin->syncPermissions(Permission::all());
        }
    }
}