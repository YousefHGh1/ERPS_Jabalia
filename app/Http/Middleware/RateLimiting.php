<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class RateLimiting
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);
        
        // استخدام Redis للتحقق من عدد المحاولات
        if ($this->hasTooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        // زيادة عدد المحاولات
        $this->hit($key, $decayMinutes);

        $response = $next($request);

        // إضافة headers للتحكم في rate limiting
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - $this->attempts($key)));
        $response->headers->set('X-RateLimit-Reset', $this->availableIn($key));

        return $response;
    }

    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(
            $request->ip() . '|' . $request->userAgent() . '|' . $request->route()->getName()
        );
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     */
    protected function hasTooManyAttempts(string $key, int $maxAttempts, int $decayMinutes): bool
    {
        $attempts = $this->attempts($key);
        $timeWindow = $decayMinutes * 60;
        
        return $attempts >= $maxAttempts;
    }

    /**
     * Increment the counter for a given key.
     */
    protected function hit(string $key, int $decayMinutes): void
    {
        try {
            $redis = Redis::connection();
            $redis->incr($key);
            $redis->expire($key, $decayMinutes * 60);
        } catch (\Exception $e) {
            // Fallback to cache if Redis is not available
            cache()->increment($key, 1, $decayMinutes * 60);
        }
    }

    /**
     * Get the number of attempts for the given key.
     */
    protected function attempts(string $key): int
    {
        try {
            $redis = Redis::connection();
            return (int) $redis->get($key) ?: 0;
        } catch (\Exception $e) {
            return (int) cache()->get($key) ?: 0;
        }
    }

    /**
     * Get the number of seconds until the key is available again.
     */
    protected function availableIn(string $key): int
    {
        try {
            $redis = Redis::connection();
            $ttl = $redis->ttl($key);
            return max(0, $ttl);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Create a 'too many attempts' response.
     */
    protected function buildResponse(string $key, int $maxAttempts)
    {
        $seconds = $this->availableIn($key);
        
        Log::warning('Rate limit exceeded', [
            'ip' => request()->ip(),
            'key' => $key,
            'seconds' => $seconds
        ]);

        return response()->json([
            'message' => 'Too many attempts. Please try again later.',
            'seconds' => $seconds,
            'available_at' => now()->addSeconds($seconds)->toISOString()
        ], 429);
    }
}
