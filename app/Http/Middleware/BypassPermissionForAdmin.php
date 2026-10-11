<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BypassPermissionForAdmin
{
    /**
     * Handle an incoming request.
     *
     * Allows admin and super-admin roles to bypass permission checks.
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $user = $request->user();

        // If user is admin or super-admin, allow access
        if ($user && ($user->hasRole('admin') || $user->hasRole('super-admin'))) {
            return $next($request);
        }

        // Otherwise, check for the specific permission
        if ($permission && $user && $user->can($permission)) {
            return $next($request);
        }

        return response()->view('errors.403', [], 403);
    }
}
