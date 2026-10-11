<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'             => 'الفئة الخاصة',
                'start_degree'     => 'درجة وزير',
                'end_degree'       => 'درجة وزير',
                'work_nature'      => 'مدراء دوائر',
                'included_degrees' => 'درجة وزير',
                'basic_salary'     => 4470,
                'degrees'          => [
                    ['degree' => 'درجة وزير', 'basic_salary' => 4470, 'order' => 1],
                ],
            ],
            [
                'name'             => 'الفئة العليا',
                'start_degree'     => 'A1',
                'end_degree'       => 'A',
                'work_nature'      => 'وظائف عليا',
                'included_degrees' => 'A1,A2,A3,A4,A',
                'basic_salary'     => 4020,
                'degrees'          => [
                    ['degree' => 'A1', 'basic_salary' => 4020, 'order' => 1],
                    ['degree' => 'A2', 'basic_salary' => 3720, 'order' => 2],
                    ['degree' => 'A3', 'basic_salary' => 3470, 'order' => 3],
                    ['degree' => 'A4', 'basic_salary' => 3220, 'order' => 4],
                    ['degree' => 'A', 'basic_salary' => 2970, 'order' => 5],
                ],
            ],
            [
                'name'             => 'الفئة الأولى',
                'start_degree'     => 'B',
                'end_degree'       => 'C',
                'work_nature'      => 'إشرافية',
                'included_degrees' => 'B,C',
                'basic_salary'     => 2720,
                'degrees'          => [
                    ['degree' => 'B', 'basic_salary' => 2720, 'order' => 1],
                    ['degree' => 'C', 'basic_salary' => 2470, 'order' => 2],
                ],
            ],
            [
                'name'             => 'الفئة الثانية',
                'start_degree'     => '1',
                'end_degree'       => '4',
                'work_nature'      => 'فنية / إدارية',
                'included_degrees' => '1,2,3,4',
                'basic_salary'     => 2220,
                'degrees'          => [
                    ['degree' => '1', 'basic_salary' => 2220, 'order' => 1],
                    ['degree' => '2', 'basic_salary' => 2080, 'order' => 2],
                    ['degree' => '3', 'basic_salary' => 1950, 'order' => 3],
                    ['degree' => '4', 'basic_salary' => 1820, 'order' => 4],
                ],
            ],
            [
                'name'             => 'الفئة الثالثة',
                'start_degree'     => '2',
                'end_degree'       => '6',
                'work_nature'      => 'حسب التصنيف',
                'included_degrees' => '2,3,4,5,6',
                'basic_salary'     => 2080,
                'degrees'          => [
                    ['degree' => '2', 'basic_salary' => 2080, 'order' => 1],
                    ['degree' => '3', 'basic_salary' => 1950, 'order' => 2],
                    ['degree' => '4', 'basic_salary' => 1820, 'order' => 3],
                    ['degree' => '5', 'basic_salary' => 1670, 'order' => 4],
                    ['degree' => '6', 'basic_salary' => 1570, 'order' => 5],
                ],
            ],
            [
                'name'             => 'الفئة الرابعة',
                'start_degree'     => '5',
                'end_degree'       => '8',
                'work_nature'      => 'حسب التصنيف',
                'included_degrees' => '5,6,7,8',
                'basic_salary'     => 1670,
                'degrees'          => [
                    ['degree' => '5', 'basic_salary' => 1670, 'order' => 1],
                    ['degree' => '6', 'basic_salary' => 1570, 'order' => 2],
                    ['degree' => '7', 'basic_salary' => 1470, 'order' => 3],
                    ['degree' => '8', 'basic_salary' => 1370, 'order' => 4],
                ],
            ],
            [
                'name'             => 'الفئة الخامسة',
                'start_degree'     => '6',
                'end_degree'       => '9',
                'work_nature'      => 'حسب التصنيف',
                'included_degrees' => '6,7,8,9',
                'basic_salary'     => 1570,
                'degrees'          => [
                    ['degree' => '6', 'basic_salary' => 1570, 'order' => 1],
                    ['degree' => '7', 'basic_salary' => 1470, 'order' => 2],
                    ['degree' => '8', 'basic_salary' => 1370, 'order' => 3],
                    ['degree' => '9', 'basic_salary' => 1300, 'order' => 4],
                ],
            ],
            [
                'name'             => 'فئة العمال',
                'start_degree'     => '10',
                'end_degree'       => '10',
                'work_nature'      => 'عمال',
                'included_degrees' => '10',
                'basic_salary'     => 1250,
                'degrees'          => [
                    ['degree' => '10', 'basic_salary' => 1250, 'order' => 1],
                ],
            ],
        ];

        foreach ($categories as $data) {
            $degrees = $data['degrees'];
            unset($data['degrees']);

            $category = Category::updateOrCreate([
                'name' => $data['name'],
            ], $data);

            $category->categoryDegrees()->delete();
            $category->categoryDegrees()->createMany($degrees);
        }
    }
}
