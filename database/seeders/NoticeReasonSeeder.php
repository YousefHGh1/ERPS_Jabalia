<?php

namespace Database\Seeders;

use App\Models\NoticeReason;
use Illuminate\Database\Seeder;

class NoticeReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (NoticeReason::count() > 0) {
            return;
        }

        $reasons = [
            [
                'name' => 'مخالفة شروط الترخيص',
                'description' => 'إخطار بسبب عدم الالتزام بشروط وأحكام الترخيص.',
            ],
            [
                'name' => 'مخالفة إشغال الطريق',
                'description' => 'إخطار بسبب إشغال الطريق أو الأرصفة بصورة غير قانونية.',
            ],
            [
                'name' => 'مخالفة النظافة العامة',
                'description' => 'إخطار بسبب عدم الالتزام بقواعد ومعايير النظافة العامة.',
            ],
            [
                'name' => 'مخالفة بناء',
                'description' => 'إخطار بسبب إنشاء أو تعديل بناء بدون ترخيص أو بالمخالفة للترخيص.',
            ],
            [
                'name' => 'مخالفة سداد الرسوم',
                'description' => 'إخطار بسبب التأخر أو عدم سداد الرسوم المستحقة.',
            ],
        ];

        foreach ($reasons as $reason) {
            NoticeReason::create($reason);
        }
    }
}

