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
        Schema::table('employees', function (Blueprint $table) {
            // Single indexes للحقول المستخدمة في البحث والفلترة
            if (!$this->hasIndex('employees', 'employees_department_id_index')) {
                $table->index('department_id');
            }
            
            if (!$this->hasIndex('employees', 'employees_section_id_index')) {
                $table->index('section_id');
            }
            
            if (!$this->hasIndex('employees', 'employees_branch_id_index')) {
                $table->index('branch_id');
            }
            
            if (!$this->hasIndex('employees', 'employees_job_id_index')) {
                $table->index('job_id');
            }
            
            if (!$this->hasIndex('employees', 'employees_status_index')) {
                $table->index('status');
            }
            
            if (!$this->hasIndex('employees', 'employees_czn_id_index')) {
                $table->index('czn_id');
            }
            
            // Composite indexes للاستعلامات الشائعة
            if (!$this->hasIndex('employees', 'employees_department_section_index')) {
                $table->index(['department_id', 'section_id']);
            }
            
            if (!$this->hasIndex('employees', 'employees_status_department_index')) {
                $table->index(['status', 'department_id']);
            }
            
            // Index للبحث بالاسم
            if (!$this->hasIndex('employees', 'employees_name_index')) {
                $table->index('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['department_id']);
            $table->dropIndex(['section_id']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['job_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['czn_id']);
            $table->dropIndex(['department_id', 'section_id']);
            $table->dropIndex(['status', 'department_id']);
            $table->dropIndex(['name']);
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
