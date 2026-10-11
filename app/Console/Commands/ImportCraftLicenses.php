<?php

namespace App\Console\Commands;

use App\Models\CraftLicense;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportCraftLicenses extends Command
{
    protected $signature = 'import:craft-licenses
        {path : Excel file path relative to the project root}
        {--fresh : Delete current craft license data before importing}
        {--dry-run : Validate and preview without writing to the database}
        {--skip-invalid : Skip rows with missing required values or duplicate file numbers}
        {--status=undefined : Imported license status}
        {--classification=تجارية : Imported license classification}
        {--registration-date= : Registration date, defaults to today}';

    protected $description = 'Import craft licenses from an Excel sheet with Arabic headers';

    public function handle(): int
    {
        $path = base_path($this->argument('path'));

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $rows = $this->readRows($path);
        if ($rows === []) {
            $this->warn('No data rows found.');

            return self::SUCCESS;
        }

        [$rows, $errors, $skipped] = $this->prepareRows($rows);
        if ($errors !== []) {
            foreach (array_slice($errors, 0, 20) as $error) {
                $this->error($error);
            }

            if (count($errors) > 20) {
                $this->error('... and ' . (count($errors) - 20) . ' more errors.');
            }

            return self::FAILURE;
        }

        $this->info('Rows ready for import: ' . count($rows));
        if ($skipped !== []) {
            $this->warn('Skipped rows: ' . count($skipped));
            foreach (array_slice($skipped, 0, 20) as $skip) {
                $this->line($skip);
            }
        }

        if ($this->option('dry-run')) {
            $this->table(
                ['file_number', 'owner_name', 'id_number', 'detailed_craft_type', 'address', 'phone_number'],
                array_map(fn (array $row) => [
                    $row['file_number'],
                    $row['owner_name'],
                    $row['id_number'],
                    $row['detailed_craft_type'],
                    $row['address'],
                    $row['phone_number'],
                ], array_slice($rows, 0, 10))
            );

            return self::SUCCESS;
        }

        $now = now();
        $registrationDate = $this->option('registration-date') ?: $now->toDateString();
        $status = (string) $this->option('status');
        $classification = (string) $this->option('classification');

        DB::transaction(function () use ($rows, $now, $registrationDate, $status, $classification) {
            if ($this->option('fresh')) {
                $licenseIds = CraftLicense::withTrashed()->pluck('id');

                if ($licenseIds->isNotEmpty()) {
                    DB::table('payments')->whereIn('craft_license_id', $licenseIds)->delete();
                    DB::table('craft_license_partners')->whereIn('craft_license_id', $licenseIds)->delete();
                    DB::table('craft_license_attachments')->whereIn('craft_license_id', $licenseIds)->delete();
                }

                DB::table('craft_fee_histories')->delete();
                DB::table('craft_licenses')->delete();
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('craft_licenses')->insert(array_map(function (array $row) use ($now, $registrationDate, $status, $classification) {
                    return [
                        'file_number' => $row['file_number'],
                        'status' => $status,
                        'id_number' => $row['id_number'],
                        'phone_number' => $row['phone_number'],
                        'owner_name' => $row['owner_name'],
                        'classification' => $classification,
                        'craft_type' => 'جاري',
                        'detailed_craft_type' => $row['detailed_craft_type'],
                        'registration_date' => $registrationDate,
                        'commercial_name' => $row['owner_name'],
                        'address' => $row['address'],
                        'total_debts' => 0,
                        'craft_fees' => 0,
                        'payment_amount' => 0,
                        'signboard_fees' => 0,
                        'discount_percent' => 0,
                        'remaining' => 0,
                        'final_price' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }, $chunk));
            }
        });

        $this->info('Imported craft licenses: ' . count($rows));

        return self::SUCCESS;
    }

    private function readRows(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $headers = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $headers[$col] = trim((string) $sheet->getCell(Coordinate::stringFromColumnIndex($col) . '1')->getValue());
        }

        $headerMap = array_flip($headers);

        $rows = [];
        for ($rowNumber = 2; $rowNumber <= $highestRow; $rowNumber++) {
            $fileNumber = $this->cellValue($sheet, $headerMap, 'رقم الملف', $rowNumber);
            $ownerName = $this->cellValue($sheet, $headerMap, 'الاسم', $rowNumber);
            $idNumber = $this->cellValue($sheet, $headerMap, 'رقم الهوية', $rowNumber);
            $craft = $this->cellValue($sheet, $headerMap, 'الحرفة', $rowNumber);
            $address = $this->cellValue($sheet, $headerMap, 'العنوان', $rowNumber);
            $rawPhone = $this->cellValue($sheet, $headerMap, 'رقم الجوال', $rowNumber);

            if ($fileNumber === '' && $ownerName === '' && $idNumber === '' && $craft === '' && $address === '' && $rawPhone === '') {
                continue;
            }

            $rows[] = [
                '_excel_row' => $rowNumber,
                'file_number' => $fileNumber,
                'owner_name' => $ownerName,
                'id_number' => $idNumber,
                'detailed_craft_type' => $craft,
                'address' => $address,
                'phone_number' => $this->normalizePhone($rawPhone),
            ];
        }

        return $rows;
    }

    private function cellValue($sheet, array $headerMap, string $header, int $row): string
    {
        $column = $headerMap[$header] ?? null;
        if ($column === null) {
            return '';
        }

        $value = $sheet->getCell(Coordinate::stringFromColumnIndex($column) . $row)->getFormattedValue();

        return trim((string) $value);
    }

    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 9 && str_starts_with($digits, '5')) {
            return '0' . $digits;
        }

        return $digits;
    }

    private function prepareRows(array $rows): array
    {
        $errors = [];
        $validRows = [];
        $skipped = [];
        $seenFileNumbers = [];
        $skipInvalid = (bool) $this->option('skip-invalid');

        foreach ($rows as $index => $row) {
            $excelRow = $row['_excel_row'] ?? ($index + 2);
            $rowErrors = [];

            if ($row['file_number'] === '') {
                $rowErrors[] = "Row {$excelRow}: missing file number.";
            } elseif (strlen($row['file_number']) > 6) {
                $rowErrors[] = "Row {$excelRow}: file number exceeds 6 characters.";
            }

            if ($row['owner_name'] === '') {
                $rowErrors[] = "Row {$excelRow}: missing owner name.";
            }

            if ($row['id_number'] !== '' && strlen($row['id_number']) > 9) {
                $rowErrors[] = "Row {$excelRow}: ID number exceeds 9 characters.";
            }

            if ($row['detailed_craft_type'] === '') {
                $rowErrors[] = "Row {$excelRow}: missing craft type.";
            }

            if ($row['file_number'] !== '') {
                if (isset($seenFileNumbers[$row['file_number']])) {
                    $rowErrors[] = "Row {$excelRow}: duplicate file number {$row['file_number']}.";
                }

                $seenFileNumbers[$row['file_number']] = true;
            }

            if ($rowErrors !== []) {
                if ($skipInvalid) {
                    $skipped[] = implode(' ', $rowErrors);
                    continue;
                }

                array_push($errors, ...$rowErrors);
            }

            $validRows[] = $row;
        }

        return [$validRows, $errors, $skipped];
    }
}
