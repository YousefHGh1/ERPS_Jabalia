<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Dynamic CSP based on user role and page type
        $csp = $this->generateCSP($request);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    /**
     * Generate CSP based on context.
     */
    protected function generateCSP(Request $request): string
    {
        $user = $request->user();
        $route = $request->route();

        // Base CSP
        $directives = [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline' 'unsafe-eval'",
            'style-src' => "'self' 'unsafe-inline'",
            'img-src' => "'self' data: https:",
            'font-src' => "'self'",
            'connect-src' => "'self'",
            'frame-src' => "'none'",
            'object-src' => "'none'",
            'base-uri' => "'self'",
            'form-action' => "'self'",
        ];

        // Allow external resources for specific pages
        if ($route && $this->needsExternalResources($route->getName())) {
            $directives['script-src'] .= ' https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com';
            $directives['style-src'] .= ' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com';
            $directives['font-src'] .= ' https://fonts.gstatic.com https://cdnjs.cloudflare.com';
        }

        // Allow WebSocket for real-time features
        if ($user && $user->hasPermissionTo('access_real_time')) {
            $directives['connect-src'] .= ' ws: wss:';
        }

        // Allow file uploads for specific roles
        if ($user && $user->hasPermissionTo('upload_files')) {
            $directives['script-src'] .= ' blob:';
        }

        // Allow iframes for admin dashboard (if needed)
        if ($user && $user->hasRole('admin')) {
            $directives['frame-src'] = "'self'";
        }

        $csp = [];
        foreach ($directives as $name => $value) {
            $csp[] = $name . ' ' . $value;
        }

        // Report URI in development/testing
        if (app()->environment('local', 'testing')) {
            $csp[] = "report-uri /csp-report";
        }

        return implode('; ', $csp);
    }

    /**
     * Check if route needs external resources.
     */
    protected function needsExternalResources(?string $routeName): bool
    {
        $routesWithExternalResources = [
            'home',
            'dashboard',
            'employees.index',
            'requests.index',
            'reports.index',
        ];

        return in_array($routeName, $routesWithExternalResources);
    }
}
