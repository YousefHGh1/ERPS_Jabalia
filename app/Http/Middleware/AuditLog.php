<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuditLog
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // milliseconds

        // Log sensitive actions
        $this->logAction($request, $response, $duration);

        return $response;
    }

    /**
     * Log the action if it's sensitive.
     */
    protected function logAction(Request $request, $response, float $duration): void
    {
        $sensitiveRoutes = [
            'employees.store',
            'employees.update',
            'employees.destroy',
            'requests.store',
            'requests.update',
            'requests.destroy',
            'users.store',
            'users.update',
            'users.destroy',
            'auth.login',
            'auth.logout',
        ];

        $routeName = $request->route() ? $request->route()->getName() : 'unknown';
        
        if (in_array($routeName, $sensitiveRoutes) || $this->isSensitiveAction($request)) {
            $logData = [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'route' => $routeName,
                'url' => $request->fullUrl(),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'request_data' => $this->sanitizeRequestData($request->all()),
                'timestamp' => now()->toISOString(),
            ];

            Log::channel('audit')->info('Sensitive action performed', $logData);
        }
    }

    /**
     * Determine if the action is sensitive.
     */
    protected function isSensitiveAction(Request $request): bool
    {
        $sensitiveKeywords = [
            'password', 'delete', 'destroy', 'ban', 'suspend', 
            'approve', 'reject', 'admin', 'permission', 'role'
        ];

        $url = strtolower($request->fullUrl());
        $method = strtolower($request->method());

        foreach ($sensitiveKeywords as $keyword) {
            if (str_contains($url, $keyword) && in_array($method, ['post', 'put', 'delete'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitize request data for logging.
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveFields = [
            'password', 'password_confirmation', 'current_password',
            'credit_card', 'ssn', 'national_id', 'bank_account'
        ];

        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveFields)) {
                $data[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeRequestData($value);
            }
        }

        return $data;
    }
}
