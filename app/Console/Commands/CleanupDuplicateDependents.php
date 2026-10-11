<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateDependents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:cleanup-duplicate-dependents
                            {--employee-id= : Clean duplicates for a single employee}
                            {--dry-run : Show duplicates without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate dependents (keep latest record for each czn_id)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Disable activity logging to prevent memory exhaustion
        config(['activitylog.enabled' => false]);
        
        $this->info('🔍 Searching for duplicate dependents...');
        $this->newLine();

        $query = DB::table('employee_dependents')
            ->select('employee_id', 'czn_id', DB::raw('COUNT(*) as count'))
            ->whereNotNull('czn_id')
            ->where('czn_id', '!=', '')
            ->groupBy('employee_id', 'czn_id')
            ->having('count', '>', 1);

        if ($this->option('employee-id')) {
            $query->where('employee_id', $this->option('employee-id'));
        }

        $duplicates = $query->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate dependents found.');
            return 0;
        }

        $this->warn("Found {$duplicates->count()} duplicate group(s)");
        $this->newLine();

        $totalDeleted = 0;
        $bar = $this->output->createProgressBar($duplicates->count());
        $bar->start();

        foreach ($duplicates as $dup) {
            $bar->advance();

            // Get all IDs for this duplicate group
            $allIds = DB::table('employee_dependents')
                ->where('employee_id', $dup->employee_id)
                ->where('czn_id', $dup->czn_id)
                ->orderByDesc('id')
                ->pluck('id')
                ->toArray();

            // Keep the first one (latest), delete the rest
            $idsToDelete = array_slice($allIds, 1);

            if ($this->option('dry-run')) {
                $this->line("Employee {$dup->employee_id}, CZN {$dup->czn_id}: {$dup->count} records, would delete " . count($idsToDelete));
            } else {
                if (!empty($idsToDelete)) {
                    $deleted = DB::table('employee_dependents')
                        ->whereIn('id', $idsToDelete)
                        ->delete();

                    $totalDeleted += $deleted;
                }
            }
        }

        $bar->finish();
        $this->newLine(2);

        if ($this->option('dry-run')) {
            $this->info('ℹ️  Dry run - no records were deleted');
        } else {
            $this->info("✅ Successfully deleted {$totalDeleted} duplicate record(s)");
        }

        return 0;
    }
}
