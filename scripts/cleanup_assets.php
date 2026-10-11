<?php

/**
 * Asset Cleanup Script
 * 
 * This script helps identify and clean up unused CSS and JS files
 * from the public/assets directory to improve performance.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AssetCleanup
{
    private $basePath;
    private $assetsPath;
    private $viewsPath;
    private $usedAssets = [];
    private $unusedAssets = [];

    public function __construct()
    {
        $this->basePath = dirname(__DIR__);
        $this->assetsPath = $this->basePath . '/public/assets';
        $this->viewsPath = $this->basePath . '/resources/views';
    }

    /**
     * Scan all Blade files for asset references
     */
    public function scanViewsForAssets()
    {
        $this->info("Scanning Blade files for asset references...");

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->viewsPath)
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                $this->extractAssetsFromContent($content);
            }
        }

        $this->info("Found " . count($this->usedAssets) . " referenced assets");
    }

    /**
     * Extract asset references from file content
     */
    private function extractAssetsFromContent($content)
    {
        // Match CSS files
        preg_match_all('/["\']assets\/css\/([^"\']+\.css)["\']/', $content, $cssMatches);
        foreach ($cssMatches[1] as $cssFile) {
            $this->usedAssets[] = "css/{$cssFile}";
        }

        // Match JS files
        preg_match_all('/["\']assets\/js\/([^"\']+\.js)["\']/', $content, $jsMatches);
        foreach ($jsMatches[1] as $jsFile) {
            $this->usedAssets[] = "js/{$jsFile}";
        }

        // Match asset() helper calls
        preg_match_all('/asset\(["\']([^"\']+)["\']\)/', $content, $assetMatches);
        foreach ($assetMatches[1] as $asset) {
            if (Str::startsWith($asset, 'assets/')) {
                $relativePath = substr($asset, 7); // Remove 'assets/'
                $this->usedAssets[] = $relativePath;
            }
        }

        // Match mix() helper calls
        preg_match_all('/mix\(["\']([^"\']+)["\']\)/', $content, $mixMatches);
        foreach ($mixMatches[1] as $asset) {
            if (Str::startsWith($asset, 'assets/')) {
                $relativePath = substr($asset, 7); // Remove 'assets/'
                $this->usedAssets[] = $relativePath;
            }
        }
    }

    /**
     * Find all asset files in public/assets
     */
    public function findAllAssetFiles()
    {
        $this->info("Scanning public/assets directory...");

        $allAssets = [];

        // CSS files
        $cssIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->assetsPath . '/css')
        );
        foreach ($cssIterator as $file) {
            if ($file->getExtension() === 'css') {
                $relativePath = 'css/' . $file->getFilename();
                $allAssets[] = $relativePath;
            }
        }

        // JS files
        $jsIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->assetsPath . '/js')
        );
        foreach ($jsIterator as $file) {
            if ($file->getExtension() === 'js') {
                $relativePath = 'js/' . $file->getFilename();
                $allAssets[] = $relativePath;
            }
        }

        return $allAssets;
    }

    /**
     * Identify unused assets
     */
    public function findUnusedAssets()
    {
        $allAssets = $this->findAllAssetFiles();
        $this->usedAssets = array_unique($this->usedAssets);

        foreach ($allAssets as $asset) {
            if (!in_array($asset, $this->usedAssets)) {
                $this->unusedAssets[] = $asset;
            }
        }

        $this->info("Found " . count($this->unusedAssets) . " potentially unused assets");
    }

    /**
     * Generate cleanup report
     */
    public function generateReport()
    {
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'total_assets_scanned' => count($this->findAllAssetFiles()),
            'used_assets' => count($this->usedAssets),
            'unused_assets' => count($this->unusedAssets),
            'unused_css' => [],
            'unused_js' => [],
            'large_unused_files' => []
        ];

        foreach ($this->unusedAssets as $asset) {
            $fullPath = $this->assetsPath . '/' . $asset;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                $fileInfo = [
                    'path' => $asset,
                    'size_bytes' => $size,
                    'size_human' => $this->formatBytes($size)
                ];

                if (Str::endsWith($asset, '.css')) {
                    $report['unused_css'][] = $fileInfo;
                } elseif (Str::endsWith($asset, '.js')) {
                    $report['unused_js'][] = $fileInfo;
                }

                if ($size > 1024 * 100) { // Files larger than 100KB
                    $report['large_unused_files'][] = $fileInfo;
                }
            }
        }

        return $report;
    }

    /**
     * Save report to file
     */
    public function saveReport()
    {
        $report = $this->generateReport();
        $reportPath = $this->basePath . '/storage/app/asset_cleanup_report.json';
        
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info("Report saved to: {$reportPath}");
        $this->info("Total unused assets: {$report['unused_assets']}");
        $this->info("Potential space savings: " . $this->calculateSpaceSavings($report));
        
        return $report;
    }

    /**
     * Calculate potential space savings
     */
    private function calculateSpaceSavings($report)
    {
        $totalBytes = 0;
        
        foreach (array_merge($report['unused_css'], $report['unused_js']) as $file) {
            $totalBytes += $file['size_bytes'];
        }
        
        return $this->formatBytes($totalBytes);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Create backup of unused assets before deletion
     */
    public function createBackup()
    {
        if (empty($this->unusedAssets)) {
            return;
        }

        $backupPath = $this->basePath . '/storage/app/unused_assets_backup_' . date('Y-m-d_H-i-s');
        
        foreach ($this->unusedAssets as $asset) {
            $sourcePath = $this->assetsPath . '/' . $asset;
            $targetPath = $backupPath . '/' . $asset;
            
            if (file_exists($sourcePath)) {
                $targetDir = dirname($targetPath);
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                copy($sourcePath, $targetPath);
            }
        }
        
        $this->info("Backup created at: {$backupPath}");
    }

    /**
     * Delete unused assets
     */
    public function deleteUnusedAssets($dryRun = true)
    {
        $deletedCount = 0;
        $totalSize = 0;

        foreach ($this->unusedAssets as $asset) {
            $fullPath = $this->assetsPath . '/' . $asset;
            
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                $totalSize += $size;
                
                if (!$dryRun) {
                    unlink($fullPath);
                    $this->info("Deleted: {$asset} (" . $this->formatBytes($size) . ")");
                } else {
                    $this->info("Would delete: {$asset} (" . $this->formatBytes($size) . ")");
                }
                
                $deletedCount++;
            }
        }

        if ($dryRun) {
            $this->info("DRY RUN: Would delete {$deletedCount} files, freeing " . $this->formatBytes($totalSize));
        } else {
            $this->info("Deleted {$deletedCount} files, freed " . $this->formatBytes($totalSize));
        }
    }

    /**
     * Print info message
     */
    private function info($message)
    {
        echo "[INFO] {$message}\n";
    }

    /**
     * Run the complete cleanup process
     */
    public function run($dryRun = true)
    {
        $this->scanViewsForAssets();
        $this->findUnusedAssets();
        $report = $this->saveReport();
        
        if (!empty($this->unusedAssets)) {
            $this->createBackup();
            $this->deleteUnusedAssets($dryRun);
        } else {
            $this->info("No unused assets found.");
        }
        
        return $report;
    }
}

// Run the cleanup if this script is executed directly
if (php_sapi_name() === 'cli') {
    $dryRun = in_array('--dry-run', $argv) || in_array('-d', $argv);
    
    $cleanup = new AssetCleanup();
    $cleanup->run($dryRun);
}
