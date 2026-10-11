@extends('layout.master')

@section('title')
    {{ __('تعديل أدوار المستخدم') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الإعدادات') }} / {{ __('المستخدمين') }} / {{ __('تعديل') }}
@endsection

@section('content')
    <div class="container-fluid settings-page">
        <form action="{{ route(config('permission_ui.route_name_prefix') . 'users.update', $user) }}" method="post">
            @csrf
            @method('PATCH')

            {{-- بطاقة معلومات المستخدم --}}
            <div class="card card-custom settings-card gutter-b mb-6">
                {{-- ترويسة الصفحة (داخل النموذج ليستمر زر الحفظ) — موحّدة مع هوية القسم --}}
                <x-page-header
                    title="{{ __('معلومات المستخدم') }}"
                    subtitle="{{ __('عرض وتعديل الأدوار المعينة لهذا المستخدم') }}"
                    icon="fa-user"
                >
                    <x-slot name="actions">
                        <a href="{{ route(config('permission_ui.route_name_prefix') . 'users.index') }}" class="btn btn-warning font-weight-bolder mr-2">
                            <i class="fas fa-arrow-right ml-2"></i>{{ __('عودة') }}
                        </a>
                        <button type="submit" class="btn btn-primary font-weight-bolder">
                            <i class="fas fa-check ml-2"></i>{{ __('حفظ التعديلات') }}
                        </button>
                    </x-slot>
                </x-page-header>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 mb-6 mb-lg-0">
                            <label class="font-weight-bold text-dark mb-2">{{ __('الاسم') }}</label>
                            <div class="input-icon">
                                <input type="text" class="form-control form-control-solid form-control-lg bg-light" value="{{ $user->name }}" readonly />
                                <span><i class="fas fa-user text-muted"></i></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="font-weight-bold text-dark mb-2">{{ __('البريد الإلكتروني') }}</label>
                            <div class="input-icon">
                                <input type="text" class="form-control form-control-solid form-control-lg bg-light" value="{{ $user->email }}" readonly />
                                <span><i class="fas fa-envelope text-muted"></i></span>
                            </div>
                        </div>
                    </div>
                    @if($user->roles->count() > 0)
                    <div class="separator separator-dashed my-6"></div>
                    <div>
                        <label class="font-weight-bold text-dark mb-3">{{ __('الأدوار الحالية') }}</label>
                        <div class="d-flex flex-wrap">
                            @foreach($user->roles as $r)
                                <span class="badge badge-success badge-pill px-4 py-2 mr-2 mb-2">{{ role_name_ar($r->name) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if ($roles->count())
            {{-- بطاقة تعيين الأدوار --}}
            <div class="card card-custom settings-card gutter-b">
                <div class="card-header border-0 py-5 d-flex align-items-center flex-wrap">
                    <div class="card-title">
                        <h3 class="card-label font-weight-bolder text-dark mb-1">
                            <i class="fas fa-users text-primary ml-2"></i>
                            {{ __('تعيين الأدوار') }}
                        </h3>
                        <span class="text-muted font-weight-bold font-size-sm">{{ __('حدد الأدوار التي يجب أن يتمتع بها هذا المستخدم') }}</span>
                    </div>
                    <div class="card-toolbar ml-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-light-success font-weight-bolder" id="selectAllRoles">
                                <i class="fas fa-check ml-1"></i>{{ __('تحديد الكل') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-light-danger font-weight-bolder" id="deselectAllRoles">
                                <i class="fas fa-trash ml-1"></i>{{ __('إلغاء الكل') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-6">
                        <div class="col-lg-6 col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-solid" id="rolesSearchInput"
                                    placeholder="{{ __('ابحث عن دور...') }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-light-primary font-weight-bold" id="rolesSearchBtn">
                                        <i class="fas fa-search ml-1"></i>{{ __('بحث') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-4 mt-3 mt-md-0 text-md-right">
                            <button type="button" class="btn btn-link text-muted font-weight-bold p-0" id="clearRolesSearch" style="display:none;">
                                {{ __('إلغاء البحث') }}
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($roles as $role)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4 role-item" data-role-name="{{ mb_strtolower($role->name) }}">
                                <div class="permission-item p-4 rounded bg-light-success user-role-card h-100">
                                    <label class="permission-item-label">
                                        <span class="checkbox checkbox-outline checkbox-success checkbox-lg">
                                            <input type="checkbox" name="roles[]" class="role-checkbox" value="{{ $role->id }}" id="role-{{ $role->id }}" @checked(in_array($role->id, old('roles', [])) || $user->roles->contains($role->id))>
                                            <span></span>
                                        </span>
                                        <span class="permission-item-text">{{ role_name_ar($role->name) }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                    @enderror
                </div>
                <div class="card-footer border-0 bg-light-success">
                    <button type="submit" class="btn btn-primary font-weight-bolder">
                        <i class="fas fa-check ml-2"></i>{{ __('حفظ التعديلات') }}
                    </button>
                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'users.index') }}" class="btn btn-warning font-weight-bolder mr-2">
                        {{ __('إلغاء') }}
                    </a>
                </div>
            </div>
            @endif
        </form>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    const rolesSearchInput = document.getElementById('rolesSearchInput');
    const rolesSearchBtn = document.getElementById('rolesSearchBtn');
    const clearRolesSearch = document.getElementById('clearRolesSearch');
    const roleItems = document.querySelectorAll('.role-item');

    function normalizeText(value) {
        return (value || '').toString().trim().toLowerCase();
    }

    function filterRoles() {
        const term = normalizeText(rolesSearchInput?.value);
        let visibleCount = 0;

        roleItems.forEach(function(item) {
            const roleName = normalizeText(item.getAttribute('data-role-name'));
            const isVisible = term === '' || roleName.includes(term);
            item.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        if (clearRolesSearch) {
            clearRolesSearch.style.display = term ? 'inline-block' : 'none';
        }

        return visibleCount;
    }

    document.getElementById('selectAllRoles')?.addEventListener('click', function() {
        roleCheckboxes.forEach(cb => cb.checked = true);
    });
    document.getElementById('deselectAllRoles')?.addEventListener('click', function() {
        roleCheckboxes.forEach(cb => cb.checked = false);
    });

    rolesSearchBtn?.addEventListener('click', filterRoles);
    rolesSearchInput?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterRoles();
        }
    });
    rolesSearchInput?.addEventListener('input', function() {
        if (!rolesSearchInput.value) {
            filterRoles();
        }
    });
    clearRolesSearch?.addEventListener('click', function() {
        if (rolesSearchInput) rolesSearchInput.value = '';
        filterRoles();
    });
});
</script>
@endsection
