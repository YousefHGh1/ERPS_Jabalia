<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $duration = 300)
    {
        // فقط تخزين استجابات GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // إنشاء مفتاح الكاش بناءً على الـ URL والمعاملات
        $cacheKey = 'response_' . md5($request->fullUrl()) . '_' . auth()->id();

        // التحقق من وجود الكاش
        if (Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey));
        }

        // تنفيذ الطلب
        $response = $next($request);

        // تخزين الاستجابة في الكاش فقط إذا كانت ناجحة
        if ($response->getStatusCode() === 200) {
            Cache::put($cacheKey, $response->getContent(), $duration);
        }

        return $response;
    }
}
