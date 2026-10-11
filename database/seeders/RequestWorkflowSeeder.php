<?php

namespace Database\Seeders;

use App\Models\RequestType;
use App\Models\RequestWorkflow;
use App\Models\Department;
use App\Models\Section;
use Illuminate\Database\Seeder;

class RequestWorkflowSeeder extends Seeder
{
    public function run()
    {
        // Get or create request types - matching RequestTypeSeeder
        $requestTypes = [
            ['name' => 'ترخيص حرفة', 'description' => 'طلب ترخيص لممارسة حرفة معينة'],
            ['name' => 'ترخيص بناء', 'description' => 'طلب ترخيص للبناء أو التشييد'],
            ['name' => 'طلب آخر', 'description' => 'طلبات أخرى غير مصنفة'],
        ];

        foreach ($requestTypes as $type) {
            RequestType::firstOrCreate(['name' => $type['name']], $type);
        }

        // Create workflow steps for each request type
        $requestTypes = RequestType::all();

        foreach ($requestTypes as $requestType) {
            $workflows = [
                [
                    'request_type_id' => $requestType->id,
                    'name' => 'استلام ومراجعة الطلب',
                    'description' => 'استلام وتسجيل الطلب الجديد والمراجعة الأولية',
                    'step_order' => 1,
                    'is_active' => true,
                ],
                [
                    'request_type_id' => $requestType->id,
                    'name' => 'التدقيق الميداني',
                    'description' => 'فحص الموقع والتأكد من المطابقة للشروط',
                    'step_order' => 2,
                    'is_active' => true,
                ],
                [
                    'request_type_id' => $requestType->id,
                    'name' => 'الدراسة الفنية',
                    'description' => 'الدراسة الفنية للطلب والتقييم',
                    'step_order' => 3,
                    'is_active' => true,
                ],
                [
                    'request_type_id' => $requestType->id,
                    'name' => 'الموافقة المالية',
                    'description' => 'مراجعة الرسوم والموافقة المالية',
                    'step_order' => 4,
                    'is_active' => true,
                ],
                [
                    'request_type_id' => $requestType->id,
                    'name' => 'إصدار الترخيص',
                    'description' => 'إصدار الترخيص النهائي وإبلاغ صاحب الطلب',
                    'step_order' => 5,
                    'is_active' => true,
                ],
            ];

            foreach ($workflows as $workflow) {
                RequestWorkflow::firstOrCreate([
                    'request_type_id' => $workflow['request_type_id'],
                    'name' => $workflow['name']
                ], $workflow);
            }
        }

        // Assign sections to workflows - use available sections
        $workflows = RequestWorkflow::all();
        $sections = Section::all();

        if ($sections->isNotEmpty()) {
            foreach ($workflows as $workflow) {
                // Assign sections based on workflow step and request type
                $section = null;

                // For craft license (ترخيص حرفة), use craft-specific sections
                if ($workflow->requestType->name === 'ترخيص حرفة') {
                    $craftSections = $sections->filter(function ($section) {
                        return str_contains($section->name, 'حرف');
                    });
                    if ($craftSections->isNotEmpty()) {
                        $sectionIndex = ($workflow->step_order - 1) % $craftSections->count();
                        $section = $craftSections->values()->get($sectionIndex);
                    }
                }

                // For building license (ترخيص بناء), use building-specific sections
                elseif ($workflow->requestType->name === 'ترخيص بناء') {
                    $buildingSections = $sections->filter(function ($section) {
                        return str_contains($section->name, 'أبنية') || str_contains($section->name, 'تنظيم');
                    });
                    if ($buildingSections->isNotEmpty()) {
                        $sectionIndex = ($workflow->step_order - 1) % $buildingSections->count();
                        $section = $buildingSections->values()->get($sectionIndex);
                    }
                }

                // Fallback to any available section
                if (!$section) {
                    $sectionIndex = ($workflow->step_order - 1) % $sections->count();
                    $section = $sections->get($sectionIndex);
                }

                if ($section) {
                    $workflow->update([
                        'section_id' => $section->id,
                        'department_id' => $section->department_id,
                    ]);
                }
            }
        }

        $this->command->info('Request workflows seeded successfully!');
    }
}
