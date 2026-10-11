<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCitizenApiKey
{
    /**
     * Protect the Citizen API route: require X-API-Key header to match config.
     * When no key is configured, all requests are denied (safe default).
     * Frontend should use the web route (citizen.forMokalaf / getCzn) which is protected by auth.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.citizen_api.api_key');

        if (empty($apiKey)) {
            return response()->json(['error' => 'Citizen API is not configured for key-based access.'], 503);
        }

        $providedKey = $request->header('X-API-Key');

        if (! is_string($providedKey) || ! hash_equals($apiKey, $providedKey)) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
