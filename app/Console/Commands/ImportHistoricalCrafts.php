<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\HistoricalCraft;

class ImportHistoricalCrafts extends Command
{
    protected $signature = 'import:historical-crafts {path}';
    protected $description = 'Import rows from an Excel file into historical_crafts (one-time import)';

    public function handle()
    {
        $pathArg = $this->argument('path');
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
                $headers[] = trim((string) $sheet->getCellByColumnAndRow($col, 1)->getValue());
            }

            $this->info('Importing rows...');
            $count = 0;
            for ($r = 2; $r <= $highestRow; $r++) {
                $row = [];
                $empty = true;
                for ($c = 1; $c <= $highestColumnIndex; $c++) {
                    $value = (string) $sheet->getCellByColumnAndRow($c, $r)->getFormattedValue();
                    if ($value !== '') $empty = false;
                    $key = $headers[$c - 1] ?? ('col_' . $c);
                    $row[$key] = $value;
                }
                if ($empty) continue;
                HistoricalCraft::create(['data' => $row]);
                $count++;
            }

            $this->info("Imported $count rows into historical_crafts");
            return 0;
        } catch (\Throwable $e) {
            $this->error('Error importing Excel: ' . $e->getMessage());
            return 1;
        }
    }
}
