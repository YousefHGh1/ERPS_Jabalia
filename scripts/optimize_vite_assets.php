<?php

/**
 * Vite Assets Optimization Script
 * 
 * This script helps optimize Vite build configuration and asset loading
 * for better performance in the Jabalia system.
 */

require_once __DIR__ . '/../vendor/autoload.php';

class ViteAssetsOptimizer
{
    private $basePath;
    private $viteConfigPath;
    private $packageJsonPath;

    public function __construct()
    {
        $this->basePath = dirname(__DIR__);
        $this->viteConfigPath = $this->basePath . '/vite.config.js';
        $this->packageJsonPath = $this->basePath . '/package.json';
    }

    /**
     * Optimize Vite configuration
     */
    public function optimizeViteConfig()
    {
        $optimizedConfig = <<<'JS'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/dashboard.css',
                'resources/js/dashboard.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['jquery', 'bootstrap'],
                    charts: ['chart.js'],
                    utils: ['./resources/js/utils.js'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
    },
    server: {
        hmr: {
            overlay: false,
        },
    },
});
JS;

        file_put_contents($this->viteConfigPath, $optimizedConfig);
        echo "[INFO] Vite configuration optimized\n";
    }

    /**
     * Update package.json with optimization dependencies
     */
    public function updatePackageJson()
    {
        if (!file_exists($this->packageJsonPath)) {
            echo "[ERROR] package.json not found\n";
            return;
        }

        $package = json_decode(file_get_contents($this->packageJsonPath), true);
        
        // Add optimization scripts
        $package['scripts']['build:prod'] = 'npm run build && npm run optimize:assets';
        $package['scripts']['optimize:assets'] = 'php scripts/optimize_vite_assets.php';
        $package['scripts']['analyze:bundle'] = 'npx vite-bundle-analyzer dist/assets';
        
        // Add optimization dependencies
        $package['devDependencies']['vite-bundle-analyzer'] = '^0.7.0';
        $package['devDependencies']['rollup-plugin-visualizer'] = '^5.9.0';
        $package['devDependencies']['terser'] = '^5.19.0';
        
        file_put_contents($this->packageJsonPath, json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "[INFO] package.json updated with optimization dependencies\n";
    }

    /**
     * Create optimized asset loading helper
     */
    public function createAssetHelper()
    {
        $helperCode = <<<'PHP'
<?php

if (!function_exists('vite_asset')) {
    /**
     * Get optimized Vite asset URL
     */
    function vite_asset(string $path): string
    {
        static $manifest = null;
        
        if ($manifest === null) {
            $manifestPath = public_path('build/manifest.json');
            
            if (!file_exists($manifestPath)) {
                return asset($path);
            }
            
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }
        
        $key = 'resources/' . ltrim($path, '/');
        
        return isset($manifest[$key])
            ? asset('build/' . $manifest[$key]['file'])
            : asset($path);
    }
}

if (!function_exists('vite_preload')) {
    /**
     * Generate preload links for critical assets
     */
    function vite_preload(array $assets): string
    {
        static $manifest = null;
        
        if ($manifest === null) {
            $manifestPath = public_path('build/manifest.json');
            
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
            }
        }
        
        $links = [];
        
        foreach ($assets as $asset) {
            $key = 'resources/' . ltrim($asset, '/');
            
            if (isset($manifest[$key])) {
                $file = $manifest[$key]['file'];
                $type = pathinfo($file, PATHINFO_EXTENSION);
                
                if ($type === 'css') {
                    $links[] = '<link rel="preload" href="' . asset('build/' . $file) . '" as="style">';
                } elseif ($type === 'js') {
                    $links[] = '<link rel="preload" href="' . asset('build/' . $file) . '" as="script">';
                }
            }
        }
        
        return implode("\n", $links);
    }
}

if (!function_exists('critical_css')) {
    /**
     * Load critical CSS inline
     */
    function critical_css(string $path): string
    {
        $cssPath = public_path('build/' . ltrim($path, '/'));
        
        if (!file_exists($cssPath)) {
            return '';
        }
        
        $css = file_get_contents($cssPath);
        
        // Extract critical CSS (simplified version)
        $critical = substr($css, 0, 2000); // First 2KB as critical
        
        return '<style>' . $critical . '</style>';
    }
}
PHP;

        $helperPath = $this->basePath . '/app/Helpers/ViteAssetsHelper.php';
        file_put_contents($helperPath, $helperCode);
        echo "[INFO] Vite assets helper created\n";
    }

    /**
     * Create optimized Blade components
     */
    public function createBladeComponents()
    {
        $componentsDir = $this->basePath . '/resources/views/components/assets';
        
        if (!is_dir($componentsDir)) {
            mkdir($componentsDir, 0755, true);
        }

        // Vite Styles Component
        $stylesComponent = <<<'BLADE'
@props(['css' => [], 'critical' => false])

@if($critical)
    @foreach($css as $file)
        {!! critical_css($file) !!}
    @endforeach
@endif

@foreach($css as $file)
    <link rel="stylesheet" href="{{ vite_asset($file) }}">
@endforeach
BLADE;

        file_put_contents($componentsDir . '/vite-styles.blade.php', $stylesComponent);

        // Vite Scripts Component
        $scriptsComponent = <<<'BLADE'
@props(['js' => [], 'defer' => true, 'async' => false])

{!! vite_preload($js) !!}

@foreach($js as $file)
    <script src="{{ vite_asset($file) }}" 
        @if($defer) defer @endif
        @if($async) async @endif>
    </script>
@endforeach
BLADE;

        file_put_contents($componentsDir . '/vite-scripts.blade.php', $scriptsComponent);

        echo "[INFO] Blade components created\n";
    }

    /**
     * Generate performance report
     */
    public function generatePerformanceReport()
    {
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'optimizations' => [
                'vite_config_optimized' => true,
                'package_json_updated' => true,
                'asset_helper_created' => true,
                'blade_components_created' => true,
            ],
            'recommendations' => [
                'Use vite_preload() for critical above-the-fold assets',
                'Implement critical_css() for faster initial render',
                'Split vendor code into separate chunks',
                'Enable gzip compression on server',
                'Set proper cache headers for static assets',
                'Consider lazy loading for non-critical JavaScript',
            ],
            'next_steps' => [
                'Run npm install to update dependencies',
                'Test the new vite_asset() helper functions',
                'Update existing Blade files to use new components',
                'Monitor bundle sizes with npm run analyze:bundle',
            ]
        ];

        $reportPath = $this->basePath . '/storage/app/vite_optimization_report.json';
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo "[INFO] Performance report saved to: {$reportPath}\n";
        
        return $report;
    }

    /**
     * Run complete optimization
     */
    public function run()
    {
        echo "[INFO] Starting Vite assets optimization...\n";
        
        $this->optimizeViteConfig();
        $this->updatePackageJson();
        $this->createAssetHelper();
        $this->createBladeComponents();
        
        $report = $this->generatePerformanceReport();
        
        echo "[INFO] Vite optimization completed successfully!\n";
        echo "[INFO] Next steps:\n";
        foreach ($report['next_steps'] as $step) {
            echo "  - {$step}\n";
        }
        
        return $report;
    }
}

// Run optimization if this script is executed directly
if (php_sapi_name() === 'cli') {
    $optimizer = new ViteAssetsOptimizer();
    $optimizer->run();
}
