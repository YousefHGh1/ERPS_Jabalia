<?php
namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Job;
use Illuminate\Database\Seeder;

class VacantEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobId = Job::query()->value('id');

        if ($jobId === null) {
            throw new \RuntimeException('لا يمكن إنشاء الموظف شاغر قبل إنشاء مسمى وظيفي واحد على الأقل.');
        }

        Employee::firstOrCreate(
            ['name' => 'شاغر'],
            [
                'czn_id'        => '000000000',
                'email'         => null,
                'gender'        => 'غير محدد',
                'status'        => 'Active',
                'address'       => '',
                'mobile'        => '0000000000',
                'birthdate'     => '1900-01-01',
                'birthplace'    => '',
                'salary'        => 0,
                'department_id' => null,
                'section_id'    => null,
                'branch_id'     => null,
                'job_id'        => $jobId,
            ]
        );
    }
}
