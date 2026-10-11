<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'رئاسة البلدية'],
            ['name' => 'مدير البلدية'],
            ['name' => 'دائرة المشاريع'],
            ['name' => 'دائرة التنظيم والتخطيط'],
            ['name' => 'دائرة المياه والصرف الصحي'],
            ['name' => 'دائرة الصحة والبيئة'],
            ['name' => 'دائرة الشؤون المالية'],
            ['name' => 'دائرة الشؤون الإدارية والموارد البشرية'],
            ['name' => 'وحدة قلم الجمهور'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
