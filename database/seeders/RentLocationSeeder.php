<?php

namespace Database\Seeders;

use App\Models\RentLocation;
use Illuminate\Database\Seeder;

class RentLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (RentLocation::count() > 0) {
            return;
        }

        $locations = [
            [
                'name' => 'سوق البلد',
                'address' => 'وسط المدينة',
                'description' => 'محلات وبسطات داخل سوق البلد الرئيسي.',
            ],
            [
                'name' => 'سوق المخيم',
                'address' => 'منطقة المخيم',
                'description' => 'محلات وبسطات داخل منطقة المخيم.',
            ],
            [
                'name' => 'بسطات الشارع العام',
                'address' => 'الشارع الرئيسي',
                'description' => 'بسطات تجارية على الشارع العام.',
            ],
            [
                'name' => 'محلات تجارية',
                'address' => 'مناطق متفرقة',
                'description' => 'محلات تجارية مرخصة في مناطق مختلفة.',
            ],
            [
                'name' => 'موقع إيجار آخر',
                'address' => null,
                'description' => 'مواقع إيجار أخرى متنوعة.',
            ],
        ];

        foreach ($locations as $location) {
            RentLocation::create($location);
        }
    }
}

