@extends('layout.master')

@section('title')
    {{ __('المستخدمين') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الإعدادات') }} / {{ __('المستخدمين') }}
@endsection

@section('content')
    <div class="container-fluid settings-page">
        <div class="card card-custom settings-card gutter-b">
            <x-page-header
                title="{{ __('المستخدمين') }}"
                subtitle="{{ __('إدارة حسابات المستخدمين والأدوار') }}"
                icon="fa-user"
            >
                <x-slot name="actions">
                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'export.users') }}" class="btn btn-light-info font-weight-bolder px-5 mr-2">
                        <i class="fas fa-file-excel mr-2"></i>{{ __('تصدير') }}
                    </a>
                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'dashboard') }}" class="btn btn-light-secondary font-weight-bolder px-5">
                        <i class="fas fa-tachometer-alt mr-2"></i>{{ __('لوحة التحكم') }}
                    </a>
                </x-slot>
            </x-page-header>

            <div class="card-body pt-7">
                {{-- إحصائيات المستخدمين --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="bg-light-primary rounded p-2 text-center">
                            <div class="text-primary font-weight-bolder font-size-h6">{{ $users->total() }}</div>
                            <div class="text-muted font-size-xs">{{ __('إجمالي') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light-success rounded p-2 text-center">
                            <div class="text-success font-weight-bolder font-size-h6">{{ $users->filter(fn($u) => $u->roles->count())->count() }}</div>
                            <div class="text-muted font-size-xs">{{ __('بأدوار') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light-warning rounded p-2 text-center">
                            <div class="text-warning font-weight-bolder font-size-h6">{{ $users->filter(fn($u) => $u->employee && $u->employee->status === 'Active')->count() }}</div>
                            <div class="text-muted font-size-xs">{{ __('نشط') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light-info rounded p-2 text-center">
                            <div class="text-info font-weight-bolder font-size-h6">{{ $users->filter(fn($u) => !$u->employee || $u->employee->status !== 'Active')->count() }}</div>
                            <div class="text-muted font-size-xs">{{ __('غير نشط') }}</div>
                        </div>
                    </div>
                </div>

                @can('index users')
                    <form method="GET" class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-3">
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
                                <select name="role_filter" class="form-control">
                                    <option value="">{{ __('جميع الأدوار') }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ request('role_filter') == $role->name ? 'selected' : '' }}>
                                            {{ role_name_ar($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status_filter" class="form-control">
                                    <option value="">{{ __('جميع الحالات') }}</option>
                                    <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>
                                        {{ __('نشط') }}
                                    </option>
                                    <option value="inactive" {{ request('status_filter') == 'inactive' ? 'selected' : '' }}>
                                        {{ __('غير نشط') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-light-primary font-weight-bold px-6">
                                    {{ __('بحث') }}
                                </button>
                                @if(!empty($search ?? request('search')) || !empty(request('role_filter')) || !empty(request('status_filter')))
                                    <a href="{{ route(config('permission_ui.route_name_prefix') . 'users.index') }}"
                                       class="btn btn-link text-muted font-weight-bold">
                                        {{ __('إلغاء الفلتر') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                @endcan

            <div class="card-body p-0 pt-0">
                @can('index users')
                    <div class="table-responsive">
                        <table class="table settings-table table-head-custom table-vertical-center">
                            <thead>
                                <tr>
                                    <th class="pl-6" style="min-width: 70px">#</th>
                                    <th style="min-width: 200px">{{ __('الاسم') }}</th>
                                    <th style="min-width: 220px">{{ __('البريد الإلكتروني') }}</th>
                                    <th style="min-width: 200px">{{ __('الأدوار') }}</th>
                                    <th style="min-width: 120px">{{ __('الحالة') }}</th>
                                    <th class="text-left pr-6" style="min-width: 180px">{{ __('الإجراءات') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="pl-6 td-id">{{ $user->id }}</td>
                                        <td>
                                            <span class="text-dark-75 font-weight-bolder">{{ $user->name }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted font-weight-bold">{{ $user->email }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $roles = $user->roles;
                                            @endphp
                                            @if($roles->count())
                                                <div class="d-flex flex-wrap">
                                                    @foreach($roles as $role)
                                                        <span class="badge {{ get_role_badge_class($role->name) }} mb-1 mr-1">{{ role_name_ar($role->name) }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted font-size-sm">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->employee)
                                                @if($user->employee->status === 'Active')
                                                    <span class="badge badge-success">{{ __('نشط') }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ __('غير نشط') }}</span>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">{{ __('بدون موظف') }}</span>
                                            @endif
                                        </td>
                                        <td class="pr-6 text-left btn-actions">
                                            @can('update users')
                                                <button type="button"
                                                    class="btn btn-sm btn-light-info font-weight-bolder reset-password-btn"
                                                    data-toggle="modal"
                                                    data-target="#resetPasswordModal"
                                                    data-user-name="{{ $user->name }}"
                                                    data-action="{{ route(config('permission_ui.route_name_prefix') . 'users.reset-password', $user) }}">
                                                    <i class="fa fa-eye mr-1"></i>{{ __('تعيين كلمة مرور') }}
                                                </button>
                                                <a href="{{ route(config('permission_ui.route_name_prefix') . 'users.edit', $user) }}"
                                                    class="btn btn-sm btn-light-primary font-weight-bolder">
                                                    <i class="fas fa-pen mr-1"></i>{{ __('تعديل الأدوار') }}
                                                </a>
                                            @endcan
                                            @can('delete users')
                                                <form action="{{ route(config('permission_ui.route_name_prefix') . 'users.destroy', $user) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light-danger font-weight-bolder"
                                                        onclick="return confirm('{{ __('هل أنت متأكد من الحذف؟') }}')">
                                                        <i class="fas fa-trash mr-1"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-0">
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
                    @if($users->hasPages())
                        <div class="card-footer border-0 d-flex justify-content-center">
                            {{ $users->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent

    <!-- Modal تعيين كلمة مرور جديدة -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" id="resetPasswordForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="resetPasswordModalLabel">{{ __('تعيين كلمة مرور جديدة') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('إغلاق') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">{{ __('المستخدم') }}</label>
                            <input type="text" class="form-control" id="resetPasswordUserName" readonly>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">{{ __('كلمة المرور الجديدة') }}</label>
                            <div class="input-group">
                                <input type="password" name="password" id="resetPasswordInput" class="form-control" required minlength="8">
                                <div class="input-group-append">
                                    <button class="btn btn-light" type="button" id="toggleResetPasswordVisibility">
                                        <i class="fa fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">{{ __('تأكيد كلمة المرور') }}</label>
                            <input type="password" name="password_confirmation" id="resetPasswordConfirmInput" class="form-control" required minlength="8">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('حفظ') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#resetPasswordModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var userName = button.data('user-name') || '';
                var action = button.data('action') || '';

                $('#resetPasswordUserName').val(userName);
                $('#resetPasswordForm').attr('action', action);

                $('#resetPasswordInput, #resetPasswordConfirmInput').val('');
            });

            $('#toggleResetPasswordVisibility').on('click', function () {
                var input = $('#resetPasswordInput');
                var type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });
        });
    </script>
@endsection
