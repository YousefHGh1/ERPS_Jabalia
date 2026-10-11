<?php

namespace Database\Seeders;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Employee::create([
            'czn_id' => 2301,
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'gender' => 'ذكر',
            'status' => 'Active',
            'address' => 'admin Address ',
            'mobile' => 000000000,
            'birthdate' => Carbon::now(),
            'birthplace' => 'admin Birthplace',
            'salary' => 0,
            'department_id' => 1,
            'section_id' => 1,
            'branch_id' => 1,
            'job_id' => 1,
        ]);

    }
}