<?php

namespace LaravelDaily\PermissionsUI\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::with('permissions')->paginate(2);

        return view('PermissionsUI::roles.index', compact('roles'));
    }

    public function create(): View
    {
        $allPermissions = Permission::pluck('name', 'id');

        $permissionCategories = [
            'Index Permissions' => [],
            'Store Permissions' => [],
            'Update Permissions' => [],
            'Delete Permissions' => [],
        ];

        foreach ($allPermissions as $id => $name) {
            if (strpos($name, 'index') === 0) {
                $permissionCategories['Index Permissions'][$id] = $name;
            } elseif (strpos($name, 'store') === 0) {
                $permissionCategories['Store Permissions'][$id] = $name;
            } elseif (strpos($name, 'update') === 0) {
                $permissionCategories['Update Permissions'][$id] = $name;
            } elseif (strpos($name, 'delete') === 0) {
                $permissionCategories['Delete Permissions'][$id] = $name;
            }
        }

        return view('PermissionsUI::roles.create', compact('permissionCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'permissions' => ['array'],
        ]);

        $role = Role::create(['name' => $request->input('name')]);

        $role->givePermissionTo($request->input('permissions'));

        return redirect()->route(config('permission_ui.route_name_prefix') . 'roles.index');
    }

    public function edit(Role $role): View
    {
        $allPermissions = Permission::pluck('name', 'id');

        $permissionCategories = [
            'Index Permissions' => [],
            'Store Permissions' => [],
            'Update Permissions' => [],
            'Delete Permissions' => [],
        ];

        foreach ($allPermissions as $id => $name) {
            if (strpos($name, 'index') === 0) {
                $permissionCategories['Index Permissions'][$id] = $name;
            } elseif (strpos($name, 'store') === 0) {
                $permissionCategories['Store Permissions'][$id] = $name;
            } elseif (strpos($name, 'update') === 0) {
                $permissionCategories['Update Permissions'][$id] = $name;
            } elseif (strpos($name, 'delete') === 0) {
                $permissionCategories['Delete Permissions'][$id] = $name;
            }
        }

        return view('PermissionsUI::roles.edit', compact('role', 'permissionCategories'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'permissions' => ['array'],
        ]);

        $role->update(['name' => $request->input('name')]);

        $role->syncPermissions($request->input('permissions'));

        return redirect()->route(config('permission_ui.route_name_prefix') . 'roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route(config('permission_ui.route_name_prefix') . 'roles.index');
    }
}