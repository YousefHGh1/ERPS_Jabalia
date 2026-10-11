<?php

/**
 * إعداد دور «موظف الشؤون القانونية» (legal_affairs_employee)
 * وفصل صلاحيات (الإخطارات / الحجوزات / أوامر الحبس) عن أدوار قلم الجمهور.
 *
 * سكربت آمن وقابل لإعادة التشغيل (idempotent) — لا يمسّ صلاحيات الأدوار
 * الأخرى إطلاقاً (يقتصر على: إنشاء الدور الجديد + إزالة الصلاحيات القانونية
 * من public_relations و republic_pen_manager + إسناد الدور للمستخدمين 151/199).
 *
 * الاستخدام من جذر المشروع:
 *   php scripts/setup_legal_affairs_role.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== إعداد دور موظف الشؤون القانونية ===\n\n";

try {
    // 1) صلاحيات الأقسام القانونية الثلاثة (الحجوزات / الإخطارات / أوامر الحبس)
    $legalPermissions = Permission::where(function ($query) {
        $query->where('name', 'like', '%reservation%')
            ->orWhere('name', 'like', '%notice%')
            ->orWhere('name', 'like', '%imprisonment%');
    })->get();

    if ($legalPermissions->isEmpty()) {
        echo "[ERROR] لم يتم العثور على صلاحيات الإخطارات/الحجوزات/أوامر الحبس.\n";
        echo "        شغّل أولاً: php artisan db:seed --class=PermissionSeeder\n";
        exit(1);
    }
    echo "[INFO] صلاحيات قانونية موجودة: " . $legalPermissions->count() . "\n";

    // 2) إنشاء الدور الجديد (إن لم يوجد) ومزامنة صلاحياته بالكامل
    $legalRole = Role::firstOrCreate(
        ['name' => 'legal_affairs_employee', 'guard_name' => 'web'],
        ['name' => 'legal_affairs_employee', 'guard_name' => 'web']
    );
    $legalRole->syncPermissions($legalPermissions);
    echo "[SUCCESS] الدور legal_affairs_employee يحمل " . $legalRole->permissions()->count() . " صلاحية\n";

    // 3) إزالة الصلاحيات القانونية من أدوار قلم الجمهور (بدون المساس ببقية صلاحياتها)
    foreach (['public_relations', 'republic_pen_manager'] as $roleName) {
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            echo "[INFO] الدور {$roleName} غير موجود — تم التخطي\n";
            continue;
        }
        foreach ($legalPermissions as $permission) {
            $role->revokePermissionTo($permission);
        }
        $remaining = $role->permissions()->whereIn('id', $legalPermissions->pluck('id'))->count();
        echo "[SUCCESS] {$roleName}: الصلاحيات القانونية المتبقية = {$remaining}\n";
    }

    // 4) إسناد الدور إلى المستخدمين (151: ميرفت سمارة، 199: محمد صبري)
    $users = User::whereIn('id', [151, 199])->get();
    if ($users->isEmpty()) {
        echo "[WARN] لم يتم العثور على المستخدمين 151/199 — تم تخطي الإسناد\n";
    }
    foreach ($users as $user) {
        $user->assignRole($legalRole);
        $has = $user->hasRole('legal_affairs_employee');
        echo "[SUCCESS] المستخدم #{$user->id} ({$user->name}): legal_affairs_employee = " . ($has ? 'نعم' : 'لا') . "\n";
    }

    // 5) تفريغ كاش الصلاحيات (Spatie)
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    echo "[SUCCESS] تم تفريغ كاش الصلاحيات\n";

    echo "\n=== اكتمل التنفيذ بنجاح ===\n";
    echo "خطوات يدوية متبقية على السيرفير:\n";
    echo "  php artisan cache:clear\n";
    echo "  php artisan config:clear\n";
    echo "  php artisan config:cache      (مطلوب — sidebar_permissions.php ملف إعدادات)\n";
    echo "  php artisan view:clear\n";
} catch (Throwable $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
