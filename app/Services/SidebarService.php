<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

/**
 * SidebarService
 * 
 * Handles sidebar menu building with permission checking and caching.
 * Integrates with sidebar_permissions.php and sidebar_menu.php
 */
class SidebarService
{
    /**
     * Cache key prefix for user-specific menus
     */
    const CACHE_PREFIX = 'sidebar_user_';
    
    /**
     * Cache TTL in seconds (30 minutes)
     */
    const CACHE_TTL = 1800;

    /**
     * Get the complete sidebar menu for the current user
     *
     * @return array
     */
    public function getMenu(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }

        // Check if caching is enabled
        $useCache = config('sidebar.use_cache', true);
        
        if ($useCache) {
            $cacheKey = self::CACHE_PREFIX . $user->id;
            
            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
                return $this->buildMenu($user);
            });
        }

        return $this->buildMenu($user);
    }

    /**
     * Build the menu for a specific user
     *
     * @param \App\Models\User $user
     * @return array
     */
    public function buildMenu($user): array
    {
        $menuConfig = config('sidebar_menu.menu', []);
        $processedMenu = [];

        foreach ($menuConfig as $menuItem) {
            $processedItem = $this->processMenuItem($menuItem, $user);
            
            // Only include items that pass visibility checks
            if ($processedItem !== null) {
                $processedMenu[] = $processedItem;
            }
        }

        return $processedMenu;
    }

    /**
     * Process a single menu item
     *
     * @param array $item
     * @param \App\Models\User $user
     * @return array|null
     */
    protected function processMenuItem(array $item, $user): ?array
    {
        // Check gate if present
        if (isset($item['gate']) && !$this->passesGate($item['gate'])) {
            return null;
        }

        // Check permissions
        if (!$this->passesPermissionCheck($item, $user)) {
            return null;
        }

        // Process children if this is a submenu
        if (isset($item['type']) && $item['type'] === 'submenu' && isset($item['children'])) {
            $visibleChildren = [];
            
            foreach ($item['children'] as $child) {
                $processedChild = $this->processMenuItem($child, $user);
                
                if ($processedChild !== null) {
                    $visibleChildren[] = $processedChild;
                }
            }

            // Only show parent if at least one child is visible
            if (empty($visibleChildren)) {
                return null;
            }

            $item['children'] = $visibleChildren;
        }

        return $item;
    }

    /**
     * Check if user passes the permission requirements
     *
     * @param array $item
     * @param \App\Models\User $user
     * @return bool
     */
    protected function passesPermissionCheck(array $item, $user): bool
    {
        // No permissions specified = visible to all authenticated users
        if (!isset($item['permissions_key']) && !isset($item['permissions']) && !isset($item['permissions_composite'])) {
            return true;
        }

        // Handle composite permissions (array of permission groups)
        if (isset($item['permissions_composite'])) {
            return $this->checkCompositePermissions($item['permissions_composite'], $user);
        }

        // Handle permissions_key (reference to sidebar_permissions groups)
        if (isset($item['permissions_key'])) {
            $permissions = config("sidebar_permissions.groups.{$item['permissions_key']}", []);
            return $user->hasAllPermissions($permissions);
        }

        // Handle inline permissions array
        if (isset($item['permissions'])) {
            return $user->hasAllPermissions($item['permissions']);
        }

        return true;
    }

    /**
     * Check composite permission groups
     *
     * @param string $compositeKey
     * @param \App\Models\User $user
     * @return bool
     */
    protected function checkCompositePermissions(string $compositeKey, $user): bool
    {
        $composite = config("sidebar_permissions.composites.{$compositeKey}", []);
        
        if (empty($composite)) {
            return true;
        }

        $allPermissions = [];
        $groups = config('sidebar_permissions.groups', []);

        foreach ($composite as $groupKey) {
            if (isset($groups[$groupKey])) {
                $allPermissions = array_merge($allPermissions, $groups[$groupKey]);
            }
        }

        return $user->hasAnyPermission($allPermissions);
    }

    /**
     * Check if a gate passes
     *
     * @param string $gate
     * @return bool
     */
    protected function passesGate(string $gate): bool
    {
        return \Gate::allows($gate);
    }

    /**
     * Clear cache for a specific user
     *
     * @param int $userId
     * @return bool
     */
    public static function clearCache(int $userId): bool
    {
        return Cache::forget(self::CACHE_PREFIX . $userId);
    }

    /**
     * Clear all sidebar caches
     *
     * @return void
     */
    public static function clearAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Get permissions for a specific group key
*
     * @param string $groupKey
     * @return array
     */
    public static function getPermissions(string $groupKey): array
    {
        return config("sidebar_permissions.groups.{$groupKey}", []);
    }

    /**
     * Get composite permissions (merged group permissions)
     *
     * @param string $compositeKey
     * @return array
     */
    public static function getCompositePermissions(string $compositeKey): array
    {
        $composite = config("sidebar_permissions.composites.{$compositeKey}", []);
        
        if (empty($composite)) {
            return [];
        }

        $groups = config('sidebar_permissions.groups', []);
        $allPermissions = [];

        foreach ($composite as $groupKey) {
            if (isset($groups[$groupKey])) {
                $allPermissions = array_merge($allPermissions, $groups[$groupKey]);
            }
        }

        return $allPermissions;
    }
}