<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            // رئاسة البلدية (ID: 1)
            ['name' => 'مكتب رئيس البلدية', 'department_id' => 1],
            ['name' => 'قسم السكرتاريا والخدمات', 'department_id' => 1],
            ['name' => 'قسم لجان المجلس البلدي', 'department_id' => 1],
            ['name' => 'وحدة الرقابة الداخلية', 'department_id' => 1],
            ['name' => 'الوحدة القانونية', 'department_id' => 1],
            ['name' => 'وحدة العلاقات العامة والتعاون الدولي', 'department_id' => 1],
            ['name' => 'وحدة الحاسوب وتكنولوجيا المعلومات', 'department_id' => 1],
            ['name' => 'وحدة التخطيط الاستراتيجي والجودة', 'department_id' => 1],
            ['name' => 'وحدة الاستثمار', 'department_id' => 1],
            
            // مدير البلدية (ID: 2)
            ['name' => 'سكرتير مدير البلدية', 'department_id' => 2],
            ['name' => 'قسم السكرتاريا العامة', 'department_id' => 2],
            ['name' => 'قسم التفتيش والمتابعة', 'department_id' => 2],
            
            // دائرة المشاريع (ID: 3)
            ['name' => 'قسم إعداد وتصميم المشاريع', 'department_id' => 3],
            ['name' => 'قسم التنفيذ والإشراف', 'department_id' => 3],
            ['name' => 'قسم الأشغال العامة', 'department_id' => 3],
            ['name' => 'قسم الكهرباء والإنارة', 'department_id' => 3],
            
            // دائرة التنظيم والتخطيط (ID: 4)
            ['name' => 'قسم الأبنية والتنظيم', 'department_id' => 4],
            ['name' => 'قسم المساحة', 'department_id' => 4],
            ['name' => 'قسم الحرف والصناعات', 'department_id' => 4],
            ['name' => 'قسم التخطيط والتطوير', 'department_id' => 4],
            ['name' => 'قسم البيانات والـ GIS', 'department_id' => 4],
            
            // دائرة المياه والصرف الصحي (ID: 5)
            ['name' => 'قسم المياه', 'department_id' => 5],
            ['name' => 'قسم الصرف الصحي', 'department_id' => 5],
            ['name' => 'قسم التشغيل والصيانة', 'department_id' => 5],
            
            // دائرة الصحة والبيئة (ID: 6)
            ['name' => 'قسم النفايات الصلبة', 'department_id' => 6],
            ['name' => 'قسم البيئة والبستنة', 'department_id' => 6],
            ['name' => 'قسم الرقابة الصحية', 'department_id' => 6],
            ['name' => 'قسم الأسواق', 'department_id' => 6],
            ['name' => 'قسم الورش والآليات', 'department_id' => 6],
            
            // دائرة الشؤون المالية (ID: 7)
            ['name' => 'قسم الجباية العامة', 'department_id' => 7],
            ['name' => 'قسم المحاسبة', 'department_id' => 7],
            ['name' => 'قسم التدقيق المالي', 'department_id' => 7],
            ['name' => 'قسم اللوازم والمشتريات', 'department_id' => 7],
            ['name' => 'قسم الصندوق العام', 'department_id' => 7],
            
            // دائرة الشؤون الإدارية والموارد البشرية (ID: 8)
            ['name' => 'قسم الموارد البشرية', 'department_id' => 8],
            ['name' => 'قسم المستودعات', 'department_id' => 8],
            ['name' => 'قسم الأرشيف المركزي', 'department_id' => 8],
            ['name' => 'قسم خدمات البلدية', 'department_id' => 8],
            
            // وحدة قلم الجمهور (ID: 9)
            ['name' => 'قسم قلم الجمهور', 'department_id' => 9],
            ['name' => 'قسم خدمات المشتركين', 'department_id' => 9],
        ];

        foreach ($sections as $section) {
            Section::create($section);
        }
    }
}