<?php

/**
 * Apply Performance Indexes Script
 * 
 * This script safely applies performance indexes to existing database
 * with proper error handling and version compatibility.
 */

class IndexApplier
{
    private $pdo;
    private $indexes;

    public function __construct()
    {
        $this->connectDatabase();
        $this->defineIndexes();
    }

    /**
     * Connect to database
     */
    private function connectDatabase()
    {
        try {
            // Read .env file
            $envFile = __DIR__ . '/../.env';
            $content = file_get_contents($envFile);
            
            preg_match('/DB_DATABASE=(.+)/', $content, $matches);
            $database = $matches[1] ?? 'municipality_erp_system';
            
            preg_match('/DB_USERNAME=(.+)/', $content, $matches);
            $username = $matches[1] ?? 'root';
            
            preg_match('/DB_PASSWORD=(.+)/', $content, $matches);
            $password = $matches[1] ?? '';
            
            preg_match('/DB_HOST=(.+)/', $content, $matches);
            $host = $matches[1] ?? '127.0.0.1';

            $dsn = "mysql:host={$host};dbname={$database}";
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "[INFO] Connected to database: {$database}\n";
        } catch (PDOException $e) {
            echo "[ERROR] Database connection failed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Define performance indexes
     */
    private function defineIndexes()
    {
        $this->indexes = [
            'requests' => [
                'idx_requests_status_created' => 'status_id, created_at',
                'idx_requests_urgent_created' => 'is_urgent, created_at',
                'idx_requests_type_status' => 'request_type_id, status_id',
                'idx_requests_created' => 'created_at',
            ],
            'employees' => [
                'idx_employees_section_status' => 'section_id, status',
                'idx_employees_status_created' => 'status, created_at',
                'idx_employees_department' => 'department_id',
            ],
            'craft_licenses' => [
                'idx_craft_licenses_status_created' => 'status, created_at',
                'idx_craft_licenses_mokalaf' => 'mokalaf_id',
            ],
            'engineerings' => [
                'idx_engineerings_remaining_created' => 'remaining_amount, created_at',
                'idx_engineerings_national_id' => 'national_id',
            ],
            'notifications' => [
                'idx_notifications_user_read' => 'user_id, read_at',
                'idx_notifications_priority_created' => 'priority, created_at',
                'idx_notifications_type' => 'type',
            ],
            'employee_holidays' => [
                'idx_holidays_status_created' => 'status, created_at',
                'idx_holidays_employee' => 'employee_id',
            ],
            'employee_permissions' => [
                'idx_permissions_status_created' => 'status, created_at',
                'idx_permissions_employee' => 'employee_id',
            ],
            'request_payments' => [
                'idx_request_payments_request_status' => 'request_id, status',
                'idx_request_payments_created' => 'created_at',
            ],
            'payments' => [
                'idx_payments_status_created' => 'status, created_at',
                'idx_payments_method' => 'payment_method',
            ],
        ];
    }

    /**
     * Check if table exists
     */
    private function tableExists($table)
    {
        try {
            $stmt = $this->pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName)
    {
        try {
            $stmt = $this->pdo->prepare("SHOW INDEX FROM {$table} WHERE Key_name = ?");
            $stmt->execute([$indexName]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create index safely
     */
    private function createIndex($table, $indexName, $columns)
    {
        try {
            // Check if table exists
            if (!$this->tableExists($table)) {
                echo "[WARNING] Table {$table} does not exist, skipping index {$indexName}\n";
                return false;
            }

            // Check if index already exists
            if ($this->indexExists($table, $indexName)) {
                echo "[INFO] Index {$indexName} already exists on table {$table}\n";
                return true;
            }

            // Create index
            $sql = "ALTER TABLE {$table} ADD INDEX {$indexName} ({$columns})";
            $this->pdo->exec($sql);
            
            echo "[SUCCESS] Created index {$indexName} on table {$table}\n";
            return true;

        } catch (PDOException $e) {
            echo "[ERROR] Failed to create index {$indexName} on table {$table}: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Apply all indexes
     */
    public function applyAllIndexes()
    {
        echo "[INFO] Starting to apply performance indexes...\n\n";

        $totalIndexes = 0;
        $successfulIndexes = 0;

        foreach ($this->indexes as $table => $tableIndexes) {
            echo "Processing table: {$table}\n";
            
            foreach ($tableIndexes as $indexName => $columns) {
                $totalIndexes++;
                if ($this->createIndex($table, $indexName, $columns)) {
                    $successfulIndexes++;
                }
            }
            
            echo "\n";
        }

        echo "=== INDEX CREATION SUMMARY ===\n";
        echo "Total indexes: {$totalIndexes}\n";
        echo "Successful: {$successfulIndexes}\n";
        echo "Failed: " . ($totalIndexes - $successfulIndexes) . "\n";
        echo "Success rate: " . round(($successfulIndexes / $totalIndexes) * 100, 1) . "%\n\n";

        return $successfulIndexes > 0;
    }

    /**
     * Analyze existing indexes
     */
    public function analyzeIndexes()
    {
        echo "[INFO] Analyzing existing indexes...\n\n";

        foreach ($this->indexes as $table => $tableIndexes) {
            if (!$this->tableExists($table)) {
                continue;
            }

            echo "Table: {$table}\n";
            
            try {
                $stmt = $this->pdo->query("SHOW INDEX FROM {$table}");
                $existingIndexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($existingIndexes as $index) {
                    $type = $index['Key_name'] === 'PRIMARY' ? 'PRIMARY' : 'INDEX';
                    echo "  - {$index['Key_name']} ({$type}): {$index['Column_name']}\n";
                }
                
                echo "\n";
            } catch (PDOException $e) {
                echo "[ERROR] Could not analyze indexes for table {$table}: " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * Get table sizes
     */
    public function getTableSizes()
    {
        echo "[INFO] Getting table sizes...\n\n";

        try {
            $stmt = $this->pdo->query("
                SELECT 
                    table_name,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                    table_rows
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ");
            
            $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo sprintf("%-20s %-10s %-10s\n", 'Table', 'Size(MB)', 'Rows');
            echo str_repeat('-', 50) . "\n";
            
            foreach ($tables as $table) {
                echo sprintf("%-20s %-10s %-10s\n", 
                    substr($table['table_name'], 0, 20), 
                    $table['size_mb'], 
                    number_format($table['table_rows'])
                );
            }
            
            echo "\n";
        } catch (PDOException $e) {
            echo "[ERROR] Could not get table sizes: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Generate optimization report
     */
    public function generateReport($successful)
    {
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'operation' => 'apply_performance_indexes',
            'success' => $successful,
            'indexes_applied' => array_keys($this->indexes),
            'total_indexes_defined' => array_sum(array_map('count', $this->indexes)),
            'recommendations' => [
                'Monitor query performance after index creation',
                'Check slow query log for optimization opportunities',
                'Consider partitioning large tables if needed',
                'Regular index maintenance and analysis',
            ],
            'next_steps' => [
                'Test application performance',
                'Monitor slow queries',
                'Check dashboard load times',
                'Verify search functionality',
            ],
        ];

        $reportPath = __DIR__ . '/../storage/app/index_optimization_report.json';
        $jsonDir = dirname($reportPath);
        
        if (!is_dir($jsonDir)) {
            mkdir($jsonDir, 0755, true);
        }
        
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo "[INFO] Optimization report saved to: {$reportPath}\n";
        return $report;
    }

    /**
     * Run complete index application
     */
    public function run()
    {
        echo "=== Jabalia Performance Index Application ===\n\n";

        // Analyze current state
        $this->analyzeIndexes();
        $this->getTableSizes();

        // Apply indexes
        $successful = $this->applyAllIndexes();

        // Generate report
        $report = $this->generateReport($successful);

        if ($successful) {
            echo "=== INDEX APPLICATION COMPLETED SUCCESSFULLY ===\n";
        } else {
            echo "=== INDEX APPLICATION COMPLETED WITH WARNINGS ===\n";
        }

        echo "\n=== RECOMMENDATIONS ===\n";
        foreach ($report['recommendations'] as $rec) {
            echo "- {$rec}\n";
        }

        return $successful;
    }
}

// Run if executed directly
if (php_sapi_name() === 'cli') {
    $applier = new IndexApplier();
    
    // Check command line arguments
    if (in_array('--analyze', $argv)) {
        $applier->analyzeIndexes();
        $applier->getTableSizes();
    } else {
        $applier->run();
    }
}
