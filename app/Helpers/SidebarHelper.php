<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Helper Functions for Sidebar
 *
 * These functions provide utilities for sidebar rendering and permission checking.
 * Integrates with sidebar_permissions.php and sidebar_menu.php
 */

if (! function_exists('sidebar_get_menu_cache_key')) {
    /**
     * Generate cache key for sidebar menu based on user ID and role.
     *
     * @param int|null $userId
     * @return string
     */
    function sidebar_get_menu_cache_key(?int $userId = null): string
    {
        $userId = $userId ?? Auth::id();
        $role   = Auth::check() ? Auth::user()->roles->pluck('name')->sort()->implode(',') : 'guest';
        return 'sidebar_menu_' . $userId . '_' . md5($role);
    }
}

if (! function_exists('sidebar_is_active')) {
    /**
     * Check if the current request matches the item's active rules.
     *
     * @param array{is?: array<string>, except_is?: array<string>, route?: array<string>} $active
     * @return bool
     */
    function sidebar_is_active(array $active): bool
    {
        if (empty($active)) {
            return false;
        }
        $request = request();

        if (! empty($active['except_is'])) {
            foreach ($active['except_is'] as $pattern) {
                if ($request->is($pattern)) {
                    return false;
                }
            }
        }

        if (! empty($active['is'])) {
            foreach ($active['is'] as $pattern) {
                if ($request->is($pattern)) {
                    return true;
                }
            }
        }

        if (! empty($active['route'])) {
            foreach ($active['route'] as $routeName) {
                if ($request->routeIs($routeName)) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (! function_exists('sidebar_get_permissions')) {
    /**
     * Get permissions for a specific group key from sidebar_permissions.php
     *
     * @param string $groupKey
     * @return array
     */
    function sidebar_get_permissions(string $groupKey): array
    {
        return config("sidebar_permissions.groups.{$groupKey}", []);
    }
}

if (! function_exists('sidebar_get_composite_permissions')) {
    /**
     * Get merged permissions from multiple groups (composite)
     *
     * @param string $compositeKey
     * @return array
     */
    function sidebar_get_composite_permissions(string $compositeKey): array
    {
        $composite = config("sidebar_permissions.composites.{$compositeKey}", []);

        if (empty($composite)) {
            return [];
        }

        $groups         = config('sidebar_permissions.groups', []);
        $allPermissions = [];

        foreach ($composite as $groupKey) {
            if (isset($groups[$groupKey])) {
                $allPermissions = array_merge($allPermissions, $groups[$groupKey]);
            }
        }

        return $allPermissions;
    }
}

if (! function_exists('sidebar_clear_cache')) {
    /**
     * Clear sidebar cache for a user or all users
     *
     * @param int|null $userId
     * @return void
     */
    function sidebar_clear_cache(?int $userId = null): void
    {
        if ($userId !== null) {
            Cache::forget(sidebar_get_menu_cache_key($userId));
        } else {
            // Clear all sidebar-related cache keys
            $cacheKeys = Cache::get('sidebar_cache_keys', []);
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
            Cache::forget('sidebar_cache_keys');
        }
    }
}

if (! function_exists('sidebar_can_see_requests_menu')) {
    /**
     * Whether the current user can see the main "الطلبات" (Requests) menu in the sidebar.
     * Preserves original logic: index requests OR index request_types OR index documents OR admin role OR mail.view.
     *
     * @return bool
     */
    function sidebar_can_see_requests_menu(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return $user->can('index requests')
        || $user->can('index request_types')
        || $user->can('index documents')
        || $user->hasRole('admin')
        || $user->hasRole('super-admin')
        || $user->can('mail.view');
    }
}

if (! function_exists('sidebar_can_see_request_mail')) {
    /**
     * Whether the current user can see "بريد الطلبات" (Request Mail) in the sidebar.
     * Only users with the mail.view permission can see this link.
     *
     * @return bool
     */
    function sidebar_can_see_request_mail(): bool
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return false;
            }

            return $user->can('mail.view');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
