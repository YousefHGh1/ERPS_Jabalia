<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EagerLoadAuthUser
{
    /**
     * تحميل علاقات المستخدم مسبقاً لتقليل استعلامات قاعدة البيانات عند كل طلب.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            Auth::user()->loadMissing(['roles', 'employee']);
        }

        return $next($request);
    }
}
