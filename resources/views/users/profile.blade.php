@extends('layout.master')

@section('title', __('الملف الشخصي'))

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('الملف الشخصي') }}
@endsection
@section('sub_title')
    {{ __('المعلومات والإعدادات') }}
@endsection

@section('content')
<div class="container-fluid">
    @php
        $u = $user ?? auth()->user();
        $emp = $u->employee;
        $dept = $emp?->department;
        $section = $emp?->section;
        $job = $emp?->job;
    @endphp

    {{-- بطاقة رأس الملف الشخصي --}}
    <div class="card card-custom gutter-b mb-6">
        <div class="card-body p-6">
            <div class="d-flex flex-wrap align-items-center">
                <div class="symbol symbol-120 symbol-circle mr-6 mb-4 mb-md-0">
                    @if($u->avatar)
                        <img src="{{ $u->avatar }}" alt="{{ $u->name }}" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <span class="symbol-label font-size-h1 font-weight-bold text-primary rounded-circle" style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center; background: #E1F0FF;">{{ mb_substr($u->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <h2 class="font-weight-bolder text-dark mb-2">{{ $u->name }}</h2>
                    <div class="text-muted font-weight-bold font-size-sm mb-1">
                        <i class="fas fa-envelope text-primary ml-1"></i> {{ $u->email }}
                    </div>
                    @if($u->mobile)
                        <div class="text-muted font-weight-bold font-size-sm mb-1">
                            <i class="fas fa-phone text-info ml-1"></i> {{ $u->mobile }}
                        </div>
                    @endif
                    @if($dept)
                        <div class="text-muted font-weight-bold font-size-sm mb-1">
                            <i class="fas fa-building text-success ml-1"></i> {{ $dept->name }}
                        </div>
                    @endif
                    @if($job)
                        <div class="text-muted font-weight-bold font-size-sm">
                            <i class="fas fa-user-tie text-warning ml-1"></i> {{ $job->name }}
                        </div>
                    @endif
                </div>
                <div class="d-flex flex-wrap flex-column flex-md-row gap-3 mt-3 mt-md-0">
                    <div class="bg-light-primary rounded p-4 text-center min-w-100px">
                        <span class="font-weight-bolder text-primary font-size-h4 d-block">{{ $u->created_at->diffInDays(now()) }}</span>
                        <span class="text-muted font-size-sm font-weight-bold">{{ __('أيام في النظام') }}</span>
                    </div>
                    <div class="bg-light-success rounded p-4 text-center min-w-100px">
                        <span class="font-weight-bolder text-success font-size-h4 d-block">{{ $u->getAllPermissions()->count() }}</span>
                        <span class="text-muted font-size-sm font-weight-bold">{{ __('صلاحية') }}</span>
                    </div>
                    <div class="bg-light-info rounded p-4 text-center min-w-100px">
                        <span class="font-weight-bolder text-info font-size-h4 d-block">{{ $u->roles->count() }}</span>
                        <span class="text-muted font-size-sm font-weight-bold">{{ __('دور') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- تبويبات المحتوى --}}
    <div class="card card-custom gutter-b">
        <div class="card-header border-0 pt-6">
            <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-primary font-weight-bold flex-nowrap " role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-info" role="tab">
                        <i class="fas fa-user ml-2"></i>{{ __('المعلومات الشخصية') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-security" role="tab">
                        <i class="fas fa-shield-alt ml-2"></i>{{ __('الأمان') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-activity" role="tab">
                        <i class="fas fa-history ml-2"></i>{{ __('النشاط') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-settings" role="tab">
                        <i class="fas fa-cog ml-2"></i>{{ __('الإعدادات') }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body pt-4">
            <div class="tab-content">
                {{-- تبويب المعلومات الشخصية --}}
                <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-4 mb-6">
                            <div class="card card-custom card-stretch border border-secondary">
                                <div class="card-header border-0 py-4">
                                    <h3 class="card-title font-weight-bolder">
                                        <i class="fas fa-user text-primary ml-2"></i>{{ __('المعلومات الأساسية') }}
                                    </h3>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="d-flex justify-content-between py-3 border-bottom border-light">
                                        <span class="text-muted font-weight-bold">{{ __('الاسم الكامل') }}</span>
                                        <span class="font-weight-bolder text-dark">{{ $u->name }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-3 border-bottom border-light">
                                        <span class="text-muted font-weight-bold">{{ __('البريد الإلكتروني') }}</span>
                                        <span class="font-weight-bolder text-dark">{{ $u->email }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-3 border-bottom border-light">
                                        <span class="text-muted font-weight-bold">{{ __('رقم الهاتف') }}</span>
                                        <span class="font-weight-bolder text-dark">{{ $u->mobile ?? '—' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-3">
                                        <span class="text-muted font-weight-bold">{{ __('تاريخ الإنشاء') }}</span>
                                        <span class="font-weight-bolder text-dark">{{ $u->created_at->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-6">
                            <div class="card card-custom card-stretch border border-secondary">
                                <div class="card-header border-0 py-4">
                                    <h3 class="card-title font-weight-bolder">
                                        <i class="fas fa-briefcase text-success ml-2"></i>{{ __('معلومات العمل') }}
                                    </h3>
                                </div>
                                <div class="card-body pt-0">
                                    @if($emp)
                                        <div class="d-flex justify-content-between py-3 border-bottom border-light">
                                            <span class="text-muted font-weight-bold">{{ __('الرقم الوظيفي') }}</span>
                                            <span class="font-weight-bolder text-dark">{{ $emp->czn_id ?? '—' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between py-3 border-bottom border-light">
                                            <span class="text-muted font-weight-bold">{{ __('الإدارة') }}</span>
                                            <span class="font-weight-bolder text-dark">{{ $dept?->name ?? '—' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between py-3 border-bottom border-light">
                                            <span class="text-muted font-weight-bold">{{ __('القسم') }}</span>
                                            <span class="font-weight-bolder text-dark">{{ $section?->name ?? '—' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between py-3">
                                            <span class="text-muted font-weight-bold">{{ __('الوظيفة') }}</span>
                                            <span class="font-weight-bolder text-dark">{{ $job?->name ?? '—' }}</span>
                                        </div>
                                    @else
                                        <p class="text-muted text-center py-6 mb-0">{{ __('لا توجد معلومات وظيفية مرتبطة بهذا الحساب') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-6">
                            <div class="card card-custom card-stretch border border-secondary">
                                <div class="card-header border-0 py-4">
                                    <h3 class="card-title font-weight-bolder">
                                        <i class="fas fa-key text-info ml-2"></i>{{ __('الصلاحيات والأدوار') }}
                                    </h3>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom border-light">
                                        <span class="text-muted font-weight-bold">{{ __('الأدوار') }}</span>
                                        <div class="d-flex flex-wrap justify-content-end">
                                            @forelse($u->roles as $role)
                                                <span class="label label-lg label-light-primary label-inline font-weight-bold ml-1 mb-1">{{ $role->name }}</span>
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between py-3 border-bottom border-light">
                                        <span class="text-muted font-weight-bold">{{ __('عدد الصلاحيات') }}</span>
                                        <span class="font-weight-bolder text-dark">{{ $u->getAllPermissions()->count() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-3">
                                        <span class="text-muted font-weight-bold">{{ __('آخر تسجيل دخول') }}</span>
                                        <span class="font-weight-bolder text-dark">{{ $u->last_login ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- تبويب الأمان --}}
                <div class="tab-pane fade" id="tab-security" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-6">
                            <div class="card card-custom border border-secondary">
                                <div class="card-header border-0 py-4">
                                    <h3 class="card-title font-weight-bolder">
                                        <i class="fas fa-shield-alt text-warning ml-2"></i>{{ __('حالة الأمان') }}
                                    </h3>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="d-flex align-items-center justify-content-between p-4 bg-light-warning rounded mb-4">
                                        <div>
                                            <span class="font-weight-bolder text-dark d-block">{{ __('المصادقة الثنائية (2FA)') }}</span>
                                            <span class="text-muted font-size-sm">{{ __('إضافة طبقة أمان إضافية لحسابك') }}</span>
                                        </div>
                                        <span class="label label-lg label-light-danger label-inline font-weight-bold">{{ __('غير مفعل') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between p-4 bg-light rounded">
                                        <div>
                                            <span class="font-weight-bolder text-dark d-block">{{ __('تسجيل الخروج من جميع الأجهزة') }}</span>
                                            <span class="text-muted font-size-sm">{{ __('إنهاء جميع الجلسات النشطة') }}</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light-danger font-weight-bolder" onclick="logoutAllDevices()">
                                            <i class="fas fa-sign-out-alt ml-1"></i>{{ __('تنفيذ') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-6">
                            <div class="card card-custom border border-secondary">
                                <div class="card-header border-0 py-4">
                                    <h3 class="card-title font-weight-bolder">
                                        <i class="fas fa-key text-primary ml-2"></i>{{ __('تغيير كلمة المرور') }}
                                    </h3>
                                </div>
                                <div class="card-body pt-0">
                                    <form id="password-form">
                                        @csrf
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('كلمة المرور الحالية') }}</label>
                                            <input type="password" name="current_password" required class="form-control form-control-solid">
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('كلمة المرور الجديدة') }}</label>
                                            <div class="input-group">
                                                <input type="password" name="new_password" id="new_password" required class="form-control form-control-solid" onkeyup="checkPasswordStrength(this.value)">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('new_password', this)">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <div class="progress h-6 mt-1" style="height: 6px;">
                                                    <div id="strength-fill" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                                </div>
                                                <small id="strength-text" class="text-muted">{{ __('أدخل كلمة مرور قوية') }}</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">{{ __('تأكيد كلمة المرور') }}</label>
                                            <input type="password" name="password_confirmation" required class="form-control form-control-solid">
                                        </div>
                                        <button type="submit" class="btn btn-primary font-weight-bolder">
                                            <i class="fas fa-save ml-2"></i>{{ __('حفظ التغييرات') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- تبويب النشاط --}}
                <div class="tab-pane fade" id="tab-activity" role="tabpanel">
                    <div class="card card-custom border border-secondary">
                        <div class="card-header border-0 py-4">
                            <h3 class="card-title font-weight-bolder">
                                <i class="fas fa-history text-info ml-2"></i>{{ __('النشاط الأخير') }}
                            </h3>
                        </div>
                        <div class="card-body pt-0">
                            @if(isset($auditLogs) && $auditLogs->count() > 0)
                                @foreach($auditLogs as $log)
                                    @php
                                        $description = is_object($log) && isset($log->description) ? $log->description : (is_array($log) && isset($log['description']) ? $log['description'] : __('نشاط'));
                                        $subjectType = is_object($log) && isset($log->subject_type) ? $log->subject_type : (is_array($log) && isset($log['subject_type']) ? $log['subject_type'] : null);
                                        $createdAt = is_object($log) && isset($log->created_at) ? $log->created_at : (is_array($log) && isset($log['created_at']) ? $log['created_at'] : null);
                                        $colorClass = is_object($log) && isset($log->color_class) ? $log->color_class : (is_array($log) && isset($log['color_class']) ? $log['color_class'] : 'primary');
                                        $icon = is_object($log) && isset($log->icon) ? $log->icon : (is_array($log) && isset($log['icon']) ? $log['icon'] : 'fas fa-circle');
                                        $formattedDate = $createdAt ? (is_string($createdAt) ? $createdAt : (is_object($createdAt) && method_exists($createdAt, 'format') ? $createdAt->format('Y-m-d H:i') : '')) : '';
                                    @endphp
                                    <div class="d-flex align-items-start p-4 border-bottom border-light">
                                        <div class="symbol symbol-40 symbol-light-{{ $colorClass }} mr-4 flex-shrink-0">
                                            <span class="symbol-label"><i class="{{ $icon }} text-{{ $colorClass }}"></i></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="font-weight-bolder text-dark d-block">{{ $description }}</span>
                                            @if($subjectType)
                                                <span class="text-muted font-size-sm">{{ __('نوع النشاط') }}: {{ class_basename($subjectType) }}</span>
                                            @endif
                                            <span class="text-muted font-size-sm d-block mt-1">{{ $formattedDate }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif(isset($attendanceRecords) && $attendanceRecords->count() > 0)
                                @foreach($attendanceRecords as $record)
                                    <div class="d-flex align-items-start p-4 border-bottom border-light">
                                        <div class="symbol symbol-40 symbol-light-success mr-4 flex-shrink-0">
                                            <span class="symbol-label"><i class="fas fa-clock text-success"></i></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="font-weight-bolder text-dark d-block">{{ __('سجل حضور') }}</span>
                                            <span class="text-muted font-size-sm">
                                                @if(isset($record->arrival_time)){{ __('الوصول') }}: {{ $record->arrival_time }}@endif
                                                @if(isset($record->departure_time)){{ __('المغادرة') }}: {{ $record->departure_time }}@endif
                                            </span>
                                            <span class="text-muted font-size-sm d-block mt-1">{{ isset($record->date) ? $record->date : now()->format('Y-m-d') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-inbox text-muted font-size-h1 mb-4"></i>
                                    <p class="text-muted font-weight-bold">{{ __('لا يوجد نشاط مسجل') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- تبويب الإعدادات --}}
                <div class="tab-pane fade" id="tab-settings" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6 mb-6">
                            <div class="card card-custom border border-secondary">
                                <div class="card-header border-0 py-4">
                                    <h3 class="card-title font-weight-bolder">
                                        <i class="fas fa-bell text-warning ml-2"></i>{{ __('الإشعارات') }}
                                    </h3>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="d-flex align-items-center justify-content-between py-4 border-bottom border-light">
                                        <span class="font-weight-bold text-dark">{{ __('الإشعارات البريد الإلكتروني') }}</span>
                                        <span class="switch switch-sm switch-icon">
                                            <label><input type="checkbox" id="notif-email" {{ ($u->email_notifications ?? true) ? 'checked' : '' }} onchange="saveNotificationSettings()"><span></span></label>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between py-4 border-bottom border-light">
                                        <span class="font-weight-bold text-dark">{{ __('إشعارات النظام') }}</span>
                                        <span class="switch switch-sm switch-icon">
                                            <label><input type="checkbox" id="notif-system" {{ ($u->system_notifications ?? true) ? 'checked' : '' }} onchange="saveNotificationSettings()"><span></span></label>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between py-4">
                                        <span class="font-weight-bold text-dark">{{ __('إشعارات الهاتف') }}</span>
                                        <span class="switch switch-sm switch-icon">
                                            <label><input type="checkbox" id="notif-sms" {{ ($u->sms_notifications ?? false) ? 'checked' : '' }} onchange="saveNotificationSettings()"><span></span></label>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-6">
                            <div class="card card-custom border border-secondary">
                                <div class="card-header border-0 py-4">
                                    <h3 class="card-title font-weight-bolder">
                                        <i class="fas fa-palette text-info ml-2"></i>{{ __('المظهر') }}
                                    </h3>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">{{ __('الوضع الليلي') }}</label>
                                        <div class="mt-2">
                                            <span class="switch switch-sm switch-icon">
                                                <label>
                                                    <input type="checkbox" id="appearance-theme" {{ ($u->theme ?? '') === 'dark' ? 'checked' : '' }}>
                                                    <span></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark">{{ __('لغة الواجهة') }}</label>
                                        <select class="form-control form-control-solid mt-2" id="appearance-language">
                                            <option value="ar" {{ ($u->language ?? 'ar') === 'ar' ? 'selected' : '' }}>{{ __('العربية') }}</option>
                                            <option value="en" {{ ($u->language ?? '') === 'en' ? 'selected' : '' }}>English</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-dark">{{ __('حجم الخط') }}</label>
                                        <select class="form-control form-control-solid mt-2" id="appearance-font-size">
                                            <option value="small" {{ ($u->font_size ?? 'medium') === 'small' ? 'selected' : '' }}>{{ __('صغير') }}</option>
                                            <option value="medium" {{ ($u->font_size ?? 'medium') === 'medium' ? 'selected' : '' }}>{{ __('متوسط') }}</option>
                                            <option value="large" {{ ($u->font_size ?? '') === 'large' ? 'selected' : '' }}>{{ __('كبير') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePasswordVisibility(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

function checkPasswordStrength(password) {
    var strength = 0;
    var text = '{{ __('أدخل كلمة مرور قوية') }}';
    var pct = 0;
    if (password.length >= 8) { strength += 25; pct += 25; }
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) { strength += 25; pct += 25; }
    if (password.match(/[0-9]/)) { strength += 25; pct += 25; }
    if (password.match(/[^a-zA-Z0-9]/)) { strength += 25; pct += 25; }
    var fill = document.getElementById('strength-fill');
    var label = document.getElementById('strength-text');
    if (password.length === 0) { text = '{{ __('أدخل كلمة مرور قوية') }}'; pct = 0; }
    else if (strength < 30) { text = '{{ __('ضعيفة جداً') }}'; fill.className = 'progress-bar bg-danger'; }
    else if (strength < 50) { text = '{{ __('ضعيفة') }}'; fill.className = 'progress-bar bg-danger'; }
    else if (strength < 70) { text = '{{ __('متوسطة') }}'; fill.className = 'progress-bar bg-warning'; }
    else if (strength < 90) { text = '{{ __('جيدة') }}'; fill.className = 'progress-bar bg-info'; }
    else { text = '{{ __('قوية جداً') }}'; fill.className = 'progress-bar bg-success'; }
    fill.style.width = pct + '%';
    label.textContent = text;
}

function logoutAllDevices() {
    if (!confirm('{{ __('هل أنت متأكد من تسجيل الخروج من جميع الأجهزة؟') }}')) return;
    fetch('{{ route("user.profile.session") }}', { method: 'GET', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) { alert('{{ __('تم تسجيل الخروج من جميع الأجهزة بنجاح') }}'); window.location.reload(); }
            else { alert('{{ __('حدث خطأ أثناء تنفيذ العملية') }}'); }
        })
        .catch(function() { alert('{{ __('حدث خطأ أثناء تنفيذ العملية') }}'); });
}

function saveNotificationSettings() {
    var emailNotif = document.getElementById('notif-email').checked;
    var systemNotif = document.getElementById('notif-system').checked;
    var smsNotif = document.getElementById('notif-sms').checked;
    
    fetch('{{ route("user.profile.notifications") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
            email_notifications: emailNotif,
            system_notifications: systemNotif,
            sms_notifications: smsNotif
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success && typeof toastr !== 'undefined') toastr.success(data.message);
        else if (data.success) alert(data.message);
    })
    .catch(function() { alert('{{ __('حدث خطأ أثناء حفظ الإعدادات') }}'); });
}

document.getElementById('password-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var fd = new FormData(this);
    var payload = {
        current_password: fd.get('current_password'),
        new_password: fd.get('new_password'),
        new_password_confirmation: fd.get('password_confirmation')
    };
    fetch('{{ route("user.profile.password") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            alert('{{ __('تم تغيير كلمة المرور بنجاح') }}');
            this.reset();
            document.getElementById('strength-fill').style.width = '0%';
            document.getElementById('strength-text').textContent = '{{ __('أدخل كلمة مرور قوية') }}';
        } else {
            alert(data.message || '{{ __('يرجى المحاولة مرة أخرى') }}');
        }
    }.bind(this))
    .catch(function() { alert('{{ __('حدث خطأ أثناء تغيير كلمة المرور') }}'); });
});

(function() {
    var appearanceUrl = '{{ route("user.profile.appearance") }}';
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    function saveAppearance(theme, language, fontSize) {
        var themeVal = (theme !== undefined && theme !== null) ? theme : (document.getElementById('appearance-theme').checked ? 'dark' : 'light');
        var langVal = (language !== undefined && language !== null) ? language : document.getElementById('appearance-language').value;
        var fontVal = (fontSize !== undefined && fontSize !== null) ? fontSize : document.getElementById('appearance-font-size').value;
        fetch(appearanceUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                theme: themeVal,
                language: langVal,
                font_size: fontVal
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success && typeof toastr !== 'undefined') toastr.success(data.message);
            else if (data.success) alert(data.message);
        });
    }

    var themeEl = document.getElementById('appearance-theme');
    if (themeEl) {
        themeEl.addEventListener('change', function() {
            var theme = this.checked ? 'dark' : 'light';
            saveAppearance(theme, null, null);
            document.body.classList.toggle('app-theme-dark', this.checked);
        });
    }

    var langEl = document.getElementById('appearance-language');
    if (langEl) {
        langEl.addEventListener('change', function() {
            saveAppearance(null, this.value, null);
            window.location.reload();
        });
    }

    var fontEl = document.getElementById('appearance-font-size');
    if (fontEl) {
        fontEl.addEventListener('change', function() {
            var font = this.value;
            saveAppearance(null, null, font);
            document.body.classList.remove('app-font-small', 'app-font-medium', 'app-font-large');
            document.body.classList.add('app-font-' + font);
        });
    }
})();
</script>
@endsection
