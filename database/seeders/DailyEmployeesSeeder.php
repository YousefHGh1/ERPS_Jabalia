<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Employee;
use App\Models\EmployeeDependent;
use App\Models\EmployeeJobHistory;
use App\Models\EmployeeJobInfo;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * موظفو المياومة (كرت عمل يومي) مع بياناتهم الوظيفية ومعاليهم وحساباتهم.
 *
 * المصدر: database/seeders/data/daily_employees.json
 * (صُدِّر بعد استيراد «كشف الموظفين اليومي» وجلب المعالين من خدمة المواطن،
 *  فلا يحتاج هذا الـ seeder إلى خدمة المواطن.)
 *
 * آمن للتشغيل أكثر من مرة: لا يكرر موظفاً أو معالاً موجوداً.
 *
 *   php artisan db:seed --class=DailyEmployeesSeeder
 */
class DailyEmployeesSeeder extends Seeder
{
    private const DATA_FILE = 'seeders/data/daily_employees.json';

    private ?int $fallbackCategoryId = null;

    public function run(): void
    {
        $path = database_path(self::DATA_FILE);
        if (! file_exists($path)) {
            $this->command->error("الملف غير موجود: {$path}");

            return;
        }

        $rows = json_decode(file_get_contents($path), true);
        if (! is_array($rows)) {
            $this->command->error('تعذر قراءة ملف البيانات (JSON غير صالح).');

            return;
        }

        activity()->disableLogging();

        $created = 0;
        $existing = 0;
        $dependents = 0;
        $usersSkipped = [];

        foreach ($rows as $row) {
            DB::transaction(function () use ($row, &$created, &$existing, &$dependents, &$usersSkipped) {
                $employee = Employee::withTrashed()->where('czn_id', $row['czn_id'])->first();

                if ($employee) {
                    $existing++;
                } else {
                    $employee = Employee::create([
                        'czn_id' => $row['czn_id'],
                        'name' => $row['name'],
                        'gender' => $row['gender'],
                        'status' => $row['status'],
                        'address' => $row['address'],
                        'mobile' => $row['mobile'],
                        'birthdate' => $row['birthdate'],
                        'birthplace' => $row['birthplace'],
                        'salary' => $row['salary'],
                        'place_of_work' => $row['place_of_work'],
                        'job_id' => $this->jobId($row['job'], $row['job_category']),
                    ]);
                    $created++;
                }

                $this->seedJobInfo($employee, $row['job_info']);
                $dependents += $this->seedDependents($employee, $row['dependents']);
                $this->seedJobHistory($employee);

                if (! $this->seedUser($employee, $row['job_info']['emp_number'])) {
                    $usersSkipped[] = $row['name'];
                }
            });
        }

        $this->command->info("موظفو المياومة: {$created} جديد، {$existing} موجود مسبقاً، {$dependents} معال جديد.");

        if ($usersSkipped) {
            $this->command->warn('لم يُنشأ حساب لـ ' . count($usersSkipped) . ' موظف لأن رقمه الوظيفي مستخدم لحساب آخر: ' . implode('، ', $usersSkipped));
        }
    }

    private function jobId(string $name, ?string $categoryName): int
    {
        $job = Job::where('name', $name)->first();
        if ($job) {
            return $job->id;
        }

        $categoryId = Category::where('name', $categoryName)->value('id') ?? $this->fallbackCategoryId();

        return Job::create(['name' => $name, 'category_id' => $categoryId])->id;
    }

    private function fallbackCategoryId(): int
    {
        return $this->fallbackCategoryId ??= Category::where('name', 'فئة العمال')->value('id')
            ?? Category::where('name', 'فئة غير مصنفة')->value('id')
            ?? Category::firstOrFail()->id;
    }

    private function seedJobInfo(Employee $employee, array $info): void
    {
        if (EmployeeJobInfo::withTrashed()->where('employee_id', $employee->id)->exists()) {
            return;
        }

        // الرقم الوظيفي فريد — إن كان محجوزاً لموظف آخر لا نكرره
        if (EmployeeJobInfo::withTrashed()->where('emp_number', $info['emp_number'])->exists()) {
            $this->command->warn("الرقم الوظيفي {$info['emp_number']} مستخدم مسبقاً — تُرك {$employee->name} بلا بيانات وظيفية.");

            return;
        }

        EmployeeJobInfo::create(['employee_id' => $employee->id] + $info);
    }

    private function seedDependents(Employee $employee, array $dependents): int
    {
        $added = 0;

        foreach ($dependents as $dependent) {
            $exists = EmployeeDependent::withTrashed()
                ->where('employee_id', $employee->id)
                ->where('czn_id', $dependent['czn_id'])
                ->exists();

            if (! $exists) {
                EmployeeDependent::create(['employee_id' => $employee->id] + $dependent);
                $added++;
            }
        }

        return $added;
    }

    private function seedJobHistory(Employee $employee): void
    {
        if ($employee->employeeJobHistories()->exists()) {
            return;
        }

        $employee->load(['job.category', 'section', 'department', 'branch', 'EmployeeJobInfo']);
        $category = $employee->job?->category;

        EmployeeJobHistory::create([
            'type' => 'assigned',
            'status' => 'Active',
            'employee_id' => $employee->id,
            'job' => $employee->job?->name,
            'category' => $category?->name,
            'degree' => $category?->start_degree,
            'section' => $employee->section?->name,
            'department' => $employee->department?->name,
            'branch' => $employee->branch?->name,
            'bonus_percent' => '0',
            'date' => $employee->EmployeeJobInfo?->work_start_date ?? now()->format('Y-m-d'),
        ]);
    }

    /**
     * حساب الدخول: الرقم الوظيفي = كلمة المرور (نفس سياسة النظام للموظفين).
     */
    private function seedUser(Employee $employee, int $empNumber): bool
    {
        if (User::where('employee_id', $employee->id)->exists()) {
            return true;
        }

        if (User::where('employee_number', $empNumber)->exists()) {
            return false;
        }

        User::create([
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email ?? $empNumber . '@jabalia.gov.ps',
            'employee_number' => (string) $empNumber,
            'password' => Hash::make((string) $empNumber),
        ]);

        return true;
    }
}
