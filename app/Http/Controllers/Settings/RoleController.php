<?php
namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected array $basicRoles = ['admin', 'super-admin', 'super_user', 'employee'];
    
    protected array $sectionRoles = [
        'administrative_affairs_manager', 'categories_manager', 'employees_manager', 
        'bonus_manager', 'jobs_manager', 'departments_manager', 'holidays_manager',
        'shift_manager', 'vehicle_manager', 'attendance_manager', 'central_treasury_manager',
        'crafts_manager', 'craft_licenses_manager', 'engineering_manager', 
        'engineering_files_manager', 'legal_affairs_manager', 'notices_manager',
        'imprisonment_orders_manager', 'legal_affairs_employee', 'republic_pen_manager',
        'public_relations', 'request_mail_officer', 'external_collection_manager'
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $category = $request->get('category', '');

        $roles = Role::with('permissions')
            ->withCount('users')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($category === 'basic', function ($query) {
                $query->whereIn('name', $this->basicRoles);
            })
            ->when($category === 'section', function ($query) {
                $query->whereIn('name', $this->sectionRoles);
            })
            ->orderBy('name')
            ->paginate(12)
            ->appends(['search' => $search, 'category' => $category]);

        return view('settings.roles.index', compact('roles', 'search'));
    }

    public function create(): View
    {
        $allPermissions = Permission::pluck('name', 'id');

        $permissionCategories = [
            'صلاحيات العرض'   => [],
            'صلاحيات الإدارة' => [],
            'صلاحيات الفهرسة'  => [],
            'صلاحيات الإضافة'  => [],
            'صلاحيات التعديل' => [],
            'صلاحيات الحذف' => [],
            'صلاحيات أخرى'  => [],
        ];

        foreach ($allPermissions as $id => $name) {
            if (strpos($name, 'view') === 0) {
                $permissionCategories['صلاحيات العرض'][$id] = $name;
            } elseif (strpos($name, 'manage') === 0) {
                $permissionCategories['صلاحيات الإدارة'][$id] = $name;
            } elseif (strpos($name, 'index') === 0) {
                $permissionCategories['صلاحيات الفهرسة'][$id] = $name;
            } elseif (strpos($name, 'store') === 0) {
                $permissionCategories['صلاحيات الإضافة'][$id] = $name;
            } elseif (strpos($name, 'update') === 0) {
                $permissionCategories['صلاحيات التعديل'][$id] = $name;
            } elseif (strpos($name, 'delete') === 0) {
                $permissionCategories['صلاحيات الحذف'][$id] = $name;
            } else {
                $permissionCategories['صلاحيات أخرى'][$id] = $name;
            }
        }

        return view('settings.roles.create', compact('permissionCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string'],
            'permissions' => ['array'],
        ]);

        $role = Role::create(['name' => $request->input('name')]);

        $role->givePermissionTo($request->input('permissions', []));

        return redirect()->route(config('permission_ui.route_name_prefix') . 'roles.index');
    }

    public function edit(Role $role): View
    {
        $allPermissions = Permission::pluck('name', 'id');

        $permissionCategories = [
            'صلاحيات العرض'   => [],
            'صلاحيات الإدارة' => [],
            'صلاحيات الفهرسة'  => [],
            'صلاحيات الإضافة'  => [],
            'صلاحيات التعديل' => [],
            'صلاحيات الحذف' => [],
            'صلاحيات أخرى'  => [],
        ];

        foreach ($allPermissions as $id => $name) {
            if (strpos($name, 'view') === 0) {
                $permissionCategories['صلاحيات العرض'][$id] = $name;
            } elseif (strpos($name, 'manage') === 0) {
                $permissionCategories['صلاحيات الإدارة'][$id] = $name;
            } elseif (strpos($name, 'index') === 0) {
                $permissionCategories['صلاحيات الفهرسة'][$id] = $name;
            } elseif (strpos($name, 'store') === 0) {
                $permissionCategories['صلاحيات الإضافة'][$id] = $name;
            } elseif (strpos($name, 'update') === 0) {
                $permissionCategories['صلاحيات التعديل'][$id] = $name;
            } elseif (strpos($name, 'delete') === 0) {
                $permissionCategories['صلاحيات الحذف'][$id] = $name;
            } else {
                $permissionCategories['صلاحيات أخرى'][$id] = $name;
            }
        }

        return view('settings.roles.edit', compact('role', 'permissionCategories'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string'],
            'permissions' => ['array'],
        ]);

        $role->update(['name' => $request->input('name')]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route(config('permission_ui.route_name_prefix') . 'roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        // Prevent deletion of basic roles
        if (in_array($role->name, $this->basicRoles)) {
            return redirect()
                ->route(config('permission_ui.route_name_prefix') . 'roles.index')
                ->with('error', __('لا يمكن حذف الأدوار الأساسية'));
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()
                ->route(config('permission_ui.route_name_prefix') . 'roles.index')
                ->with('error', __('لا يمكن حذف دور لديه مستخدمين نشطين'));
        }

        // Log activity before delete
        activity()
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->log('تم حذف الدور: ' . $role->name);
        
        $role->delete();

        return redirect()->route(config('permission_ui.route_name_prefix') . 'roles.index')
            ->with('success', __('تم حذف الدور بنجاح'));
    }

    /**
     * Duplicate a role with its permissions
     */
    public function duplicate(Role $role): RedirectResponse
    {
        $newRoleName = $role->name . '_copy_' . time();
        
        $newRole = Role::create([
            'name' => $newRoleName,
            'guard_name' => 'web'
        ]);
        
        // Copy all permissions
        $newRole->syncPermissions($role->permissions->pluck('name')->toArray());
        
        // Log activity
        activity()
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->log('تم نسخ الدور: ' . $role->name . ' إلى: ' . $newRoleName);

        return redirect()
            ->route(config('permission_ui.route_name_prefix') . 'roles.edit', $newRole)
            ->with('success', __('تم نسخ الدور بنجاح'));
    }

    /**
     * Create a role based on permission type
     */
    public function createByType(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'in:view,store,update,delete,all'],
            'role_name' => ['required', 'string'],
        ]);

        $type = $request->input('type');
        $roleName = $request->input('role_name');

        // Check if role already exists
        if (Role::where('name', $roleName)->exists()) {
            return redirect()
                ->route(config('permission_ui.route_name_prefix') . 'roles.index')
                ->with('error', __('الدور موجود بالفعل'));
        }

        // Create the role
        $role = Role::create([
            'name' => $roleName,
            'guard_name' => 'web'
        ]);

        // Get permissions based on type
        $permissions = $this->getPermissionsByType($type);

        // Assign permissions to role
        $role->syncPermissions($permissions);

        // Log activity
        activity()
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->log('تم إنشاء دور من نوع ' . $type . ': ' . $roleName);

        return redirect()
            ->route(config('permission_ui.route_name_prefix') . 'roles.edit', $role)
            ->with('success', __('تم إنشاء الدور بنجاح'));
    }

    /**
     * Get permissions by type
     */
    protected function getPermissionsByType(string $type): array
    {
        $prefixes = match($type) {
            'view' => ['index', 'show', 'view'],
            'store' => ['store', 'create'],
            'update' => ['update', 'edit'],
            'delete' => ['delete', 'destroy'],
            'all' => ['index', 'show', 'view', 'store', 'create', 'update', 'edit', 'delete', 'destroy'],
            default => [],
        };

        return Permission::where(function ($query) use ($prefixes) {
            foreach ($prefixes as $prefix) {
                $query->orWhere('name', 'like', $prefix . '%');
            }
        })->pluck('name')->toArray();
    }
}
