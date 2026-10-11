<?php

/**
 * Apply Indexes to New Tables
 * 
 * This script applies performance indexes to newly created tables
 */

class NewIndexApplier
{
    private $pdo;

    public function __construct()
    {
        $this->connectDatabase();
    }

    /**
     * Connect to database
     */
    private function connectDatabase()
    {
        try {
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
     * Apply indexes to new tables
     */
    public function applyIndexes()
    {
        echo "[INFO] Applying indexes to newly created tables...\n\n";

        // Requests table indexes
        $this->addIndexIfNotExists('requests', 'idx_requests_status_created', 'status_id, created_at');
        $this->addIndexIfNotExists('requests', 'idx_requests_urgent_created', 'is_urgent, created_at');
        $this->addIndexIfNotExists('requests', 'idx_requests_type_status', 'request_type_id, status_id');
        $this->addIndexIfNotExists('requests', 'idx_requests_created', 'created_at');
        $this->addIndexIfNotExists('requests', 'idx_requests_mokalaf', 'mokalaf_id');

        // Craft licenses indexes
        $this->addIndexIfNotExists('craft_licenses', 'idx_craft_licenses_status_created', 'status, created_at');
        $this->addIndexIfNotExists('craft_licenses', 'idx_craft_licenses_mokalaf', 'mokalaf_id');
        $this->addIndexIfNotExists('craft_licenses', 'idx_craft_licenses_type', 'craft_type');

        // Engineerings indexes
        $this->addIndexIfNotExists('engineerings', 'idx_engineerings_remaining_created', 'remaining_amount, created_at');
        $this->addIndexIfNotExists('engineerings', 'idx_engineerings_national_id', 'national_id');
        $this->addIndexIfNotExists('engineerings', 'idx_engineerings_status', 'status');

        // Request payments indexes
        $this->addIndexIfNotExists('request_payments', 'idx_request_payments_request_status', 'request_id, status');
        $this->addIndexIfNotExists('request_payments', 'idx_request_payments_created', 'created_at');
        $this->addIndexIfNotExists('request_payments', 'idx_request_payments_method', 'payment_method');

        // Payments indexes
        $this->addIndexIfNotExists('payments', 'idx_payments_status_created', 'status, created_at');
        $this->addIndexIfNotExists('payments', 'idx_payments_method', 'payment_method');
        $this->addIndexIfNotExists('payments', 'idx_payments_craft_license', 'craft_license_id');

        // Mokalafs indexes
        $this->addIndexIfNotExists('mokalafs', 'idx_mokalafs_identity', 'identity_number');
        $this->addIndexIfNotExists('mokalafs', 'idx_mokalafs_license', 'license_number');
        $this->addIndexIfNotExists('mokalafs', 'idx_mokalafs_name', 'name');

        echo "\n[INFO] Index application completed!\n";
        $this->showSummary();
    }

    /**
     * Add index if not exists
     */
    private function addIndexIfNotExists($table, $indexName, $columns)
    {
        try {
            // Check if table exists
            $tableCheck = $this->pdo->query("SHOW TABLES LIKE '{$table}'")->fetchAll();
            if (empty($tableCheck)) {
                echo "[INFO] Table {$table} does not exist, skipping\n";
                return false;
            }

            // Check if index already exists
            $indexCheck = $this->pdo->prepare("SHOW INDEX FROM {$table} WHERE Key_name = ?");
            $indexCheck->execute([$indexName]);
            if ($indexCheck->rowCount() > 0) {
                echo "[INFO] Index {$indexName} already exists on {$table}\n";
                return false;
            }

            // Create index
            $sql = "ALTER TABLE {$table} ADD INDEX {$indexName} ({$columns})";
            $this->pdo->exec($sql);
            
            echo "[SUCCESS] Created index {$indexName} on {$table}\n";
            return true;

        } catch (PDOException $e) {
            echo "[ERROR] Failed to create index {$indexName} on {$table}: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Show summary of created indexes
     */
    private function showSummary()
    {
        echo "\n=== INDEX SUMMARY ===\n";

        $tables = ['requests', 'craft_licenses', 'engineerings', 'request_payments', 'payments', 'mokalafs'];
        
        foreach ($tables as $table) {
            try {
                $indexes = $this->pdo->query("SHOW INDEX FROM {$table}")->fetchAll();
                
                echo "\nTable: {$table}\n";
                foreach ($indexes as $index) {
                    $type = $index['Key_name'] === 'PRIMARY' ? 'PRIMARY' : 'INDEX';
                    echo "  - {$index['Key_name']} ({$type}): {$index['Column_name']}\n";
                }
            } catch (PDOException $e) {
                echo "[ERROR] Could not get indexes for {$table}: " . $e->getMessage() . "\n";
            }
        }

        echo "\n=== OPTIMIZATION COMPLETE ===\n";
        echo "Performance indexes have been applied to all tables.\n";
        echo "You should now see improved query performance.\n\n";

        echo "=== NEXT STEPS ===\n";
        echo "1. Test the application performance\n";
        echo "2. Check the dashboard load times\n";
        echo "3. Monitor slow query logs\n";
        echo "4. Visit /performance for monitoring\n\n";
    }
}

// Run if executed directly
if (php_sapi_name() === 'cli') {
    $applier = new NewIndexApplier();
    $applier->applyIndexes();
}
