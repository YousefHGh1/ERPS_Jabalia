<?php

namespace App\Console\Commands;

use App\Models\ImportedExcelFile;
use App\Models\JebayaInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JebayaImport extends Command
{
    protected $signature = 'jebaya:import 
        {--file= : المسار إلى ملف فواتير الجباية xlsx}
        {--reset : حذف البيانات الحالية قبل الاستيراد}
        {--from-db : إعادة الاستيراد من الملف المخزن في قاعدة البيانات}
        {--force : تجاوز فحص تكرار الملف وإعادة استيراده}';

    protected $description = 'استيراد فواتير الجباية من ملف Excel إلى قاعدة البيانات مع حفظ الملف';

    public function handle()
    {
        if ($this->option('from-db')) {
            $record = ImportedExcelFile::orderByDesc('imported_at')->orderByDesc('id')->first();
            if (! $record) {
                $this->error('لا يوجد ملف مستورد مخزن في قاعدة البيانات.');
                return self::FAILURE;
            }

            $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $record->file_name;
            if (! file_exists($tmpPath)) {
                file_put_contents($tmpPath, $record->file_data);
                $this->info("تم استخراج الملف من قاعدة البيانات إلى: {$tmpPath}");
            }

            return $this->importFromFile($tmpPath, true);
        }

        $file = $this->option('file') ?: base_path('fawatweeer.xlsx');

        if (! file_exists($file)) {
            $this->error("الملف غير موجود: {$file}");
            return self::FAILURE;
        }

        return $this->importFromFile($file, false);
    }

    protected function importFromFile(string $file, bool $fromDb): int
    {
        if ($this->option('reset')) {
            JebayaInvoice::query()->delete();
            ImportedExcelFile::query()->delete();
            $this->info('تم حذف البيانات السابقة.');
        }

        if (! $fromDb) {
            if (! $this->storeFile($file)) {
                return self::FAILURE;
            }
        }

        $zip = new \ZipArchive();
        if ($zip->open($file) !== true) {
            $this->error('تعذر فتح ملف xlsx.');
            return self::FAILURE;
        }

        $wsName = 'xl/worksheets/sheet1.xml';
        if ($zip->locateName($wsName) === false) {
            $this->error('لا توجد ورقة sheet1 في الملف.');
            return self::FAILURE;
        }

        // قراءة السلاسل المشتركة
        $shared = [];
        if ($zip->locateName('xl/sharedStrings.xml') !== false) {
            $sr = new \XMLReader();
            $sr->open('zip://' . str_replace('\\', '/', $file) . '#xl/sharedStrings.xml', null, LIBXML_NONET);
            $idx = -1;
            while ($sr->read()) {
                if ($sr->nodeType === \XMLReader::ELEMENT && $sr->localName === 'si') {
                    $idx++;
                    $shared[$idx] = '';
                } elseif ($sr->nodeType === \XMLReader::ELEMENT && $sr->localName === 't') {
                    $shared[$idx] .= $sr->readString();
                }
            }
            $sr->close();
        }

        $this->line('قراءة ورقة البيانات...');

        $wr = new \XMLReader();
        $wr->open('zip://' . str_replace('\\', '/', $file) . '#' . $wsName, null, LIBXML_NONET);

        $inserted = 0;
        $skipped = 0;
        $chunk = [];
        $rowNum = 0; // 1-based excel row

        while ($wr->read()) {
            if ($wr->nodeType !== \XMLReader::ELEMENT || $wr->localName !== 'row') {
                continue;
            }
            $rowNum++;
            if ($rowNum === 1) {
                continue; // skip header
            }

            $cells = $this->parseRow($wr, $shared);

            $name   = $cells['L'] ?? '';
            $amount = $this->num($cells['E'] ?? 0); // net_amt

            if ($name === '' && $amount == 0) {
                $skipped++;
                continue;
            }

            $chunk[] = [
                'agreement_id'        => $this->nullable($cells['A'] ?? null),
                'voucher_no'          => $this->nullable($cells['B'] ?? null),
                'voucher_date'        => $this->excelDate($cells['C'] ?? null),
                'voucher_amt'         => $this->num($cells['D'] ?? 0),
                'net_amt'             => $amount,
                'total_late'          => $this->num($cells['F'] ?? 0),
                'last_due_date'       => $this->excelDate($cells['G'] ?? null),
                'current_voucher_amt' => $this->num($cells['H'] ?? 0),
                'auto_discount_ratio' => $this->num($cells['I'] ?? 0),
                'voucher_month'       => $this->nullable($cells['J'] ?? null),
                'mobile_no'           => $this->nullable($cells['K'] ?? null),
                'arabic_name'         => $name,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            $inserted++;

            if (count($chunk) >= 500) {
                DB::table('jebaya_invoices')->insertOrIgnore($chunk);
                $chunk = [];
            }
        }
        $wr->close();

        if (! empty($chunk)) {
            DB::table('jebaya_invoices')->insertOrIgnore($chunk);
        }

        if (! $fromDb) {
            ImportedExcelFile::where('file_name', basename($file))
                ->update(['records_count' => $inserted, 'imported_at' => now()]);
        }

        $this->info("اكتمل الاستيراد: تم إدراج {$inserted} فاتورة (تم تخطي {$skipped} صف فارغ).");

        return self::SUCCESS;
    }

    protected function storeFile(string $file): bool
    {
        $fileName = basename($file);
        $exists = ImportedExcelFile::where('file_name', $fileName)->exists();

        if ($exists && ! $this->option('force')) {
            $this->warn("الملف {$fileName} مخزن بالفعل في قاعدة البيانات. استخدم --force لإعادة استيراده.");
            return false;
        }

        if ($exists) {
            ImportedExcelFile::where('file_name', $fileName)->delete();
            $this->info("تم حذف السجل القديم للملف {$fileName}.");
        }

        ImportedExcelFile::create([
            'file_name'     => $fileName,
            'mime_type'     => mime_content_type($file),
            'file_size'     => filesize($file),
            'file_data'     => file_get_contents($file),
            'records_count' => 0,
        ]);

        $this->info("تم حفظ الملف {$fileName} في قاعدة البيانات.");

        return true;
    }

    /**
     * تحليل صف XML إلى أعمدة A-L.
     */
    protected function parseRow(\XMLReader $wr, array $shared): array
    {
        $dom = new \DOMDocument();
        $dom->appendChild($dom->importNode($wr->expand(), true));
        $xp = new \DOMXPath($dom);
        $xp->registerNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $cells = [];
        foreach ($xp->query('.//m:c') as $c) {
            $ref = $c->getAttribute('r');
            $t = $c->getAttribute('t');
            $cname = preg_replace('/\d+$/', '', $ref);
            $vNode = $xp->query('m:v', $c)->item(0);
            $val = $vNode ? trim($vNode->nodeValue) : '';
            if ($t === 's' && $val !== '' && isset($shared[(int) $val])) {
                $val = $shared[(int) $val];
            }
            if ($t === 'inlineStr') {
                $is = $xp->query('m:is', $c)->item(0);
                if ($is) {
                    $val = $is->textContent;
                }
            }
            $cells[$cname] = $val;
        }

        return $cells;
    }

    /**
     * تحويل رقم تاريخ Excel (serial) إلى Y-m-d.
     */
    protected function excelDate($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value) || (int) $value <= 0) {
            return null;
        }
        $serial = (int) round((float) $value);
        return date('Y-m-d', strtotime('1899-12-30 +' . $serial . ' days'));
    }

    protected function num($value): float
    {
        $value = trim((string) $value);
        return $value === '' ? 0.0 : (float) $value;
    }

    protected function nullable($value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}

