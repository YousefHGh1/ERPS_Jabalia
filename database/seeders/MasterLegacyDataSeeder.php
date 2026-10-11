<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * يحمّل بيانات الملفات التاريخية (هندسة وتنظيم) من ملف master2026_mysql.sql
 * إلى جدول MASTER.
 *
 * المتطلبات:
 * 1. تشغيل migration جدول MASTER أولاً: php artisan migrate
 * 2. وجود الملف master2026_mysql.sql في جذر المشروع
 *
 * التشغيل:
 *   php artisan db:seed --class=MasterLegacyDataSeeder
 */
class MasterLegacyDataSeeder extends Seeder
{
    /** مسار ملف SQL في جذر المشروع */
    protected function getSqlPath(): string
    {
        return base_path('master2026_mysql.sql');
    }

    public function run(): void
    {
        $path = $this->getSqlPath();

        if (! is_file($path) || ! is_readable($path)) {
            $this->command->error("ملف SQL غير موجود أو غير قابل للقراءة: {$path}");
            $this->command->info('ضع ملف master2026_mysql.sql في جذر المشروع ثم أعد التشغيل.');
            return;
        }

        $this->command->info('بدء استيراد بيانات MASTER من: ' . $path);

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->command->error('تعذر فتح الملف.');
            return;
        }

        $inserted = 0;
        $skipped = 0;
        $batchSize = 500;
        $batch = 0;

        DB::disableQueryLog();

        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                // ننفذ فقط أسطر INSERT INTO `MASTER` (نتجاهل SET, DROP, التعليقات، إلخ)
                if ($line !== '' && stripos($line, 'INSERT INTO `MASTER`') === 0) {
                    try {
                        DB::unprepared($line);
                        $inserted++;
                        if ($inserted % $batchSize === 0) {
                            $batch++;
                            $this->command->getOutput()->write("\rتم إدخال {$inserted} سجل حتى الآن...");
                        }
                    } catch (\Throwable $e) {
                        $skipped++;
                        if ($skipped <= 3) {
                            $this->command->warn("تخطي سطر بسبب خطأ: " . $e->getMessage());
                        }
                    }
                }
            }
        } finally {
            fclose($handle);
            DB::enableQueryLog();
        }

        $this->command->newLine();
        $this->command->info("تم استيراد {$inserted} سجل بنجاح.");

        // بناء فهرس البحث للسجلات المستوردة (أُدخلت بـ SQL مباشر)
        $this->command->call('historical:reindex', ['--source' => 'engineering']);
        if ($skipped > 0) {
            $this->command->warn("تم تخطي {$skipped} سطر بسبب أخطاء.");
        }
    }
}
