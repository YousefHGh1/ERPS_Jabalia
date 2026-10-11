@extends('layout.master')

@section('title')
    {{ __('الأدوار') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الإعدادات') }} / {{ __('الأدوار') }}
@endsection

@section('content')
    <div class="container-fluid settings-page">
        <div class="card card-custom settings-card gutter-b">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('الأدوار') }}"
                subtitle="{{ __('إدارة أدوار المستخدمين وصلاحياتهم') }}"
                icon="fa-users"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-info font-weight-bolder px-5 mr-2" data-toggle="modal" data-target="#quickCreateRoleModal">
                        <i class="fas fa-magic mr-2"></i>{{ __('إنشاء سريع') }}
                    </a>
                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'export.roles') }}" class="btn btn-light-info font-weight-bolder px-5 mr-2">
                        <i class="fas fa-file-excel mr-2"></i>{{ __('تصدير') }}
                    </a>
                    @can('store roles')
                        <a class="btn btn-primary font-weight-bolder px-5" href="{{ route(config('permission_ui.route_name_prefix') . 'roles.create') }}">
                            <i class="fas fa-plus mr-2"></i>{{ __('إضافة دور') }}
                        </a>
                    @endcan
                </x-slot>
            </x-page-header>

            {{-- إحصائيات سريعة --}}
            <div class="card-body border-bottom">
                <div class="row">
                    <div class="col-md-3">
                        <div class="bg-light-success rounded p-3 text-center">
                            <div class="text-success font-weight-bolder font-size-h4">{{ $roles->total() }}</div>
                            <div class="text-muted font-size-sm">{{ __('إجمالي الأدوار') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light-primary rounded p-3 text-center">
                            <div class="text-primary font-weight-bolder font-size-h4">{{ \Spatie\Permission\Models\Permission::count() }}</div>
                            <div class="text-muted font-size-sm">{{ __('إجمالي الصلاحيات') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light-warning rounded p-3 text-center">
                            <div class="text-warning font-weight-bolder font-size-h4">{{ \App\Models\User::count() }}</div>
                            <div class="text-muted font-size-sm">{{ __('إجمالي المستخدمين') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light-info rounded p-3 text-center">
                            <div class="text-info font-weight-bolder font-size-h4">{{ \App\Models\User::whereHas('roles')->count() }}</div>
                            <div class="text-muted font-size-sm">{{ __('مستخدمين بأدوار') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body pt-7">
                @can('index roles')
                    <form method="GET" class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="input-icon">
                                    <input type="text"
                                           name="search"
                                           class="form-control"
                                           placeholder="{{ __('ابحث بالاسم...') }}"
                                           value="{{ $search ?? request('search') }}">
                                    <span>
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-control">
                                    <option value="">{{ __('جميع التصنيفات') }}</option>
                                    <option value="basic" {{ request('category') == 'basic' ? 'selected' : '' }}>
                                        {{ __('أدوار أساسية') }}
                                    </option>
                                    <option value="section" {{ request('category') == 'section' ? 'selected' : '' }}>
                                        {{ __('أدوار الأقسام') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mt-3 mt-md-0">
                                <button type="submit" class="btn btn-light-primary font-weight-bold px-6">
                                    {{ __('بحث') }}
                                </button>
                                @if(!empty($search ?? request('search')) || !empty(request('category')))
                                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'roles.index') }}"
                                       class="btn btn-link text-muted font-weight-bold">
                                        {{ __('إلغاء البحث') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                @endcan

            <div class="card-body p-0 pt-0">
                @can('index roles')
                    <div class="row">
                        @forelse($roles as $role)
                            <div class="col-xl-4 col-lg-6 col-md-12 mb-6">
                                <div class="card card-custom role-card">
                                    <div class="card-header border-0 py-5 bg-light-primary">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge {{ get_role_badge_class($role->name) }} badge-lg mr-2">
                                                        {{ role_name_ar($role->name) }}
                                                    </span>
                                                    @if(is_basic_role($role->name))
                                                        <span class="badge badge-secondary">{{ __('أساسي') }}</span>
                                                    @elseif(is_section_role($role->name))
                                                        <span class="badge badge-primary">{{ __('قسم') }}</span>
                                                    @else
                                                        <span class="badge badge-warning">{{ __('خاص') }}</span>
                                                    @endif
                                                </div>
                                                <span class="text-muted font-size-sm">ID: {{ $role->id }}</span>
                                            </div>
                                            <div class="btn-actions">
                                                @can('update roles')
                                                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'roles.edit', $role) }}"
                                                        class="btn btn-sm btn-white font-weight-bolder"
                                                        title="{{ __('تعديل') }}">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                    <form action="{{ route(config('permission_ui.route_name_prefix') . 'roles.duplicate', $role) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info font-weight-bolder"
                                                                title="{{ __('نسخ الدور') }}">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('delete roles')
                                                    @if(!is_basic_role($role->name))
                                                        <form action="{{ route(config('permission_ui.route_name_prefix') . 'roles.destroy', $role) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger font-weight-bolder"
                                                                title="{{ __('حذف') }}"
                                                                onclick="return confirm('{{ __('هل أنت متأكد من حذف الدور: :role؟', ['role' => role_name_ar($role->name)]) }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row mb-4">
                                            <div class="col-6">
                                                <div class="role-stat">
                                                    <span class="role-stat-label">{{ __('الصلاحيات') }}</span>
                                                    <span class="role-stat-value">{{ $role->permissions->count() }}</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="role-stat">
                                                    <span class="role-stat-label">{{ __('المستخدمين') }}</span>
                                                    <span class="role-stat-value">{{ $role->users_count ?? 0 }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="permissions-preview mb-4">
                                            <span class="text-muted font-size-sm font-weight-bold mb-2 d-block">{{ __('أول 3 صلاحيات:') }}</span>
                                            <div class="permissions-container">
                                                @if($role->permissions->count() > 0)
                                                    @foreach($role->permissions->take(3) as $permission)
                                                        <div class="permission-item-truncated" title="{{ perm_name_ar($permission->name) }}">
                                                            <span class="badge {{ get_permission_badge_class($permission->name) }}">
                                                                {{ \Illuminate\Support\Str::limit(perm_name_ar($permission->name), 20, '...') }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                    @if($role->permissions->count() > 3)
                                                        <a href="{{ route(config('permission_ui.route_name_prefix') . 'roles.edit', $role) }}" 
                                                           class="badge badge-secondary view-more-btn"
                                                           title="{{ __('عرض +:count صلاحيات أخرى', ['count' => $role->permissions->count() - 3]) }}">
                                                            +{{ $role->permissions->count() - 3 }}
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-muted font-size-sm">—</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="role-footer">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted font-size-sm">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    {{ $role->created_at->format('Y-m-d') }}
                                                </span>
                                                <a href="{{ route(config('permission_ui.route_name_prefix') . 'roles.edit', $role) }}" 
                                                   class="btn btn-sm btn-primary font-weight-bolder">
                                                    {{ __('عرض التفاصيل') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="settings-empty">
                                    <div class="empty-icon"><i class="fas fa-info-circle"></i></div>
                                    <div class="empty-text">{{ __('لا توجد سجلات') }}</div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    @if($roles->hasPages())
                        <div class="card-footer border-0 d-flex justify-content-center">
                            {!! $roles->withQueryString()->links('pagination::bootstrap-4') !!}
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </div>

    {{-- Modal for Quick Role Creation --}}
    <div class="modal fade" id="quickCreateRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('إنشاء دور سريع') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route(config('permission_ui.route_name_prefix') . 'roles.create-by-type') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">{{ __('اسم الدور') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-solid" name="role_name" 
                                   placeholder="{{ __('مثال: viewer_role') }}" required />
                        </div>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">{{ __('نوع الصلاحيات') }} <span class="text-danger">*</span></label>
                            <select class="form-control form-control-solid" name="type" required>
                                <option value="">{{ __('اختر نوع الصلاحيات') }}</option>
                                <option value="view">{{ __('عرض فقط (index, show, view)') }}</option>
                                <option value="store">{{ __('إضافة فقط (store, create)') }}</option>
                                <option value="update">{{ __('تعديل فقط (update, edit)') }}</option>
                                <option value="delete">{{ __('حذف فقط (delete, destroy)') }}</option>
                                <option value="all">{{ __('جميع الصلاحيات') }}</option>
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ __('سيتم إنشاء دور جديد تلقائياً بجميع الصلاحيات من النوع المحدد') }}
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('إلغاء') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('إنشاء الدور') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.role-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #ebedf3;
    border-radius: 8px;
    overflow: hidden;
}

.role-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.role-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 12px;
    background: linear-gradient(135deg, #f3f6f9 0%, #e8eef5 100%);
    border-radius: 8px;
    position: relative;
}

.role-stat::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #3699ff 0%, #1bc5bd 100%);
    border-radius: 8px 8px 0 0;
}

.role-stat-label {
    font-size: 12px;
    color: #7e8299;
    margin-bottom: 4px;
    font-weight: 500;
}

.role-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #3f4254;
}

.permissions-preview {
    padding: 16px;
    background: #f9fafc;
    border-radius: 8px;
    border: 1px solid #ebedf3;
}

.permissions-container {
    max-width: 100%;
}

.permission-item-truncated {
    display: inline-block;
    margin-bottom: 6px;
    margin-right: 6px;
}

.permission-item-truncated .badge {
    font-size: 11px;
    padding: 5px 10px;
    white-space: nowrap;
    display: inline-block;
    border-radius: 4px;
}

.view-more-btn {
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.view-more-btn:hover {
    opacity: 0.8;
    transform: scale(1.05);
}

.role-footer {
    padding: 16px;
    border-top: 1px solid #ebedf3;
    margin-top: 16px;
    background: #fafafa;
}

.btn-actions {
    display: flex;
    gap: 6px;
}

.btn-actions .btn {
    padding: 8px 12px;
    border-radius: 6px;
}

.badge-lg {
    font-size: 14px;
    padding: 8px 16px;
    border-radius: 6px;
}
</style>
@endpush
