<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RequestType;

class RequestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'ترخيص حرفة',
                'description' => 'طلب ترخيص لممارسة حرفة معينة'
            ],
            [
                'name' => 'ترخيص بناء',
                'description' => 'طلب ترخيص للبناء أو التشييد'
            ],
            [
                'name' => 'طلب آخر',
                'description' => 'طلبات أخرى غير مصنفة'
            ]
        ];

        foreach ($types as $type) {
            RequestType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
