<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\EmployeeDependent;

class ExportDependentsBorn2008 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:export-tawjihi-dependents
                            {--birth-year= : Birth year for tawjihi students (default: current year - 18)}
                            {--output= : Output file path (CSV format)}
                            {--section-id= : Filter by section ID}
                            {--department-id= : Filter by department ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export dependents born in tawjihi year (dynamic based on current year)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Calculate birth year for tawjihi students (current year - 18)
        $currentYear = Carbon::now()->year;
        $birthYear = $this->option('birth-year') ?? ($currentYear - 18);

        $this->info("🔍 Searching for dependents born in {$birthYear} (Tawjihi students)...");
        $this->newLine();

        // Build query
        $query = EmployeeDependent::with(['employee' => function($q) {
            $q->with(['department', 'section', 'job']);
        }])
        ->whereYear('birth', $birthYear)
        ->orderBy('employee_id')
        ->orderBy('birth');

        // Apply filters if provided
        if ($this->option('section-id')) {
            $query->whereHas('employee', function($q) {
                $q->where('section_id', $this->option('section-id'));
            });
        }

        if ($this->option('department-id')) {
            $query->whereHas('employee', function($q) {
                $q->where('department_id', $this->option('department-id'));
            });
        }

        $dependents = $query->get();

        if ($dependents->isEmpty()) {
            $this->warn("⚠️  No dependents born in {$birthYear} found.");
            $this->newLine();
            
            // Create empty CSV file with headers if output is specified
            if ($this->option('output')) {
                $this->exportEmptyCsv($this->option('output'), $birthYear);
            }
            
            return 0;
        }

        $this->info("✅ Found {$dependents->count()} dependent(s) born in {$birthYear}");
        $this->newLine();

        // Display results in table format
        $this->table(
            ['Employee ID', 'Employee Name', 'Mobile', 'Department', 'Section', 'Dependent Name', 'CZN ID', 'Birth Date', 'Gender', 'Relation'],
            $dependents->map(function($dep) {
                return [
                    $dep->employee->emp_number ?? 'N/A',
                    $dep->employee->name ?? 'N/A',
                    $dep->employee->mobile ?? 'N/A',
                    $dep->employee->department->name ?? 'N/A',
                    $dep->employee->section->name ?? 'N/A',
                    $dep->name,
                    $dep->czn_id,
                    Carbon::parse($dep->birth)->format('Y-m-d'),
                    $dep->gender,
                    $dep->relation,
                ];
            })->toArray()
        );

        // Export to CSV if output file specified
        if ($this->option('output')) {
            $this->exportToCsv($dependents, $this->option('output'));
        }

        $this->newLine();
        $this->info('✅ Export completed successfully');

        return 0;
    }

    /**
     * Export data to CSV file
     */
    private function exportToCsv($dependents, $filePath): void
    {
        // Create directory if it doesn't exist
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fp = fopen($filePath, 'w');
        
        // Add BOM for Excel Arabic support
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV Headers
        fputcsv($fp, [
            'رقم الموظف',
            'اسم الموظف',
            'رقم الجوال',
            'القسم',
            'الشعبة',
            'اسم المعال',
            'رقم الهوية',
            'تاريخ الميلاد',
            'الجنس',
            'صلة القرابة',
            'الحالة'
        ], ',', '"');

        // Data rows
        foreach ($dependents as $dep) {
            fputcsv($fp, [
                $dep->employee->emp_number ?? '',
                $dep->employee->name ?? '',
                $dep->employee->mobile ?? '',
                $dep->employee->department->name ?? '',
                $dep->employee->section->name ?? '',
                $dep->name,
                $dep->czn_id,
                Carbon::parse($dep->birth)->format('Y-m-d'),
                $dep->gender,
                $dep->relation,
                $dep->status
            ], ',', '"');
        }

        fclose($fp);

        $this->info("📄 CSV file exported to: {$filePath}");
    }

    /**
     * Export empty CSV file with headers
     */
    private function exportEmptyCsv($filePath, $birthYear): void
    {
        // Create directory if it doesn't exist
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fp = fopen($filePath, 'w');
        
        // Add BOM for Excel Arabic support
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV Headers
        fputcsv($fp, [
            'رقم الموظف',
            'اسم الموظف',
            'رقم الجوال',
            'القسم',
            'الشعبة',
            'اسم المعال',
            'رقم الهوية',
            'تاريخ الميلاد',
            'الجنس',
            'صلة القرابة',
            'الحالة'
        ], ',', '"');

        // Add empty row with message
        fputcsv($fp, [
            '',
            '',
            '',
            '',
            '',
            "لا توجد بيانات للمعالين المولودين في {$birthYear}",
            '',
            '',
            '',
            '',
            ''
        ], ',', '"');

        fclose($fp);

        $this->info("📄 Empty CSV file created: {$filePath}");
    }
}
