<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestStatus;
use Illuminate\Support\Facades\DB;

class RequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                'name' => 'قيد الانتظار',
                'color' => 'warning',
                'description' => 'الطلب قيد الانتظار أو قيد التنفيذ'
            ],
            [
                'name' => 'مكتمل',
                'color' => 'success',
                'description' => 'تم إكمال الطلب بنجاح'
            ],
            [
                'name' => 'مرفوض',
                'color' => 'danger',
                'description' => 'تم رفض الطلب'
            ],
            [
                'name' => 'ناقص',
                'color' => 'warning',
                'description' => 'الطلب يحتاج إلى معلومات إضافية'
            ]
        ];

        foreach ($statuses as $status) {
            RequestStatus::firstOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
