<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Seeder لإنشاء الصلاحيات الأساسية لكل وحدة (Controller/Module)
 * بما يتوافق مع ما هو مستخدم فعلياً داخل الكنترولرات.
 *
 * الصلاحيات تُنشأ بشكل idempotent (لن تتكرر عند إعادة التشغيل).
 */
class ControllerPermissionsSeeder extends Seeder
{
    protected string $guard = 'web';

    public function run(): void
    {
        $permissionsByResource = [
            // المكلفين
            'mokalafs' => [
                'index mokalafs',
                'store mokalafs',
                'update mokalafs',
                'delete mokalafs',
            ],

            // الطلبات
            'requests' => [
                'index requests',
                'store requests',
                'update requests',
                'delete requests',
            ],

            // تراخيص الحرف
            'craft_licenses' => [
                'index craft_licenses',
                'create craft_licenses',
                'edit craft_licenses',
                'delete craft_licenses',
                'print craft_licenses',
                'cancel_payment craft_licenses',
                'renew craft_licenses',
            ],

            // الحجوزات
            'reservations' => [
                'index reservations',
                'store reservations',
                'update reservations',
                'delete reservations',
            ],

            // الإخطارات
            'notices' => [
                'index notices',
                'store notices',
                'update notices',
                'delete notices',
            ],

            // أوامر الحبس
            'imprisonment_orders' => [
                'index imprisonment_orders',
                'store imprisonment_orders',
                'update imprisonment_orders',
                'delete imprisonment_orders',
            ],
        ];

        foreach ($permissionsByResource as $resource => $names) {
            foreach ($names as $name) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => $this->guard],
                    ['name' => $name, 'guard_name' => $this->guard]
                );
            }
        }
    }
}

