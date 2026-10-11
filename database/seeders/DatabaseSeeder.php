<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * ترتيب التنفيذ مهم! (التبعيات تُشغّل أولاً)
     *
     * @return void
     */
    public function run()
    {
        // 1. الصلاحيات الأساسية (بدون Employee أو User)
        $this->call(PermissionSeeder::class);

        // 2. صلاحيات الكنترولرات
        $this->call(ControllerPermissionsSeeder::class);

        // 3. أدوار الكنترولرات
        $this->call(ControllerRolesSeeder::class);

        // 4. الأدوار الأخرى (بعد إضافة جميع الصلاحيات)
        $this->call(RoleSeeder::class);
        $this->call(RolePermissionsSeeder::class);
        $this->call(ModelRolesSeeder::class);

        // 5. تأكد من أن admin لديه جميع الصلاحيات (بما فيها الجديدة)
        // يتم استدعاؤها بعد جميع الـ seeders الأخرى لضمان ربط جميع الصلاحيات
        $this->call([
            \Database\Seeders\AdminPermissionsSyncSeeder::class,
        ]);

        // 5. البيانات الأساسية (Departments, Sections, Branches, Jobs)
        $this->call(DepartmentSeeder::class);
        $this->call(SectionSeeder::class);
        $this->call(branchSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(JobSeeder::class);

        // 6. Employees (مهم لـ UserSeeder)
        $this->call(EmployeesSeeder::class);
        $this->call(VacantEmployeeSeeder::class);

        // 7. Users (بعد Employees بـ employee_id)
        $this->call(UserSeeder::class);

        // 8. البيانات الأخرى
        $this->call(ShiftsSeeder::class);
        $this->call(RequestStatusSeeder::class);
        $this->call(RequestTypeSeeder::class);
        $this->call(NoticeReasonSeeder::class);
        $this->call(RentLocationSeeder::class);

        // 9. موظفو المياومة (كرت عمل يومي) مع معاليهم وحساباتهم — من database/seeders/data/daily_employees.json
        $this->call(DailyEmployeesSeeder::class);

        // مسح cache الصلاحيات لضمان تحديثها
        \Illuminate\Support\Facades\Cache::forget('spatie.permission.cache');
        $this->command->info('🗑️ تم مسح cache الصلاحيات');

        // رسالة تأكيد
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('✅ تم إنشاء جميع البيانات بنجاح!');
        $this->command->info('═══════════════════════════════════════════');
    }
}
