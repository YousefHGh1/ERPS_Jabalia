<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Job;
use App\Models\Section;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class EmployeeImportService
{
    private ?int $defaultCategoryId = null;

    private const FIELD_MAP = [
        'اسم الموظف' => 'name',
        'هوية' => 'czn_id',
        'الدائرة' => 'department_name',
        'القسم' => 'section_name',
        'الشعبة' => 'branch_name',
        'المسمى الوظيفي المعتمد' => 'job_name',
        'المسمى الوظيفي' => 'job_name',
        'تاريخ الميلاد' => 'birthdate',
        'جنس' => 'gender',
        'الجنس' => 'gender',
        'رقم الهوية' => 'czn_id',
        'رقم الجوال' => 'mobile',
        'رقم الهاتف' => 'mobile',
        'البريد الإلكتروني' => 'email',
        'العنوان' => 'address',
        'مكان الولادة' => 'birthplace',
        'الراتب' => 'salary',
        'الحالة' => 'status',
        'الاسم' => 'name',
    ];

    public function parseFile(string $path, ?string $extension = null): array
    {
        $extension = strtolower($extension ?? pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            return $this->parseSpreadsheet($path);
        }

        return $this->parseCsv($path);
    }

    public function importFromFile(string $path, ?string $extension = null): array
    {
        activity()->disableLogging();

        $rows = $this->parseFile($path, $extension);

        if (empty($rows)) {
            throw new \RuntimeException('لم يتم العثور على بيانات صالحة في الملف');
        }

        return $this->importRows($rows);
    }

    public function importRows(array $rows): array
    {
        activity()->disableLogging();

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if (empty($row['czn_id']) || empty($row['name'])) {
                continue;
            }

            if (Employee::where('czn_id', $row['czn_id'])->exists()) {
                $skipped++;
                continue;
            }

            try {
                $this->createEmployee($row);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = 'صف ' . ($index + 2) . " ({$row['name']}): {$e->getMessage()}";
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    private function parseCsv(string $path): array
    {
        $data = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException('تعذر فتح الملف');
        }

        $headers = $this->normalizeHeaders(fgetcsv($handle) ?: []);

        while (($row = fgetcsv($handle)) !== false) {
            $employeeData = $this->mapRow($headers, $row);
            if (!empty($employeeData['czn_id']) && !empty($employeeData['name'])) {
                $data[] = $this->normalizeEmployeeData($employeeData);
            }
        }

        fclose($handle);

        return $data;
    }

    private function parseSpreadsheet(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        if (empty($rows)) {
            return [];
        }

        $headers = $this->normalizeHeaders(array_shift($rows));
        $data = [];

        foreach ($rows as $row) {
            $employeeData = $this->mapRow($headers, $row);
            if (!empty($employeeData['czn_id']) && !empty($employeeData['name'])) {
                $data[] = $this->normalizeEmployeeData($employeeData);
            }
        }

        return $data;
    }

    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $header = trim((string) $header);
            $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);
            $header = preg_replace('/\s*\*$/', '', $header);

            return $header;
        }, $headers);
    }

    private function mapRow(array $headers, array $row): array
    {
        $employeeData = [];

        foreach ($headers as $index => $header) {
            $fieldName = self::FIELD_MAP[$header] ?? null;
            if ($fieldName && array_key_exists($index, $row) && $row[$index] !== null && $row[$index] !== '') {
                $employeeData[$fieldName] = trim((string) $row[$index]);
            }
        }

        return $employeeData;
    }

    private function createEmployee(array $employeeData): Employee
    {
        $employeeData = $this->normalizeEmployeeData($employeeData);

        $departmentId = $this->getOrCreateDepartment($employeeData['department_name'] ?? null);
        $sectionId = $this->getOrCreateSection($employeeData['section_name'] ?? null, $departmentId);
        $branchId = $this->getOrCreateBranch($employeeData['branch_name'] ?? null, $sectionId);
        $jobId = $this->getOrCreateJob($employeeData['job_name'] ?? null);

        if (!$jobId) {
            throw new \RuntimeException('تعذر تحديد المسمى الوظيفي');
        }

        return Employee::create([
            'czn_id' => $employeeData['czn_id'],
            'name' => $employeeData['name'],
            'birthdate' => $employeeData['birthdate'] ?? '1900-01-01',
            'mobile' => $employeeData['mobile'] ?? '0000000000',
            'email' => $employeeData['email'] ?? null,
            'address' => $employeeData['address'] ?? 'غير محدد',
            'gender' => $employeeData['gender'] ?? 'male',
            'birthplace' => $employeeData['birthplace'] ?? 'غير محدد',
            'salary' => $employeeData['salary'] ?? 0,
            'status' => $employeeData['status'] ?? 'active',
            'department_id' => $departmentId,
            'section_id' => $sectionId,
            'branch_id' => $branchId,
            'job_id' => $jobId,
        ]);
    }

    private function normalizeEmployeeData(array $data): array
    {
        if (isset($data['czn_id'])) {
            $data['czn_id'] = preg_replace('/\.0$/', '', (string) $data['czn_id']);
        }

        if (isset($data['mobile'])) {
            $data['mobile'] = preg_replace('/\.0$/', '', (string) $data['mobile']);
        }

        if (!empty($data['birthdate'])) {
            $data['birthdate'] = $this->normalizeBirthdate($data['birthdate']);
        }

        if (isset($data['gender'])) {
            $gender = trim($data['gender']);
            if (in_array($gender, ['ذكر', 'male'], true)) {
                $data['gender'] = 'male';
            } elseif (in_array($gender, ['أنثى', 'انثى', 'female'], true)) {
                $data['gender'] = 'female';
            }
        }

        if (isset($data['status'])) {
            $status = trim($data['status']);
            if (in_array($status, ['نشط', 'active', 'فعال', 'فعّال'], true)) {
                $data['status'] = 'active';
            } elseif (in_array($status, ['غير نشط', 'inactive', 'غير فعال'], true)) {
                $data['status'] = 'inactive';
            }
        }

        if (isset($data['salary'])) {
            $data['salary'] = (float) preg_replace('/[^\d.]/', '', (string) $data['salary']);
        }

        return $data;
    }

    private function normalizeBirthdate(mixed $value): string
    {
        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        $value = trim((string) $value);

        if (str_contains($value, '/')) {
            $dateParts = explode('/', $value);
            if (count($dateParts) === 3) {
                return $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
            }
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value, $matches)) {
            return $matches[0];
        }

        return $value;
    }

    private function getDefaultCategoryId(): int
    {
        if ($this->defaultCategoryId !== null) {
            return $this->defaultCategoryId;
        }

        $category = Category::first();
        if (!$category) {
            $category = Category::create([
                'name' => 'فئة غير مصنفة',
                'start_degree' => '10',
                'end_degree' => '10',
                'work_nature' => 'غير مصنفة',
                'included_degrees' => '10',
                'basic_salary' => 0,
            ]);
        }

        return $this->defaultCategoryId = $category->id;
    }

    private function getOrCreateDepartment(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }

        $department = Department::where('name', 'like', '%' . $name . '%')->first();
        if (!$department) {
            $department = Department::create(['name' => $name]);
        }

        return $department->id;
    }

    private function getOrCreateSection(?string $name, ?int $departmentId): ?int
    {
        if (empty($name) || empty($departmentId)) {
            return null;
        }

        $section = Section::where('name', 'like', '%' . $name . '%')
            ->where('department_id', $departmentId)
            ->first();

        if (!$section) {
            $section = Section::create([
                'name' => $name,
                'department_id' => $departmentId,
            ]);
        }

        return $section->id;
    }

    private function getOrCreateBranch(?string $name, ?int $sectionId): ?int
    {
        if (empty($name) || empty($sectionId)) {
            return null;
        }

        $branch = Branch::where('name', 'like', '%' . $name . '%')
            ->where('section_id', $sectionId)
            ->first();

        if (!$branch) {
            $branch = Branch::create([
                'name' => $name,
                'section_id' => $sectionId,
            ]);
        }

        return $branch->id;
    }

    private function getOrCreateJob(?string $name): ?int
    {
        if (empty($name)) {
            return null;
        }

        $job = Job::where('name', 'like', '%' . $name . '%')->first();
        if (!$job) {
            $job = Job::create([
                'name' => $name,
                'category_id' => $this->getDefaultCategoryId(),
            ]);
        }

        return $job->id;
    }
}
