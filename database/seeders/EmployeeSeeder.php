<?php

namespace Database\Seeders;

use App\Services\EmployeeImportService;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = public_path('templates/employees_data.csv');

        if (!file_exists($csvFile)) {
            $this->command->error('CSV file not found: ' . $csvFile);
            return;
        }

        $importService = app(EmployeeImportService::class);

        try {
            $results = $importService->importFromFile($csvFile, 'csv');

            $this->command->info("Successfully imported {$results['imported']} employees");
            if ($results['skipped'] > 0) {
                $this->command->warn("Skipped {$results['skipped']} employees (already exist)");
            }
            if (!empty($results['errors'])) {
                $this->command->warn('Errors (' . count($results['errors']) . '):');
                foreach (array_slice($results['errors'], 0, 10) as $error) {
                    $this->command->warn("  - {$error}");
                }
                if (count($results['errors']) > 10) {
                    $this->command->warn('  ... and ' . (count($results['errors']) - 10) . ' more');
                }
            }
        } catch (\Throwable $e) {
            $this->command->error('Error during import: ' . $e->getMessage());
            throw $e;
        }
    }
}
