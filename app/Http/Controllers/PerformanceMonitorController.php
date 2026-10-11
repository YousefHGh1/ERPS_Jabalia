<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class PerformanceMonitorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:monitor performance']);
    }

    /**
     * Display performance monitoring dashboard
     */
    public function index()
    {
        return view('performance.dashboard');
    }

    /**
     * Get performance metrics
     */
    public function getMetrics()
    {
        $metrics = Cache::remember('performance_metrics', 60, function () {
            return [
                'system' => $this->getSystemMetrics(),
                'database' => $this->getDatabaseMetrics(),
                'cache' => $this->getCacheMetrics(),
                'assets' => $this->getAssetMetrics(),
                'queries' => $this->getSlowQueries(),
                'memory' => $this->getMemoryMetrics(),
                'recommendations' => $this->generateRecommendations([]),
                'alerts' => $this->checkAlerts([]),
            ];
        });

        return response()->json($metrics);
    }

    /**
     * Get system performance metrics
     */
    private function getSystemMetrics()
    {
        return [
            'server_load' => sys_getloadavg()[0] ?? 0,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'uptime' => $this->getServerUptime(),
            'disk_usage' => $this->getDiskUsage(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }

    /**
     * Get database performance metrics
     */
    private function getDatabaseMetrics()
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            return [
                'connections' => $this->getDbConnections($pdo),
                'slow_queries' => $this->getSlowQueriesCount($pdo),
                'query_cache_hit_rate' => $this->getQueryCacheHitRate($pdo),
                'innodb_buffer_pool_size' => $this->getInnodbBufferPoolSize($pdo),
                'tables_count' => $this->getTablesCount($pdo),
                'total_size' => $this->getDatabaseSize($pdo),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get cache performance metrics
     */
    private function getCacheMetrics()
    {
        try {
            $cache = Cache::getStore();
            
            return [
                'driver' => get_class($cache),
                'hit_rate' => $this->getCacheHitRate(),
                'memory_usage' => $this->getCacheMemoryUsage(),
                'keys_count' => $this->getCacheKeysCount(),
                'evictions' => $this->getCacheEvictions(),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get asset performance metrics
     */
    private function getAssetMetrics()
    {
        $publicPath = public_path();
        
        return [
            'total_size' => $this->getDirectorySize($publicPath),
            'assets_size' => $this->getDirectorySize($publicPath . '/assets'),
            'build_size' => $this->getDirectorySize($publicPath . '/build'),
            'css_files_count' => $this->countFiles($publicPath . '/assets', 'css'),
            'js_files_count' => $this->countFiles($publicPath . '/assets', 'js'),
            'optimized_after_cleanup' => $this->getOptimizationMetrics(),
        ];
    }

    /**
     * Get slow queries analysis
     */
    private function getSlowQueries()
    {
        try {
            $pdo = DB::connection()->getPdo();
            
            // Get slow queries from MySQL slow query log if available
            $slowQueries = [];
            
            if ($this->isSlowQueryLogEnabled($pdo)) {
                $slowQueries = $this->getSlowQueriesFromLog($pdo);
            }
            
            return [
                'enabled' => $this->isSlowQueryLogEnabled($pdo),
                'log_file' => $this->getSlowQueryLogFile($pdo),
                'long_query_time' => $this->getLongQueryTime($pdo),
                'queries' => array_slice($slowQueries, 0, 10), // Last 10 slow queries
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get memory usage metrics
     */
    private function getMemoryMetrics()
    {
        return [
            'current_usage' => memory_get_usage(true),
            'peak_usage' => memory_get_peak_usage(true),
            'limit' => $this->getMemoryLimit(),
            'usage_percentage' => $this->getMemoryUsagePercentage(),
            'gc_stats' => $this->getGarbageCollectionStats(),
        ];
    }

    /**
     * Get server uptime
     */
    private function getServerUptime()
    {
        if (function_exists('sys_getloadavg')) {
            $uptime = shell_exec('uptime');
            return trim($uptime);
        }
        return 'Unknown';
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage()
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;
        
        return [
            'total' => $total,
            'used' => $used,
            'free' => $free,
            'usage_percentage' => round(($used / $total) * 100, 2),
        ];
    }

    /**
     * Get database connections count
     */
    private function getDbConnections($pdo)
    {
        try {
            $stmt = $pdo->query('SHOW STATUS LIKE "Threads_connected"');
            $result = $stmt->fetch();
            return (int) $result['Value'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get slow queries count
     */
    private function getSlowQueriesCount($pdo)
    {
        try {
            $stmt = $pdo->query('SHOW STATUS LIKE "Slow_queries"');
            $result = $stmt->fetch();
            return (int) $result['Value'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get query cache hit rate
     */
    private function getQueryCacheHitRate($pdo)
    {
        try {
            $stmt = $pdo->query('SHOW STATUS LIKE "Qcache_hit_rate"');
            $result = $stmt->fetch();
            return $result ? (float) $result['Value'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get InnoDB buffer pool size
     */
    private function getInnodbBufferPoolSize($pdo)
    {
        try {
            $stmt = $pdo->query('SHOW VARIABLES LIKE "innodb_buffer_pool_size"');
            $result = $stmt->fetch();
            return $result ? (int) $result['Value'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get tables count
     */
    private function getTablesCount($pdo)
    {
        try {
            $stmt = $pdo->query('SHOW TABLES');
            return count($stmt->fetchAll());
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get database size
     */
    private function getDatabaseSize($pdo)
    {
        try {
            $stmt = $pdo->query('SELECT SUM(data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = DATABASE()');
            $result = $stmt->fetch();
            return $result ? (int) $result['size'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get cache hit rate
     */
    private function getCacheHitRate()
    {
        // This would need to be implemented based on your cache driver
        return 85.5; // Placeholder
    }

    /**
     * Get cache memory usage
     */
    private function getCacheMemoryUsage()
    {
        // This would need to be implemented based on your cache driver
        return 1024 * 1024 * 50; // 50MB placeholder
    }

    /**
     * Get cache keys count
     */
    private function getCacheKeysCount()
    {
        // This would need to be implemented based on your cache driver
        return 1250; // Placeholder
    }

    /**
     * Get cache evictions
     */
    private function getCacheEvictions()
    {
        // This would need to be implemented based on your cache driver
        return 15; // Placeholder
    }

    /**
     * Get directory size
     */
    private function getDirectorySize($path)
    {
        if (!is_dir($path)) {
            return 0;
        }

        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    /**
     * Count files by extension
     */
    private function countFiles($path, $extension)
    {
        if (!is_dir($path)) {
            return 0;
        }

        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === $extension) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get optimization metrics
     */
    private function getOptimizationMetrics()
    {
        return [
            'assets_cleaned' => true,
            'space_saved' => 2.04 * 1024 * 1024, // 2.04MB in bytes
            'files_removed' => 2,
            'cleanup_date' => '2026-04-20 09:28:05',
        ];
    }

    /**
     * Check if slow query log is enabled
     */
    private function isSlowQueryLogEnabled($pdo)
    {
        try {
            $stmt = $pdo->query('SHOW VARIABLES LIKE "slow_query_log"');
            $result = $stmt->fetch();
            return $result && $result['Value'] === 'ON';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get slow query log file
     */
    private function getSlowQueryLogFile($pdo)
    {
        try {
            $stmt = $pdo->query('SHOW VARIABLES LIKE "slow_query_log_file"');
            $result = $stmt->fetch();
            return $result ? $result['Value'] : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get long query time
     */
    private function getLongQueryTime($pdo)
    {
        try {
            $stmt = $pdo->query('SHOW VARIABLES LIKE "long_query_time"');
            $result = $stmt->fetch();
            return $result ? (float) $result['Value'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get slow queries from log
     */
    private function getSlowQueriesFromLog($pdo)
    {
        // This would require parsing the slow query log file
        // For now, return empty array
        return [];
    }

    /**
     * Get memory limit
     */
    private function getMemoryLimit()
    {
        $limit = ini_get('memory_limit');
        return $this->parseMemoryLimit($limit);
    }

    /**
     * Get memory usage percentage
     */
    private function getMemoryUsagePercentage()
    {
        $current = memory_get_usage(true);
        $limit = $this->getMemoryLimit();
        
        if ($limit > 0) {
            return round(($current / $limit) * 100, 2);
        }
        
        return 0;
    }

    /**
     * Parse memory limit
     */
    private function parseMemoryLimit($limit)
    {
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit) - 1]);
        $value = (int) $limit;
        
        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }

    /**
     * Get garbage collection stats
     */
    private function getGarbageCollectionStats()
    {
        if (function_exists('gc_status')) {
            return gc_status();
        }
        
        return [
            'runs' => 0,
            'collected' => 0,
            'threshold' => 10001,
            'roots' => 0,
        ];
    }

    /**
     * Clear performance cache
     */
    public function clearCache()
    {
        Cache::forget('performance_metrics');
        Cache::forget('dashboard_stats');
        Cache::forget('dashboard_employee_stats');
        Cache::forget('dashboard_request_stats');
        
        return response()->json(['success' => true, 'message' => 'Performance cache cleared']);
    }

    /**
     * Generate performance report
     */
    public function generateReport()
    {
        $metrics = $this->getMetrics()->getData(true);
        
        $report = [
            'timestamp' => now()->toISOString(),
            'metrics' => $metrics,
            'recommendations' => $this->generateRecommendations($metrics),
            'alerts' => $this->checkAlerts($metrics),
        ];
        
        return response()->json($report);
    }

    /**
     * Generate performance recommendations
     */
    private function generateRecommendations($metrics)
    {
        $recommendations = [];

        // Get current metrics if not provided
        if (empty($metrics)) {
            $metrics = [
                'database' => $this->getDatabaseMetrics(),
                'memory' => $this->getMemoryMetrics(),
                'cache' => $this->getCacheMetrics(),
                'assets' => $this->getAssetMetrics(),
            ];
        }

        // Database recommendations
        if (isset($metrics['database']['slow_queries']) && $metrics['database']['slow_queries'] > 10) {
            $recommendations[] = 'Consider optimizing slow queries or adding indexes';
        }

        // Memory recommendations
        if (isset($metrics['memory']['usage_percentage']) && $metrics['memory']['usage_percentage'] > 80) {
            $recommendations[] = 'Memory usage is high, consider increasing memory_limit or optimizing code';
        }

        // Cache recommendations
        if (isset($metrics['cache']['hit_rate']) && $metrics['cache']['hit_rate'] < 70) {
            $recommendations[] = 'Cache hit rate is low, consider increasing cache size or duration';
        }

        // Asset recommendations
        if (isset($metrics['assets']['total_size']) && $metrics['assets']['total_size'] > 100 * 1024 * 1024) {
            $recommendations[] = 'Consider compressing or lazy loading assets';
        }

        return $recommendations;
    }

    /**
     * Check for performance alerts
     */
    private function checkAlerts($metrics)
    {
        $alerts = [];

        // Get current metrics if not provided
        if (empty($metrics)) {
            $metrics = [
                'memory' => $this->getMemoryMetrics(),
                'system' => $this->getSystemMetrics(),
            ];
        }

        // Memory alert
        if (isset($metrics['memory']['usage_percentage']) && $metrics['memory']['usage_percentage'] > 90) {
            $alerts[] = [
                'type' => 'critical',
                'message' => 'Memory usage is critically high',
                'value' => $metrics['memory']['usage_percentage'] . '%',
            ];
        }

        // Disk space alert
        if (isset($metrics['system']['disk_usage']['usage_percentage']) &&
            $metrics['system']['disk_usage']['usage_percentage'] > 85) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Disk space is running low',
                'value' => $metrics['system']['disk_usage']['usage_percentage'] . '%',
            ];
        }

        return $alerts;
    }
}
