<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) إضافة عمود department_id إن لم يكن موجوداً
        if (! Schema::hasColumn('request_responses', 'department_id')) {
            DB::statement('ALTER TABLE `request_responses` ADD COLUMN `department_id` BIGINT UNSIGNED NULL AFTER `section_id`');
        }

        // 2) جعل section_id يقبل NULL (لدعم مسارات الدوائر بدون قسم)
        DB::statement('ALTER TABLE `request_responses` MODIFY `section_id` BIGINT UNSIGNED NULL');

        // 3) إضافة مفتاح أجنبي لـ department_id إن لم يكن موجوداً
        $fks = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'request_responses' AND COLUMN_NAME = 'department_id' AND REFERENCED_TABLE_NAME = 'departments'");
        if (empty($fks)) {
            try {
                DB::statement('ALTER TABLE `request_responses` ADD CONSTRAINT `request_responses_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT');
            } catch (\Throwable $e) {
                // تجاهل إن كان موجوداً مسبقاً
            }
        }

        // 4) فهرس اختياري لتسريع فلترة الدوائر
        try {
            DB::statement('CREATE INDEX `request_responses_department_id_response_type_index` ON `request_responses` (`department_id`, `response_type`)');
        } catch (\Throwable $e) {
            // تجاهل إن كان موجوداً
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE `request_responses` DROP FOREIGN KEY `request_responses_department_id_foreign`');
        } catch (\Throwable $e) {
        }
        try {
            DB::statement('DROP INDEX `request_responses_department_id_response_type_index` ON `request_responses`');
        } catch (\Throwable $e) {
        }
        if (Schema::hasColumn('request_responses', 'department_id')) {
            DB::statement('ALTER TABLE `request_responses` DROP COLUMN `department_id`');
        }
    }
};
