<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PreviewExcel extends Command
{
    protected $signature = 'preview:excel {path} {--rows=5} {--start=2}';
    protected $description = 'Preview Excel file headers and sample rows';

    public function handle()
    {
        $pathArg = $this->argument('path');
        $rows = (int) $this->option('rows');
        $start = max(2, (int) $this->option('start'));

        $base = base_path($pathArg);
        if (!file_exists($base)) {
            $this->error("File not found: $base");
            return 1;
        }

        try {
            $spreadsheet = IOFactory::load($base);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            // Read header
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $headers[] = trim((string) $sheet->getCell(Coordinate::stringFromColumnIndex($col) . '1')->getValue());
            }

            $this->info('Headers:');
            foreach ($headers as $i => $h) {
                $this->line(($i+1) . ": " . $h);
            }

            $this->info("\nSample rows (up to $rows):");
            $sampleEnd = min($highestRow, $start + $rows - 1);
            for ($r = $start; $r <= $sampleEnd; $r++) {
                $row = [];
                for ($c = 1; $c <= $highestColumnIndex; $c++) {
                    $row[] = (string) $sheet->getCell(Coordinate::stringFromColumnIndex($c) . $r)->getFormattedValue();
                }
                $this->line($r . ': ' . implode(' | ', $row));
            }

            return 0;
        } catch (\Throwable $e) {
            $this->error('Error reading Excel: ' . $e->getMessage());
            return 1;
        }
    }
}
