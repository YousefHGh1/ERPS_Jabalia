<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // فقط إضافة الفهارس المفقودة وتجنب الموجودة
        
        // تحسين جدول employee_holidays (تجنب الفهارس الموجودة)
        $this->addIndexIfNotExists('employee_holidays', 'employee_holidays_employee_id_index', 'employee_id');
        $this->addIndexIfNotExists('employee_holidays', 'employee_holidays_holiday_id_index', 'holiday_id');
        // تجنب start_date_end_date_index لأنه موجود بالفعل

        // تحسين جدول craft_licenses
        $this->addIndexIfNotExists('craft_licenses', 'craft_licenses_file_number_index', 'file_number');
        $this->addIndexIfNotExists('craft_licenses', 'craft_licenses_status_index', 'status');
        $this->addIndexIfNotExists('craft_licenses', 'craft_licenses_id_number_index', 'id_number');
        $this->addIndexIfNotExists('craft_licenses', 'craft_licenses_owner_name_index', 'owner_name');
        $this->addIndexIfNotExists('craft_licenses', 'craft_licenses_registration_date_index', 'registration_date');
        $this->addIndexIfNotExists('craft_licenses', 'craft_licenses_craft_type_index', 'craft_type');
        $this->addCompositeIndexIfNotExists('craft_licenses', 'craft_licenses_status_type_index', ['status', 'craft_type']);

        // تحسين جدول engineerings
        $this->addIndexIfNotExists('engineerings', 'engineerings_file_number_index', 'file_number');
        $this->addIndexIfNotExists('engineerings', 'engineerings_national_id_index', 'national_id');
        $this->addIndexIfNotExists('engineerings', 'engineerings_plot_number_index', 'plot_number');
        $this->addIndexIfNotExists('engineerings', 'engineerings_neighborhood_index', 'neighborhood');
        $this->addIndexIfNotExists('engineerings', 'engineerings_application_date_index', 'application_date');
        $this->addIndexIfNotExists('engineerings', 'engineerings_created_by_index', 'created_by');
        $this->addCompositeIndexIfNotExists('engineerings', 'engineerings_plot_neighborhood_index', ['plot_number', 'neighborhood']);

        // تحسين جدول request_responses
        $this->addIndexIfNotExists('request_responses', 'request_responses_workflow_id_index', 'workflow_id');
        $this->addIndexIfNotExists('request_responses', 'request_responses_created_by_index', 'created_by');
        $this->addIndexIfNotExists('request_responses', 'request_responses_responsed_by_index', 'responsed_by');
        $this->addIndexIfNotExists('request_responses', 'request_responses_responsed_at_index', 'responsed_at');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف الفهارس المضافة فقط
        $this->dropIndexIfExists('employee_holidays', 'employee_holidays_employee_id_index');
        $this->dropIndexIfExists('employee_holidays', 'employee_holidays_holiday_id_index');

        $this->dropIndexIfExists('craft_licenses', 'craft_licenses_file_number_index');
        $this->dropIndexIfExists('craft_licenses', 'craft_licenses_status_index');
        $this->dropIndexIfExists('craft_licenses', 'craft_licenses_id_number_index');
        $this->dropIndexIfExists('craft_licenses', 'craft_licenses_owner_name_index');
        $this->dropIndexIfExists('craft_licenses', 'craft_licenses_registration_date_index');
        $this->dropIndexIfExists('craft_licenses', 'craft_licenses_craft_type_index');
        $this->dropIndexIfExists('craft_licenses', 'craft_licenses_status_type_index');

        $this->dropIndexIfExists('engineerings', 'engineerings_file_number_index');
        $this->dropIndexIfExists('engineerings', 'engineerings_national_id_index');
        $this->dropIndexIfExists('engineerings', 'engineerings_plot_number_index');
        $this->dropIndexIfExists('engineerings', 'engineerings_neighborhood_index');
        $this->dropIndexIfExists('engineerings', 'engineerings_application_date_index');
        $this->dropIndexIfExists('engineerings', 'engineerings_created_by_index');
        $this->dropIndexIfExists('engineerings', 'engineerings_plot_neighborhood_index');

        $this->dropIndexIfExists('request_responses', 'request_responses_workflow_id_index');
        $this->dropIndexIfExists('request_responses', 'request_responses_created_by_index');
        $this->dropIndexIfExists('request_responses', 'request_responses_responsed_by_index');
        $this->dropIndexIfExists('request_responses', 'request_responses_responsed_at_index');
    }

    /**
     * إضافة فهرس مفرد إذا لم يكن موجوداً
     */
    private function addIndexIfNotExists(string $table, string $indexName, string $column): void
    {
        try {
            if (!$this->hasIndex($table, $indexName)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->index($column);
                });
            }
        } catch (\Exception $e) {
            // تجاهل الخطأ إذا كان الفهرس موجوداً بالفعل
        }
    }

    /**
     * إضافة فهرس مركب إذا لم يكن موجوداً
     */
    private function addCompositeIndexIfNotExists(string $table, string $indexName, array $columns): void
    {
        try {
            if (!$this->hasIndex($table, $indexName)) {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    $table->index($columns);
                });
            }
        } catch (\Exception $e) {
            // تجاهل الخطأ إذا كان الفهرس موجوداً بالفعل
        }
    }

    /**
     * حذف فهرس إذا كان موجوداً
     */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        try {
            if ($this->hasIndex($table, $indexName)) {
                Schema::table($table, function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            }
        } catch (\Exception $e) {
            // تجاهل الخطأ
        }
    }

    /**
     * التحقق من وجود index بطريقة موثوقة
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
};
