<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Http\Controllers\EmployeeJobInfoController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncEmployeeRelatedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:sync-related-data
                            {--employee-id= : Sync a single employee by ID}
                            {--skip-dependents : Skip syncing dependents (uses external citizen API)}
                            {--only-job-history : Only sync job history}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create missing dependents and job history for employees that already have job info';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        DB::disableQueryLog();
        activity()->disableLogging();

        $controller = app(EmployeeJobInfoController::class);

        $query = Employee::query()
            ->whereHas('EmployeeJobInfo')
            ->with(['EmployeeJobInfo']);

        if ($this->option('employee-id')) {
            $query->where('id', $this->option('employee-id'));
        }

        $total = $query->count();

        if ($total === 0) {
            $this->warn('No employees with job info found.');
            return self::SUCCESS;
        }

        $syncDependents = ! ($this->option('skip-dependents') || $this->option('only-job-history'));
        $syncJobHistory = true;

        if ($syncDependents) {
            $this->warn('Dependents sync uses the external citizen API and may be slow.');
        }

        $this->info("Processing {$total} employee(s)...");

        $processed = 0;
        $failed = 0;

        $query->chunk(50, function ($employees) use ($controller, $syncDependents, $syncJobHistory, $total, &$processed, &$failed) {
            foreach ($employees as $employee) {
                $processed++;

                try {
                    if ($syncDependents) {
                        $controller->createDependentsIfMissing($employee);
                    }

                    if ($syncJobHistory) {
                        $controller->createJobHistoryIfMissing($employee);
                    }

                    $controller->createUserIfMissing($employee);
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning("employee:sync-related-data failed for employee", [
                        'employee_id' => $employee->id,
                        'name'        => $employee->name,
                        'error'       => $e->getMessage(),
                    ]);
                    $this->error("[{$processed}/{$total}] Failed: {$employee->name} (ID: {$employee->id}) - {$e->getMessage()}");
                    continue;
                }

                $this->line("[{$processed}/{$total}] Processed: {$employee->name} (ID: {$employee->id}) [memory: " . $this->formatBytes(memory_get_usage(true)) . "]");
            }
        });

        $this->newLine();
        $this->info("Sync completed: {$processed}/{$total} processed, {$failed} failed.");

        activity()->enableLogging();

        return self::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
