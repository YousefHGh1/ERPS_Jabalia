<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(): View
    {
        $usersCount       = User::count();
        $rolesCount       = Role::count();
        $permissionsCount = Permission::count();
        $assignedUsers    = User::whereHas('roles')->count();
        $unassignedUsers  = max(0, $usersCount - $assignedUsers);

        $rolesWithUsers   = Role::whereHas('users')->count();
        $emptyRoles       = max(0, $rolesCount - $rolesWithUsers);
        $usedPermissions  = Permission::whereHas('roles')->count();
        $unusedPermissions = max(0, $permissionsCount - $usedPermissions);

        $coveragePct      = $usersCount > 0 ? round(($assignedUsers / $usersCount) * 100) : 0;
        $rolesCoveragePct = $rolesCount > 0 ? round(($rolesWithUsers / $rolesCount) * 100) : 0;
        $permsUsagePct    = $permissionsCount > 0 ? round(($usedPermissions / $permissionsCount) * 100) : 0;

        $countByPrefix = function (array $prefixes): int {
            $q = Permission::query();
            $q->where(function ($query) use ($prefixes) {
                foreach ($prefixes as $i => $prefix) {
                    $i === 0
                        ? $query->where('name', 'like', $prefix . '%')
                        : $query->orWhere('name', 'like', $prefix . '%');
                }
            });

            return (int) $q->count();
        };

        $typeStats = [
            ['key' => 'view',   'label' => 'عرض',   'icon' => 'fa-eye',   'color' => '#3699FF', 'count' => $countByPrefix(['index', 'show', 'view'])],
            ['key' => 'store',  'label' => 'إضافة',  'icon' => 'fa-plus',   'color' => '#1BC5BD', 'count' => $countByPrefix(['store', 'create'])],
            ['key' => 'update', 'label' => 'تعديل',  'icon' => 'fa-pen',    'color' => '#FFA800', 'count' => $countByPrefix(['update', 'edit'])],
            ['key' => 'delete', 'label' => 'حذف',    'icon' => 'fa-trash',  'color' => '#F64E60', 'count' => $countByPrefix(['delete', 'destroy'])],
        ];
        $typedTotal = array_sum(array_column($typeStats, 'count'));
        $otherCount = max(0, $permissionsCount - $typedTotal);
        $typeStats[] = ['key' => 'other', 'label' => 'أخرى', 'icon' => 'fa-shapes', 'color' => '#8950FC', 'count' => $otherCount];
        foreach ($typeStats as &$row) {
            $row['pct'] = $permissionsCount > 0 ? round(($row['count'] / $permissionsCount) * 100) : 0;
        }
        unset($row);

        $recentUsers = User::with(['roles', 'employee'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentRoles = Role::with('permissions')
            ->withCount(['users', 'permissions'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPermissions = Permission::withCount('roles')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $topRoles = Role::withCount(['users', 'permissions'])
            ->orderByDesc('users_count')
            ->limit(4)
            ->get();

        $prefix = (string) config('permission_ui.route_name_prefix');

        return view('settings.dashboard', compact(
            'usersCount', 'rolesCount', 'permissionsCount',
            'assignedUsers', 'unassignedUsers',
            'rolesWithUsers', 'emptyRoles',
            'usedPermissions', 'unusedPermissions',
            'coveragePct', 'rolesCoveragePct', 'permsUsagePct',
            'typeStats',
            'recentUsers', 'recentRoles', 'recentPermissions', 'topRoles',
            'prefix'
        ));
    }
}

