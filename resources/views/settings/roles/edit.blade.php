@extends('layout.master')

@section('title')
    {{ __('تعديل دور') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الإعدادات') }} / {{ __('الأدوار') }} / {{ __('تعديل') }}
@endsection

@section('content')
    <div class="container-fluid settings-page">
        <form action="{{ route(config('permission_ui.route_name_prefix') . 'roles.update', $role) }}" method="post">
            @csrf
            @method('PATCH')

            {{-- بطاقة معلومات الدور --}}
            <div class="card card-custom settings-card settings-form-card gutter-b mb-6">
                {{-- ترويسة الصفحة (داخل النموذج ليستمر زر الحفظ) — موحّدة مع هوية القسم --}}
                <x-page-header
                    title="{{ __('تعديل الدور') }}"
                    subtitle="{{ __('تعديل اسم الدور والصلاحيات المرتبطة به') }}"
                    icon="fa-users"
                >
                    <x-slot name="actions">
                        <a href="{{ route(config('permission_ui.route_name_prefix') . 'roles.index') }}" class="btn btn-warning font-weight-bolder mr-2">
                            <i class="fas fa-arrow-right ml-2"></i>{{ __('عودة') }}
                        </a>
                        <button type="submit" class="btn btn-primary font-weight-bolder">
                            <i class="fas fa-check ml-2"></i>{{ __('حفظ التعديلات') }}
                        </button>
                    </x-slot>
                </x-page-header>
                <div class="card-body pt-7">
                    <div class="row align-items-center mb-4">
                        <div class="col-lg-6">
                            <label class="font-weight-bold text-dark mb-2">{{ __('اسم الدور') }} <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <input type="text" class="form-control form-control-solid form-control-lg" name="name" id="name" value="{{ old('name', role_name_ar($role->name)) }}" required placeholder="{{ __('مثال: manager_accounting') }}" />
                                <span><i class="fas fa-user text-muted"></i></span>
                            </div>
                            @error('name')
                                <span class="text-danger font-weight-bold d-block mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-lg-3 mt-4 mt-lg-0">
                            <div class="bg-light-info rounded p-4 text-center">
                                <span class="font-weight-bolder text-dark-75 d-block">{{ __('عدد الصلاحيات المحددة') }}</span>
                                <span class="font-size-h2 font-weight-bolder text-primary" id="selectedPermissionsCount">{{ $role->permissions->count() }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3 mt-4 mt-lg-0">
                            <div class="bg-light-success rounded p-4 text-center">
                                <span class="font-weight-bolder text-dark-75 d-block">{{ __('إجمالي الصلاحيات') }}</span>
                                <span class="font-size-h2 font-weight-bolder text-success" id="totalPermissionsCount">{{ collect($permissionCategories)->flatten()->count() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- خيارات سريعة --}}
                    <div class="separator separator-dashed my-6"></div>
                    <label class="font-weight-bold text-dark mb-3">{{ __('اختيار سريع حسب النوع') }}</label>
                    <div class="row mb-4">
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-light-info btn-block font-weight-bolder quick-select" data-type="view">
                                <i class="fas fa-eye mr-1"></i>{{ __('عرض') }}
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-light-success btn-block font-weight-bolder quick-select" data-type="store">
                                <i class="fas fa-plus mr-1"></i>{{ __('إضافة') }}
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-light-warning btn-block font-weight-bolder quick-select" data-type="update">
                                <i class="fas fa-edit mr-1"></i>{{ __('تعديل') }}
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-light-danger btn-block font-weight-bolder quick-select" data-type="delete">
                                <i class="fas fa-trash mr-1"></i>{{ __('حذف') }}
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-light-primary btn-block font-weight-bolder quick-select" data-type="all">
                                <i class="fas fa-crown mr-1"></i>{{ __('الكل') }}
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-light-secondary btn-block font-weight-bolder" id="clearQuickSelect">
                                <i class="fas fa-times mr-1"></i>{{ __('مسح') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if (collect($permissionCategories)->count())
            {{-- بطاقة الصلاحيات --}}
            <div class="card card-custom settings-card gutter-b">
                <div class="card-header border-0 py-5 d-flex align-items-center flex-wrap">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark mb-1">
                            <i class="fas fa-lock text-primary ml-2"></i>
                            {{ __('الصلاحيات') }}
                        </h3>
                        <span class="text-muted font-weight-bold font-size-sm">{{ __('حدد الصلاحيات التي يتمتع بها هذا الدور') }}</span>
                    </div>
                    <div class="card-toolbar ml-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-light-primary font-weight-bolder" id="selectAllPermissions">
                                <i class="fas fa-check ml-1"></i>{{ __('تحديد الكل') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-light-danger font-weight-bolder" id="deselectAllPermissions">
                                <i class="fas fa-trash ml-1"></i>{{ __('إلغاء الكل') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        @foreach ($permissionCategories as $category => $permissions)
                            @if(count($permissions) > 0)
                                <div class="col-lg-4 col-md-6 mb-6">
                                    <div class="card card-custom border-0 h-100 shadow-sm role-permission-card {{ $category === 'صلاحيات أخرى' ? 'bg-light-secondary' : 'bg-light-primary' }}">
                                        <div class="card-header cursor-pointer collapsed border-0 py-4" data-toggle="collapse" data-target="#category-{{ \Illuminate\Support\Str::slug($category) }}" aria-expanded="false">
                                            <div class="card-title d-flex align-items-center">
                                                <label class="checkbox checkbox-outline checkbox-primary checkbox-lg mb-0">
                                                    <input type="checkbox" class="category-select-all" data-category="{{ \Illuminate\Support\Str::slug($category) }}" />
                                                    <span></span>
                                                </label>
                                                <div class="ml-3">
                                                    <span class="font-weight-bolder text-dark d-block">{{ $category }}</span>
                                                    <span class="text-muted font-size-sm">{{ count($permissions) }} صلاحية</span>
                                                </div>
                                            </div>
                                            <div class="card-toolbar">
                                                <span class="badge {{ getCategoryBadgeClass($category) }} badge-pill px-3 py-2">{{ count($permissions) }}</span>
                                                <i class="fas fa-chevron-down expand-icon ml-2"></i>
                                            </div>
                                        </div>
                                        <div id="category-{{ \Illuminate\Support\Str::slug($category) }}" class="collapse">
                                            <div class="card-body pt-0 permission-list-body">
                                                @foreach ($permissions as $id => $permName)
                                                    <div class="permission-item hover:bg-white rounded">
                                                        <label class="permission-item-label">
                                                            <span class="checkbox checkbox-outline checkbox-sm checkbox-primary">
                                                                <input type="checkbox" name="permissions[]" class="permission-checkbox" data-category="{{ \Illuminate\Support\Str::slug($category) }}" value="{{ $id }}" id="permission-{{ $id }}" @checked(in_array($id, old('permissions', [])) || $role->permissions->contains($id))>
                                                                <span></span>
                                                            </span>
                                                            <span class="permission-item-text">{{ perm_name_ar($permName) }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @error('permissions')
                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                    @enderror
                </div>
                <div class="card-footer border-0 bg-light-primary">
                    <button type="submit" class="btn btn-primary font-weight-bolder">
                        <i class="fas fa-check ml-2"></i>{{ __('حفظ التعديلات') }}
                    </button>
                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'roles.index') }}" class="btn btn-warning font-weight-bolder mr-2">
                        {{ __('إلغاء') }}
                    </a>
                </div>
            </div>
            @endif
        </form>
    </div>
@endsection

@push('styles')
<style>
.permission-item {
    padding: 8px 12px;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.permission-item:hover {
    background-color: rgba(255, 255, 255, 0.7);
}

.permission-item-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    margin: 0;
}

.permission-item-text {
    margin-right: 12px;
    font-size: 14px;
}

.expand-icon {
    transition: transform 0.3s ease;
}

.expand-icon.rotate-180 {
    transform: rotate(180deg);
}

.role-permission-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.role-permission-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
@endpush

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    const selectAllBtn = document.getElementById('selectAllPermissions');
    const deselectAllBtn = document.getElementById('deselectAllPermissions');
    const countEl = document.getElementById('selectedPermissionsCount');
    const quickSelectBtns = document.querySelectorAll('.quick-select');
    const clearQuickSelectBtn = document.getElementById('clearQuickSelect');

    function updateCount() {
        if (countEl) {
            countEl.textContent = document.querySelectorAll('.permission-checkbox:checked').length;
        }
    }

    function toggleCategory(categorySlug, checked) {
        document.querySelectorAll('.permission-checkbox[data-category="' + categorySlug + '"]').forEach(cb => cb.checked = checked);
        const catSelectAll = document.querySelector('.category-select-all[data-category="' + categorySlug + '"]');
        if (catSelectAll) catSelectAll.checked = checked;
        updateCount();
    }

    selectAllBtn?.addEventListener('click', function() {
        permissionCheckboxes.forEach(cb => cb.checked = true);
        document.querySelectorAll('.category-select-all').forEach(cb => cb.checked = true);
        updateCount();
    });

    deselectAllBtn?.addEventListener('click', function() {
        permissionCheckboxes.forEach(cb => cb.checked = false);
        document.querySelectorAll('.category-select-all').forEach(cb => cb.checked = false);
        updateCount();
    });

    // Quick select by type
    quickSelectBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            const prefixes = {
                'view': ['index', 'show', 'view'],
                'store': ['store', 'create'],
                'update': ['update', 'edit'],
                'delete': ['delete', 'destroy'],
                'all': ['index', 'show', 'view', 'store', 'create', 'update', 'edit', 'delete', 'destroy']
            }[type] || [];

            permissionCheckboxes.forEach(cb => {
                const permName = cb.id.replace('permission-', '');
                const label = document.querySelector('label[for="' + cb.id + '"] .permission-item-text')?.textContent || '';
                
                const shouldCheck = prefixes.some(prefix => label.includes(prefix) || permName.includes(prefix));
                cb.checked = shouldCheck;
            });

            // Update category checkboxes
            document.querySelectorAll('.category-select-all').forEach(catCb => {
                const cat = catCb.dataset.category;
                const catCheckboxes = document.querySelectorAll('.permission-checkbox[data-category="' + cat + '"]');
                catCb.checked = Array.from(catCheckboxes).every(c => c.checked);
            });

            updateCount();
        });
    });

    // Clear quick select
    clearQuickSelectBtn?.addEventListener('click', function() {
        permissionCheckboxes.forEach(cb => cb.checked = false);
        document.querySelectorAll('.category-select-all').forEach(cb => cb.checked = false);
        updateCount();
    });

    document.querySelectorAll('.category-select-all').forEach(cb => {
        cb.addEventListener('click', function(e) { e.stopPropagation(); });
        cb.addEventListener('change', function() {
            toggleCategory(this.dataset.category, this.checked);
        });
    });

    // Handle expand icon rotation
    document.querySelectorAll('.card-header[data-toggle="collapse"]').forEach(header => {
        header.addEventListener('click', function() {
            const icon = this.querySelector('.expand-icon');
            const target = this.getAttribute('data-target');
            const targetEl = document.querySelector(target);
            
            setTimeout(() => {
                if (targetEl.classList.contains('show')) {
                    icon.classList.add('rotate-180');
                } else {
                    icon.classList.remove('rotate-180');
                }
            }, 100);
        });
    });

    permissionCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateCount();
            const cat = this.dataset.category;
            const catCheckboxes = document.querySelectorAll('.permission-checkbox[data-category="' + cat + '"]');
            const catSelectAll = document.querySelector('.category-select-all[data-category="' + cat + '"]');
            if (catSelectAll) catSelectAll.checked = Array.from(catCheckboxes).every(c => c.checked);
        });
    });

    updateCount();
});
</script>
@endsection
