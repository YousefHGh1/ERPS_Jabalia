<?php
namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $type = $request->get('type', '');

        $permissions = Permission::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($type !== '', function ($query) use ($type) {
                $query->where('name', 'like', $type . '%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->appends(['search' => $search, 'type' => $type]);

        return view('settings.permissions.index', compact('permissions', 'search'));
    }

    public function create(): View
    {
        $roles = Role::all();

        return view('settings.permissions.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string'],
            'roles' => ['array'],
        ]);

        $permission = Permission::create($data);

        $permission->syncRoles($request->input('roles', []));

        return redirect()->route(config('permission_ui.route_name_prefix') . 'permissions.index');
    }

    public function edit(Permission $permission): View
    {
        $roles = Role::all();

        return view('settings.permissions.edit', compact('permission', 'roles'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string'],
            'roles' => ['array'],
        ]);

        $permission->update($data);

        $permission->syncRoles($request->input('roles', []));

        return redirect()->route(config('permission_ui.route_name_prefix') . 'permissions.index');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()->route(config('permission_ui.route_name_prefix') . 'permissions.index');
    }

    public function syncControllerPermissions(Request $request): RedirectResponse
    {
        if (! $request->user()->can('update permissions')) {
            abort(403);
        }

        try {
            Artisan::call('permissions:auto-generate', [
                '--force' => true,
                '--assign-user' => $request->user()->id,
                '--no-interaction' => true,
            ]);

            return redirect()
                ->route(config('permission_ui.route_name_prefix') . 'permissions.index')
                ->with('success', __('تم إعادة إنشاء جميع الصلاحيات والأدوار من الكنترولرات وربط Admin بجميع الصلاحيات.'));
        } catch (\Throwable $e) {
            return redirect()
                ->route(config('permission_ui.route_name_prefix') . 'permissions.index')
                ->with('error', __('حدث خطأ أثناء تحديث الصلاحيات: ') . $e->getMessage());
        }
    }
}
