<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetUserLocale
{
    /**
     * Set application locale and (optional) locale for Carbon from authenticated user preference.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->language) {
            $locale = $request->user()->language === 'en' ? 'en' : 'ar';
            App::setLocale($locale);
        }

        return $next($request);
    }
}
