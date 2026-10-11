<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_job_infos', function (Blueprint $table) {
            // Single indexes للحقول المستخدمة في البحث والفلترة
            if (!$this->hasIndex('employee_job_infos', 'employee_job_infos_emp_number_index')) {
                $table->index('emp_number');
            }
            
            if (!$this->hasIndex('employee_job_infos', 'employee_job_infos_employee_id_index')) {
                $table->index('employee_id');
            }
            
            if (!$this->hasIndex('employee_job_infos', 'employee_job_infos_work_start_date_index')) {
                $table->index('work_start_date');
            }
            
            if (!$this->hasIndex('employee_job_infos', 'employee_job_infos_type_index')) {
                $table->index('type');
            }
            
            // Composite indexes للاستعلامات الشائعة
            if (!$this->hasIndex('employee_job_infos', 'employee_job_infos_employee_emp_number_index')) {
                $table->index(['employee_id', 'emp_number']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_job_infos', function (Blueprint $table) {
            $table->dropIndex(['emp_number']);
            $table->dropIndex(['employee_id']);
            $table->dropIndex(['work_start_date']);
            $table->dropIndex(['type']);
            $table->dropIndex(['employee_id', 'emp_number']);
        });
    }

    /**
     * التحقق من وجود index
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        
        $indexes = $connection->select("
            SELECT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = ? 
            AND INDEX_NAME = ?
        ", [$database, $table, $indexName]);
        
        return !empty($indexes);
    }
};
