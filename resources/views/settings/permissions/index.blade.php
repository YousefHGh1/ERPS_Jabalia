@extends('layout.master')

@section('title')
    {{ __('الصلاحيات') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الإعدادات') }} / {{ __('الصلاحيات') }}
@endsection

@section('content')
    <div class="container-fluid settings-page">
        <div class="card card-custom settings-card gutter-b">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('الصلاحيات') }}"
                subtitle="{{ __('إدارة صلاحيات النظام') }}"
                icon="fa-lock"
            >
                <x-slot name="actions">
                    @can('update permissions')
                        <form action="{{ route(config('permission_ui.route_name_prefix') . 'permissions.sync-controllers') }}"
                              method="POST" class="d-inline mr-2">
                            @csrf
                            <button type="submit" class="btn btn-light-danger font-weight-bolder px-5"
                                    onclick="return confirm('{{ __('سيتم حذف جميع الصلاحيات والأدوار الحالية وإعادة إنشائها من الكنترولرات. هل أنت متأكد؟') }}')">
                                <i class="fas fa-sync-alt mr-2"></i>{{ __('إعادة إنشاء صلاحيات الأنظمة') }}
                            </button>
                        </form>
                    @endcan
                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'export.permissions') }}" class="btn btn-light-info font-weight-bolder px-5 mr-2">
                        <i class="fas fa-file-excel mr-2"></i>{{ __('تصدير') }}
                    </a>
                    @can('store permissions')
                        <a class="btn btn-primary font-weight-bolder px-5" href="{{ route(config('permission_ui.route_name_prefix') . 'permissions.create') }}">
                            <i class="fas fa-plus mr-2"></i>{{ __('إضافة صلاحية') }}
                        </a>
                    @endcan
                </x-slot>
            </x-page-header>

            {{-- إحصائيات الصلاحيات --}}
            <div class="card-body border-bottom">
                <div class="row">
                    <div class="col-md-3">
                        <div class="bg-light-primary rounded p-3 text-center">
                            <div class="text-primary font-weight-bolder font-size-h4">{{ \Spatie\Permission\Models\Permission::count() }}</div>
                            <div class="text-muted font-size-sm">{{ __('إجمالي الصلاحيات') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light-success rounded p-3 text-center">
                            <div class="text-success font-weight-bolder font-size-h4">{{ \Spatie\Permission\Models\Role::count() }}</div>
                            <div class="text-muted font-size-sm">{{ __('إجمالي الأدوار') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light-warning rounded p-3 text-center">
                            <div class="text-warning font-weight-bolder font-size-h4">{{ \Spatie\Permission\Models\Permission::where('name', 'like', 'index%')->count() }}</div>
                            <div class="text-muted font-size-sm">{{ __('صلاحيات العرض') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light-info rounded p-3 text-center">
                            <div class="text-info font-weight-bolder font-size-h4">{{ \Spatie\Permission\Models\Permission::where('name', 'like', 'delete%')->count() }}</div>
                            <div class="text-muted font-size-sm">{{ __('صلاحيات الحذف') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body pt-7">
                @can('index permissions')
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
                                <select name="type" class="form-control">
                                    <option value="">{{ __('جميع الأنواع') }}</option>
                                    <option value="index" {{ request('type') == 'index' ? 'selected' : '' }}>
                                        {{ __('عرض') }}
                                    </option>
                                    <option value="store" {{ request('type') == 'store' ? 'selected' : '' }}>
                                        {{ __('إضافة') }}
                                    </option>
                                    <option value="update" {{ request('type') == 'update' ? 'selected' : '' }}>
                                        {{ __('تعديل') }}
                                    </option>
                                    <option value="delete" {{ request('type') == 'delete' ? 'selected' : '' }}>
                                        {{ __('حذف') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mt-3 mt-md-0">
                                <button type="submit" class="btn btn-light-primary font-weight-bold px-6">
                                    {{ __('بحث') }}
                                </button>
                                @if(!empty($search ?? request('search')) || !empty(request('type')))
                                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'permissions.index') }}"
                                       class="btn btn-link text-muted font-weight-bold">
                                        {{ __('إلغاء البحث') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                @endcan

            <div class="card-body p-0 pt-0">
                @can('index permissions')
                    <div class="table-responsive">
                        <table class="table settings-table table-head-custom table-vertical-center">
                            <thead>
                                <tr>
                                    <th class="pl-6" style="min-width: 70px">#</th>
                                    <th style="min-width: 260px">{{ __('الاسم') }}</th>
                                    <th style="min-width: 140px">{{ __('تاريخ الإنشاء') }}</th>
                                    <th class="text-left pr-6" style="min-width: 180px">{{ __('الإجراءات') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $permission)
                                    <tr>
                                        <td class="pl-6 td-id">{{ $permission->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge {{ get_permission_badge_class($permission->name) }} mr-2">
                                                    @if(strpos($permission->name, 'index') === 0 || strpos($permission->name, 'view') === 0)
                                                        {{ __('عرض') }}
                                                    @elseif(strpos($permission->name, 'store') === 0 || strpos($permission->name, 'create') === 0)
                                                        {{ __('إضافة') }}
                                                    @elseif(strpos($permission->name, 'update') === 0 || strpos($permission->name, 'edit') === 0)
                                                        {{ __('تعديل') }}
                                                    @elseif(strpos($permission->name, 'delete') === 0 || strpos($permission->name, 'destroy') === 0)
                                                        {{ __('حذف') }}
                                                    @else
                                                        {{ __('أخرى') }}
                                                    @endif
                                                </span>
                                                <span class="font-weight-bold">{{ perm_name_ar($permission->name) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-muted font-weight-bold font-size-sm">
                                            {{ $permission->created_at->format('Y-m-d') }}
                                        </td>
                                        <td class="pr-6 text-left btn-actions">
                                            @can('update permissions')
                                                <a href="{{ route(config('permission_ui.route_name_prefix') . 'permissions.edit', $permission) }}"
                                                    class="btn btn-sm btn-light-primary font-weight-bolder">
                                                    <i class="fas fa-pen mr-1"></i>{{ __('تعديل') }}
                                            </a>
                                            @endcan
                                            @can('delete permissions')
                                                <form action="{{ route(config('permission_ui.route_name_prefix') . 'permissions.destroy', $permission) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light-danger font-weight-bolder"
                                                        onclick="return confirm('{{ __('هل أنت متأكد من الحذف؟') }}')">
                                                        <i class="fas fa-trash mr-1"></i>{{ __('حذف') }}
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-0">
                                            <div class="settings-empty">
                                                <div class="empty-icon"><i class="fas fa-info-circle"></i></div>
                                                <div class="empty-text">{{ __('لا توجد سجلات') }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($permissions->hasPages())
                        <div class="card-footer border-0 d-flex justify-content-center">
                            {!! $permissions->withQueryString()->links('pagination::bootstrap-4') !!}
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </div>
@endsection
