<?php

namespace App\View\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use App\Services\SidebarService;

/**
 * SidebarComposer
 *
 * Builds and caches the sidebar menu based on user permissions.
 * Integrates with sidebar_permissions.php and sidebar_menu.php
 */
class SidebarComposer
{
    protected const PERMISSION_MATCH_ALL = 'all';
    protected const PERMISSION_MATCH_ANY = 'any';

    /**
     * Cache key prefix
     */
    protected const CACHE_PREFIX = 'sidebar_menu_';

    /**
     * Cache TTL in seconds (30 minutes)
     */
    protected const CACHE_TTL = 1800;

    /**
     * Bind data to the view.
     * Builds the sidebar menu from config and filters by permissions/gates once per request.
     */
    public function compose(View $view): void
    {
        $view->with('sidebarMenu', $this->buildVisibleMenu());
    }

    /**
     * Build menu array with only items the current user is allowed to see.
     * Uses caching for improved performance.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildVisibleMenu(): array
    {
        $user = Auth::user();

        if ($user === null) {
            return [];
        }

        // Build menu directly without caching for debugging
        return $this->buildMenuFromConfig($user);
    }

    /**
     * Build menu from configuration files
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function buildMenuFromConfig($user): array
    {
        $menu = config('sidebar.menu', []);
        return $this->filterMenuItems($menu, $user);
    }

    /**
     * Filter menu items based on permissions
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    protected function filterMenuItems(array $items, $user): array
    {
        $visible = [];

        foreach ($items as $item) {
            $entry = $item;
            $filteredChildren = [];

            if (isset($item['children'])) {
                $filteredChildren = $this->filterMenuItems($item['children'], $user);
            }

            $isVisibleBySelf = $this->isItemVisible($item, $user);
            $showWhenAnyChildVisible = $this->shouldShowWhenAnyChildVisible($item);

            if (!empty($filteredChildren) && $showWhenAnyChildVisible) {
                $entry['children'] = $filteredChildren;
                $visible[] = $entry;
                continue;
            }

            if (!$isVisibleBySelf) {
                continue;
            }

            if (isset($item['children'])) {
                if (count($filteredChildren) === 0) {
                    continue;
                }
                $entry['children'] = $filteredChildren;
            }

            $visible[] = $entry;
        }

        return $visible;
    }

    /**
     * Check if an item is visible to the user
     *
     * @param array<string, mixed> $item
     * @return bool
     */
    protected function isItemVisible(array $item, $user): bool
    {
        if ($user === null) {
            return false;
        }

        // Admin and super-admin see everything
        if ($user->hasRole(['admin', 'super-admin'])) {
            return true;
        }

        // Check gate if present
        if (isset($item['gate'])) {
            $gate = $item['gate'];
            return is_string($gate) && Gate::allows($gate);
        }

        // Check permissions_key (reference to sidebar_permissions groups)
        if (isset($item['permissions_key'])) {
            $permissions = config("sidebar_permissions.groups.{$item['permissions_key']}", []);
            return $user->hasAllPermissions($permissions);
        }

        // Check permissions_composite (reference to composite groups)
        if (isset($item['permissions_composite'])) {
            return $this->checkCompositePermissions($item['permissions_composite'], $user);
        }

        // Check inline permissions array (legacy support)
        $permissions = $item['permissions'] ?? null;
        if (empty($permissions) || !is_array($permissions)) {
            return true;
        }

        $matchMode = $this->permissionMatchMode($item);
        if ($matchMode === self::PERMISSION_MATCH_ANY) {
            foreach ($permissions as $permission) {
                if ($user->can($permission)) {
                    return true;
                }
            }
            return false;
        }

        foreach ($permissions as $permission) {
            if (!$user->can($permission)) {
                return false;
            }
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

        $groups = config('sidebar_permissions.groups', []);

        foreach ($composite as $groupKey) {
            if (isset($groups[$groupKey])) {
                if ($user->hasAnyPermission($groups[$groupKey])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Parent menus default to visibility by any visible child.
     *
     * @param array<string, mixed> $item
     */
    protected function shouldShowWhenAnyChildVisible(array $item): bool
    {
        if (!array_key_exists('show_when_any_child_visible', $item)) {
            return isset($item['children']);
        }

        return (bool) $item['show_when_any_child_visible'];
    }

    /**
     * @param array<string, mixed> $item
     */
    protected function permissionMatchMode(array $item): string
    {
        $mode = $item['permission_match'] ?? self::PERMISSION_MATCH_ALL;

        return in_array($mode, [self::PERMISSION_MATCH_ALL, self::PERMISSION_MATCH_ANY], true)
            ? $mode
            : self::PERMISSION_MATCH_ALL;
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
}