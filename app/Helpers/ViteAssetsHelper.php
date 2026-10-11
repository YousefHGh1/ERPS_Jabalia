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