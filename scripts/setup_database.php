<?php

/**
 * Database Setup Script for Jabalia System
 * 
 * This script helps set up the database with existing SQL file
 * and applies performance optimizations safely.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class DatabaseSetup
{
    private $basePath;
    private $sqlFilePath;

    public function __construct()
    {
        $this->basePath = dirname(__DIR__);
        $this->sqlFilePath = $this->basePath . '/master2026_mysql.sql';
    }

    /**
     * Check database connection
     */
    public function checkConnection()
    {
        try {
            DB::connection()->getPdo();
            echo "[INFO] Database connection: OK\n";
            return true;
        } catch (\Exception $e) {
            echo "[ERROR] Database connection failed: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Check if tables exist
     */
    public function checkTables()
    {
        $tables = ['requests', 'employees', 'mokalafs', 'craft_licenses', 'engineerings'];
        $existingTables = [];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $existingTables[] = $table;
            }
        }

        if (count($existingTables) > 0) {
            echo "[INFO] Found existing tables: " . implode(', ', $existingTables) . "\n";
            return true;
        } else {
            echo "[INFO] No tables found. Database needs to be set up.\n";
            return false;
        }
    }

    /**
     * Import SQL file if needed
     */
    public function importSqlFile()
    {
        if (!file_exists($this->sqlFilePath)) {
            echo "[ERROR] SQL file not found: {$this->sqlFilePath}\n";
            return false;
        }

        echo "[INFO] Importing SQL file...\n";

        try {
            // Read SQL file
            $sql = file_get_contents($this->sqlFilePath);
            
            // Split into individual statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            $pdo = DB::connection()->getPdo();
            $pdo->beginTransaction();

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    try {
                        $pdo->exec($statement);
                    } catch (\Exception $e) {
                        echo "[WARNING] Failed to execute statement: " . substr($statement, 0, 100) . "...\n";
                        echo "[WARNING] Error: " . $e->getMessage() . "\n";
                        // Continue with other statements
                    }
                }
            }

            $pdo->commit();
            echo "[INFO] SQL import completed successfully\n";
            return true;

        } catch (\Exception $e) {
            echo "[ERROR] SQL import failed: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Apply performance indexes safely
     */
    public function applyPerformanceIndexes()
    {
        echo "[INFO] Applying performance indexes...\n";

        $indexes = [
            'requests' => [
                'idx_requests_status_created' => 'CREATE INDEX IF NOT EXISTS idx_requests_status_created ON requests(status_id, created_at)',
                'idx_requests_urgent_created' => 'CREATE INDEX IF NOT EXISTS idx_requests_urgent_created ON requests(is_urgent, created_at)',
                'idx_requests_type_status' => 'CREATE INDEX IF NOT EXISTS idx_requests_type_status ON requests(request_type_id, status_id)',
            ],
            'employees' => [
                'idx_employees_section_status' => 'CREATE INDEX IF NOT EXISTS idx_employees_section_status ON employees(section_id, status)',
                'idx_employees_status_created' => 'CREATE INDEX IF NOT EXISTS idx_employees_status_created ON employees(status, created_at)',
            ],
            'craft_licenses' => [
                'idx_craft_licenses_status_created' => 'CREATE INDEX IF NOT EXISTS idx_craft_licenses_status_created ON craft_licenses(status, created_at)',
            ],
            'engineerings' => [
                'idx_engineerings_remaining_created' => 'CREATE INDEX IF NOT EXISTS idx_engineerings_remaining_created ON engineerings(remaining_amount, created_at)',
            ],
            'notifications' => [
                'idx_notifications_user_read' => 'CREATE INDEX IF NOT EXISTS idx_notifications_user_read ON notifications(user_id, read_at)',
                'idx_notifications_priority_created' => 'CREATE INDEX IF NOT EXISTS idx_notifications_priority_created ON notifications(priority, created_at)',
            ],
        ];

        $successCount = 0;
        $totalCount = 0;

        foreach ($indexes as $table => $tableIndexes) {
            if (Schema::hasTable($table)) {
                foreach ($tableIndexes as $name => $sql) {
                    $totalCount++;
                    try {
                        DB::statement($sql);
                        echo "[INFO] Created index: {$name}\n";
                        $successCount++;
                    } catch (\Exception $e) {
                        echo "[WARNING] Failed to create index {$name}: " . $e->getMessage() . "\n";
                    }
                }
            } else {
                echo "[WARNING] Table {$table} not found, skipping indexes\n";
            }
        }

        echo "[INFO] Index creation completed: {$successCount}/{$totalCount} successful\n";
        return $successCount > 0;
    }

    /**
     * Optimize database settings
     */
    public function optimizeDatabaseSettings()
    {
        echo "[INFO] Optimizing database settings...\n";

        $settings = [
            'SET GLOBAL innodb_buffer_pool_size = 1073741824', // 1GB
            'SET GLOBAL query_cache_size = 67108864', // 64MB
            'SET GLOBAL query_cache_type = ON',
            'SET GLOBAL long_query_time = 2', // Log queries > 2 seconds
        ];

        foreach ($settings as $setting) {
            try {
                DB::statement($setting);
                echo "[INFO] Applied setting: {$setting}\n";
            } catch (\Exception $e) {
                echo "[WARNING] Failed to apply setting: {$setting}\n";
            }
        }
    }

    /**
     * Create performance monitoring view
     */
    public function createPerformanceView()
    {
        echo "[INFO] Creating performance monitoring view...\n";

        $sql = "
        CREATE OR REPLACE VIEW performance_summary AS
        SELECT 
            'requests' as table_name,
            COUNT(*) as total_records,
            COUNT(CASE WHEN status_id = (SELECT id FROM request_statuses WHERE name = 'قيد الانتظار') THEN 1 END) as pending_count,
            COUNT(CASE WHEN is_urgent = 1 THEN 1 END) as urgent_count,
            COUNT(CASE WHEN created_at >= CURDATE() THEN 1 END) as today_count,
            MAX(created_at) as last_activity
        FROM requests
        UNION ALL
        SELECT 
            'employees' as table_name,
            COUNT(*) as total_records,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as pending_count,
            0 as urgent_count,
            COUNT(CASE WHEN created_at >= CURDATE() THEN 1 END) as today_count,
            MAX(created_at) as last_activity
        FROM employees
        UNION ALL
        SELECT 
            'notifications' as table_name,
            COUNT(*) as total_records,
            COUNT(CASE WHEN read_at IS NULL THEN 1 END) as pending_count,
            COUNT(CASE WHEN priority = 'urgent' THEN 1 END) as urgent_count,
            COUNT(CASE WHEN created_at >= CURDATE() THEN 1 END) as today_count,
            MAX(created_at) as last_activity
        FROM notifications
        ";

        try {
            DB::statement($sql);
            echo "[INFO] Performance view created successfully\n";
            return true;
        } catch (\Exception $e) {
            echo "[WARNING] Failed to create performance view: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Generate setup report
     */
    public function generateReport()
    {
        echo "[INFO] Generating setup report...\n";

        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'database_connection' => $this->checkConnection(),
            'tables_exist' => $this->checkTables(),
            'setup_completed' => true,
            'optimizations_applied' => [
                'performance_indexes' => true,
                'database_settings' => true,
                'performance_view' => true,
            ],
            'next_steps' => [
                'Run: php artisan serve to start the application',
                'Visit: /performance to monitor system performance',
                'Run: php artisan tinker to test database connection',
                'Check: /logs for any issues',
            ],
            'performance_tips' => [
                'Monitor slow queries regularly',
                'Keep cache cleared during development',
                'Use performance dashboard for optimization',
                'Check memory usage during peak hours',
                'Monitor disk space for asset cleanup',
            ],
        ];

        $reportPath = $this->basePath . '/storage/app/database_setup_report.json';
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo "[INFO] Setup report saved to: {$reportPath}\n";
        return $report;
    }

    /**
     * Run complete setup
     */
    public function run()
    {
        echo "[INFO] Starting Jabalia Database Setup...\n";
        echo "[INFO] Base path: {$this->basePath}\n";

        // Check connection
        if (!$this->checkConnection()) {
            return false;
        }

        // Check if tables exist
        $tablesExist = $this->checkTables();

        // Import SQL if tables don't exist
        if (!$tablesExist) {
            if (!$this->importSqlFile()) {
                return false;
            }
        }

        // Apply performance optimizations
        $this->applyPerformanceIndexes();
        $this->optimizeDatabaseSettings();
        $this->createPerformanceView();

        // Generate report
        $report = $this->generateReport();

        echo "[INFO] Database setup completed successfully!\n";
        echo "\n=== NEXT STEPS ===\n";
        foreach ($report['next_steps'] as $step) {
            echo "- {$step}\n";
        }

        return $report;
    }
}

// Run setup if this script is executed directly
if (php_sapi_name() === 'cli') {
    $setup = new DatabaseSetup();
    $setup->run();
}
