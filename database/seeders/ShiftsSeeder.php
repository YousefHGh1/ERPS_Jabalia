<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $shifts = [
            [
                'name' => 'first Shift',
                'start_time' => '08:00:00',
                'end_time' => '14:00:00',
                'days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Sunday']),
                'start_late' => 15,
                'end_late' => 15,
                'start_shift' => '2023-01-01',
                'end_shift' => '2023-12-31',
            ],
            [
                'name' => 'second Shift',
                'start_time' => '8:00:00',
                'end_time' => '14:00:00',
                'days' => json_encode(['Saturday', 'Friday']),
                'start_late' => 15,
                'end_late' => 15,
                'start_shift' => '2023-01-01',
                'end_shift' => '2023-12-31',
            ],
        ];

        DB::table('shifts')->insert($shifts);
    }
}