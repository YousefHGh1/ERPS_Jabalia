<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('bump_requests_list_cache_version')) {
    /**
     * Increment the requests list cache version to invalidate any cached list views.
     * Use this instead of Cache::forget('requests_index_*') because Laravel cache
     * does not support wildcard key deletion. Cache keys for requests list should
     * include this version (e.g. "requests_index_v{$version}_{hash}") when implemented.
     */
    function bump_requests_list_cache_version(): void
    {
        Cache::put('requests_list_version', (int) Cache::get('requests_list_version', 0) + 1);
    }
}
