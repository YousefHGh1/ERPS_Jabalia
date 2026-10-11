<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $roleFilter = $request->get('role_filter', '');
        $statusFilter = $request->get('status_filter', '');

        $users = User::with('roles', 'employee')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->when($roleFilter !== '', function ($query) use ($roleFilter) {
                $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('name', $roleFilter);
                });
            })
            ->when($statusFilter !== '', function ($query) use ($statusFilter) {
                if ($statusFilter === 'active') {
                    $query->whereHas('employee', function ($q) {
                        $q->where('status', 'Active');
                    });
                } elseif ($statusFilter === 'inactive') {
                    $query->whereHas('employee', function ($q) {
                        $q->where('status', '!=', 'Active');
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $roles = Role::all();

        return view('settings.users.index', compact('users', 'search', 'roles'));
    }

    public function edit(User $user): View
    {
        $roles = Role::all();

        return view('settings.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'roles' => ['required', 'array'],
        ]);

        $user->syncRoles($request->input('roles'));

        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('تم تعديل صلاحيات المستخدم: ' . $user->name);

        return redirect()->route(config('permission_ui.route_name_prefix') . 'users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Log activity before delete
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('تم حذف المستخدم: ' . $user->name);
        
        // 在删除用户前，将关联的revenue_batches记录的opened_by_user_id和closed_by_user_id设置为NULL
        \App\Models\RevenueBatch::where('opened_by_user_id', $user->id)->update(['opened_by_user_id' => null]);
        \App\Models\RevenueBatch::where('closed_by_user_id', $user->id)->update(['closed_by_user_id' => null]);

        $user->delete();

        return redirect()->route(config('permission_ui.route_name_prefix') . 'users.index')
            ->with('success', __('تم حذف المستخدم بنجاح'));
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        if (! $request->user()->can('update users')) {
            abort(403, __('ليس لديك صلاحية للوصول إلى هذه الصفحة'));
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($data['password']);
        $user->password_changed_at = now();
        $user->save();
        
        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('تم إعادة تعيين كلمة مرور المستخدم: ' . $user->name);

        return redirect()
            ->route(config('permission_ui.route_name_prefix') . 'users.index')
            ->with('success', __('تم تحديث كلمة مرور المستخدم بنجاح'));
    }
}
