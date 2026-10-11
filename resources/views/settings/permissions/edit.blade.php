@extends('layout.master')

@section('title')
    {{ __('تعديل صلاحية') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الإعدادات') }} / {{ __('الصلاحيات') }} / {{ __('تعديل') }}
@endsection

@section('content')
    <div class="container-fluid settings-page">
        <div class="card card-custom settings-card settings-form-card gutter-b">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('تعديل الصلاحية') }}"
                subtitle="{{ perm_name_ar($permission->name) }}"
                icon="fa-lock"
            >
                <x-slot name="actions">
                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'permissions.index') }}" class="btn btn-light-danger font-weight-bolder">
                        <i class="fas fa-arrow-right mr-2"></i>{{ __('عودة') }}
                    </a>
                </x-slot>
            </x-page-header>

            <form action="{{ route(config('permission_ui.route_name_prefix') . 'permissions.update', $permission) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <label class="font-weight-bold text-dark">{{ __('اسم الصلاحية') }} <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <input type="text" class="form-control form-control-solid form-control-lg" name="name" id="name" value="{{ old('name', perm_name_ar($permission->name)) }}" required />
                                <span><i class="fas fa-lock text-muted"></i></span>
                            </div>
                            @error('name')
                                <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-lg-6 mb-4">
                            <label class="font-weight-bold text-dark">{{ __('الوصف (اختياري)') }}</label>
                            <div class="input-icon">
                                <input type="text" class="form-control form-control-solid form-control-lg" name="description" id="description" value="{{ old('description') }}" placeholder="{{ __('وصف الصلاحية') }}" />
                                <span><i class="fas fa-info text-muted"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-lightbulb mr-2"></i>
                        <strong>{{ __('تنسيق الصلاحيات الموصى به:') }}</strong>
                        <ul class="mb-0 mt-2 mr-4">
                            <li>{{ __('عرض: index, show, view (مثال: index employees)') }}</li>
                            <li>{{ __('إضافة: store, create (مثال: store employees)') }}</li>
                            <li>{{ __('تعديل: update, edit (مثال: update employees)') }}</li>
                            <li>{{ __('حذف: delete, destroy (مثال: delete employees)') }}</li>
                        </ul>
                    </div>

                    @if ($roles->count())
                        <div class="separator separator-dashed my-6"></div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <label class="font-weight-bold text-dark mb-0">{{ __('تعيين للأدوار') }}</label>
                                <span class="text-muted font-size-sm">{{ __('عدد الأدوار المحددة: ') }}<span id="selectedRolesCount">{{ $permission->roles->count() }}</span></span>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-light-primary font-weight-bolder" id="selectAllRoles">
                                    <i class="fas fa-check ml-1"></i>{{ __('تحديد الكل') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-light-danger font-weight-bolder" id="deselectAllRoles">
                                    <i class="fas fa-times ml-1"></i>{{ __('إلغاء الكل') }}
                                </button>
                            </div>
                        </div>
                        <div class="permission-list-body bg-light-primary rounded p-4">
                            <div class="row">
                                @foreach ($roles as $role)
                                    <div class="col-md-4 col-lg-3 mb-4">
                                        <div class="permission-item hover:bg-white rounded p-3">
                                            <label class="permission-item-label">
                                                <span class="checkbox checkbox-outline checkbox-primary">
                                                    <input type="checkbox" name="roles[]" id="role-{{ $role->id }}" value="{{ $role->id }}" @checked(in_array($role->id, old('roles', [])) || $permission->roles->contains($role->id))>
                                                    <span></span>
                                                </span>
                                                <span class="permission-item-text">{{ role_name_ar($role->name) }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('roles')
                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    @endif
                </div>
                <div class="card-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary font-weight-bolder mr-2">
                        <i class="fas fa-check"></i> {{ __('حفظ التعديلات') }}
                    </button>
                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'permissions.index') }}" class="btn btn-warning font-weight-bolder">
                        {{ __('إلغاء') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
.permission-item {
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.permission-item:hover {
    background-color: rgba(255, 255, 255, 0.7);
    transform: translateX(4px);
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
    font-weight: 500;
}
</style>
@endpush

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const selectAllBtn = document.getElementById('selectAllRoles');
    const deselectAllBtn = document.getElementById('deselectAllRoles');
    const countEl = document.getElementById('selectedRolesCount');

    function updateCount() {
        if (countEl) {
            countEl.textContent = document.querySelectorAll('input[name="roles[]"]:checked').length;
        }
    }

    function selectAll() {
        roleCheckboxes.forEach(cb => cb.checked = true);
        updateCount();
    }

    function deselectAll() {
        roleCheckboxes.forEach(cb => cb.checked = false);
        updateCount();
    }

    selectAllBtn?.addEventListener('click', function(e) {
        e.preventDefault();
        selectAll();
    });

    deselectAllBtn?.addEventListener('click', function(e) {
        e.preventDefault();
        deselectAll();
    });

    roleCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    updateCount();
});
</script>
@endsection
