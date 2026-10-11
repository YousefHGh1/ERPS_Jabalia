<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for rate limiting across the application.
    | Different limits are set for different types of routes based on their
    | sensitivity and expected usage patterns.
    |
    */

    /*
     * Default rate limits for API routes
     */
    'api' => [
        'default' => '60,1',      // 60 requests per minute
        'strict' => '30,1',       // 30 requests per minute
        'very_strict' => '10,1',  // 10 requests per minute
    ],

    /*
     * Rate limits for web routes
     */
    'web' => [
        'default' => '100,1',     // 100 requests per minute
        'auth' => '20,1',         // 20 requests per minute for auth routes
        'sensitive' => '5,1',     // 5 requests per minute for sensitive operations
    ],

    /*
     * Specific route rate limits
     */
    'routes' => [
        // Authentication routes
        'login' => '5,1',                    // 5 login attempts per minute
        'password_reset' => '3,1',           // 3 password reset attempts per minute
        'password_change' => '3,1',          // 3 password change attempts per minute
        'register' => '3,1',                 // 3 registration attempts per minute

        // API routes
        'citizen_api' => '100,1',            // 100 requests per minute for citizen API
        'cache_management' => '30,1',        // 30 requests per minute for cache operations
        'security' => '20,1',                // 20 requests per minute for security operations

        // Sensitive operations
        'payment' => '10,1',                 // 10 payment attempts per minute
        'bulk_operations' => '5,1',          // 5 bulk operations per minute
        'export' => '3,1',                   // 3 export requests per minute
    ],

    /*
     * Rate limit by user roles
     */
    'by_role' => [
        'admin' => '200,1',      // Higher limit for admins
        'employee' => '100,1',   // Standard limit for employees
        'citizen' => '50,1',     // Lower limit for citizens
    ],

    /*
     * Rate limit for IP-based protection
     */
    'ip_based' => [
        'global' => '1000,1',    // 1000 requests per minute per IP globally
        'suspicious' => '10,1',  // Very strict for suspicious IPs
    ],

    /*
     * Decay time for rate limits (in seconds)
     */
    'decay_minutes' => 1,

    /*
     * Whether to log rate limit violations
     */
    'log_violations' => env('RATE_LIMIT_LOG_VIOLATIONS', true),

    /*
     * Whether to block IPs after repeated violations
     */
    'block_on_repeated_violations' => env('RATE_LIMIT_BLOCK_ON_REPEATED', true),

    /*
     * Number of violations before blocking
     */
    'violations_before_block' => 5,

    /*
     * Block duration in minutes
     */
    'block_duration_minutes' => 15,

];
