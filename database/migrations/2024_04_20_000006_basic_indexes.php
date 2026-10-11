<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "[INFO] Starting basic indexes migration...\n";

        // Add indexes to existing tables only
        $this->safeAddIndex('employees', 'section_id, status', 'idx_employees_section_status');
        $this->safeAddIndex('employees', 'status, created_at', 'idx_employees_status_created');
        
        $this->safeAddIndex('notifications', 'user_id, read_at', 'idx_notifications_user_read');
        $this->safeAddIndex('notifications', 'priority, created_at', 'idx_notifications_priority_created');
        
        $this->safeAddIndex('employee_holidays', 'status, created_at', 'idx_holidays_status_created');
        $this->safeAddIndex('employee_holidays', 'employee_id', 'idx_holidays_employee');
        
        $this->safeAddIndex('employee_permissions', 'status, created_at', 'idx_permissions_status_created');
        $this->safeAddIndex('employee_permissions', 'employee_id', 'idx_permissions_employee');

        echo "[INFO] Basic indexes migration completed\n";
    }

    /**
     * Safely add index with error handling
     */
    private function safeAddIndex($table, $columns, $indexName)
    {
        try {
            // Check if table exists first
            $tableCheck = DB::select("SHOW TABLES LIKE '{$table}'");
            if (empty($tableCheck)) {
                echo "[INFO] Table {$table} does not exist, skipping\n";
                return false;
            }

            // Check if index already exists
            $indexCheck = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (!empty($indexCheck)) {
                echo "[INFO] Index {$indexName} already exists on {$table}\n";
                return false;
            }

            // Add the index
            $sql = "ALTER TABLE {$table} ADD INDEX {$indexName} ({$columns})";
            DB::statement($sql);
            
            echo "[SUCCESS] Added index {$indexName} to {$table}\n";
            return true;

        } catch (\Exception $e) {
            echo "[ERROR] Failed to add index {$indexName} to {$table}: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "[INFO] Rolling back basic indexes...\n";

        $indexes = [
            'employees' => ['idx_employees_section_status', 'idx_employees_status_created'],
            'notifications' => ['idx_notifications_user_read', 'idx_notifications_priority_created'],
            'employee_holidays' => ['idx_holidays_status_created', 'idx_holidays_employee'],
            'employee_permissions' => ['idx_permissions_status_created', 'idx_permissions_employee'],
        ];

        foreach ($indexes as $table => $indexNames) {
            foreach ($indexNames as $indexName) {
                try {
                    $sql = "ALTER TABLE {$table} DROP INDEX {$indexName}";
                    DB::statement($sql);
                    echo "[INFO] Dropped index {$indexName} from {$table}\n";
                } catch (\Exception $e) {
                    echo "[ERROR] Failed to drop index {$indexName}: " . $e->getMessage() . "\n";
                }
            }
        }
    }
};
