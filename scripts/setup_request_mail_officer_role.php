<?php

/**
 * إعداد دور «موظف متابعة بريد الطلبات» (request_mail_officer)
 * للرد على طلبات المسارات ومتابعة بريد الطلبات فقط.
 *
 * دور ضيّق لا يمنح إدارة المكلفين أو أنواع الطلبات أو مساراتها:
 * - index requests: إظهار قائمتي «الطلبات» و«Mail» في الشريط الجانبي
 *   (عبر Gate: sidebar_can_see_requests_menu و sidebar_can_see_request_mail).
 * - show requests: عرض تفاصيل الطلب من صفحة بريد الطلبات.
 * - show/store/update request_mails: عرض صفحة الرد وتسجيل الرد.
 * - index mokalafs: الاستعلام المرجعي للمكلف (بدون تعديل بياناته).
 * - index request_types: العرض المرجعي لأنواع الطلبات فقط.
 *
 * ملاحظة تشغيلية: الرد الفعلي يعتمد على ربط الموظف بقسم/دائرة
 * (انظر StoreRequestMailRequest::authorize و RequestMailController
 * و resources/views/requests/mail/status.blade.php)، لذا بعد إسناد
 * الدور يجب ربط المستخدم بقسم «قسم قلم الجمهور».
 *
 * سكربت آمن وقابل لإعادة التشغيل (idempotent) — يقتصر على إنشاء
 * الدور ومزامنة صلاحياته الضيقة + تفريغ كاش الصلاحيات.
 * الإسناد لمستخدمين يتم يدوياً من شاشة إدارة المستخدمين.
 *
 * الاستخدام من جذر المشروع:
 *   php scripts/setup_request_mail_officer_role.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Section;
use Illuminate\Contracts\Console\Kernel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== إعداد دور موظف متابعة بريد الطلبات ===\n\n";

try {
    // 1) الصلاحيات الضيقة للدور (رد ومتابعة فقط)
    $permissionNames = [
        'index requests',
        'show requests',
        'show request_mails',
        'store request_mails',
        'update request_mails',
        'index mokalafs',
        'index request_types',
    ];

    $officerPermissions = Permission::whereIn('name', $permissionNames)->get();

    $missing = array_diff($permissionNames, $officerPermissions->pluck('name')->all());
    if (! empty($missing)) {
        echo '[ERROR] صلاحيات مفقودة في قاعدة البيانات: ' . implode(', ', $missing) . "\n";
        echo "        شغّل أولاً: php artisan db:seed --class=PermissionSeeder\n";
        echo "        ثم: php artisan permissions:sync-controllers\n";
        exit(1);
    }
    echo '[INFO] صلاحيات الدور موجودة: ' . $officerPermissions->count() . "\n";

    // 2) إنشاء الدور (إن لم يوجد) ومزامنة صلاحياته بالكامل
    $officerRole = Role::firstOrCreate(
        ['name' => 'request_mail_officer', 'guard_name' => 'web'],
        ['name' => 'request_mail_officer', 'guard_name' => 'web']
    );
    $officerRole->syncPermissions($officerPermissions);
    echo '[SUCCESS] الدور request_mail_officer يحمل ' . $officerRole->permissions()->count() . " صلاحية\n";

    // 3) التأكد من وجود قسم «قسم قلم الجمهور» (مطلوب لربط الموظف)
    $section = Section::where('name', 'قسم قلم الجمهور')->first();
    if (! $section) {
        echo "[WARN] قسم «قسم قلم الجمهور» غير موجود — أنشئه من الشؤون الإدارية قبل ربط الموظفين\n";
    } else {
        echo "[INFO] قسم قلم الجمهور موجود (sections.id = {$section->id}) — اربط الموظف به من شاشة الموظفين\n";
    }

    // 4) تفريغ كاش الصلاحيات (Spatie)
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    echo "[SUCCESS] تم تفريغ كاش الصلاحيات\n";

    echo "\n=== اكتمل التنفيذ بنجاح ===\n";
    echo "خطوات يدوية متبقية على السيرفر:\n";
    echo "  1) إسناد الدور للمستخدم من: /permissions/users (دور: موظف متابعة بريد الطلبات)\n";
    echo "  2) ربط الموظف بقسم «قسم قلم الجمهور» من شاشة الموظفين\n";
    echo "  3) php artisan sidebar:clear-cache\n";
    echo "  4) php artisan cache:clear\n";
    echo "  5) php artisan config:clear\n";
    echo "  6) php artisan config:cache      (مطلوب — sidebar_permissions.php ملف إعدادات)\n";
    echo "  7) php artisan view:clear\n";
} catch (Throwable $e) {
    echo '[ERROR] ' . $e->getMessage() . "\n";
    exit(1);
}
