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
        // Use raw SQL for maximum compatibility
        $this->addIndexIfNotExists('employees', 'idx_employees_section_status', 'section_id, status');
        $this->addIndexIfNotExists('employees', 'idx_employees_status_created', 'status, created_at');
        
        $this->addIndexIfNotExists('notifications', 'idx_notifications_user_read', 'user_id, read_at');
        $this->addIndexIfNotExists('notifications', 'idx_notifications_priority_created', 'priority, created_at');
        
        $this->addIndexIfNotExists('employee_holidays', 'idx_holidays_status_created', 'status, created_at');
        $this->addIndexIfNotExists('employee_holidays', 'idx_holidays_employee', 'employee_id');
        
        $this->addIndexIfNotExists('employee_permissions', 'idx_permissions_status_created', 'status, created_at');
        $this->addIndexIfNotExists('employee_permissions', 'idx_permissions_employee', 'employee_id');

        echo "[INFO] Simple indexes migration completed successfully\n";
    }

    /**
     * Add index if it doesn't exist using raw SQL
     */
    private function addIndexIfNotExists($table, $indexName, $columns)
    {
        try {
            // Check if table exists
            if (!$this->tableExists($table)) {
                echo "[INFO] Table {$table} does not exist, skipping index {$indexName}\n";
                return;
            }

            // Check if index already exists
            if ($this->indexExists($table, $indexName)) {
                echo "[INFO] Index {$indexName} already exists on table {$table}\n";
                return;
            }

            // Create index using raw SQL
            $sql = "ALTER TABLE {$table} ADD INDEX {$indexName} ({$columns})";
            DB::statement($sql);
            
            echo "[SUCCESS] Created index {$indexName} on table {$table}\n";
        } catch (\Exception $e) {
            echo "[ERROR] Failed to create index {$indexName} on table {$table}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Check if table exists
     */
    private function tableExists($table)
    {
        try {
            $result = DB::select("SHOW TABLES LIKE '{$table}'");
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName)
    {
        try {
            $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = [
            'employees' => ['idx_employees_section_status', 'idx_employees_status_created'],
            'notifications' => ['idx_notifications_user_read', 'idx_notifications_priority_created'],
            'employee_holidays' => ['idx_holidays_status_created', 'idx_holidays_employee'],
            'employee_permissions' => ['idx_permissions_status_created', 'idx_permissions_employee'],
        ];

        foreach ($indexes as $table => $indexNames) {
            foreach ($indexNames as $indexName) {
                try {
                    if ($this->indexExists($table, $indexName)) {
                        $sql = "ALTER TABLE {$table} DROP INDEX {$indexName}";
                        DB::statement($sql);
                        echo "[INFO] Dropped index {$indexName} from table {$table}\n";
                    }
                } catch (\Exception $e) {
                    echo "[ERROR] Failed to drop index {$indexName}: " . $e->getMessage() . "\n";
                }
            }
        }
    }
};
