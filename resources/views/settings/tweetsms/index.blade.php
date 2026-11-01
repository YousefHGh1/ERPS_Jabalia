@extends('layout.master')

@section('title', 'التحكم بنظام الرسائل')
@section('page_title', __('controll_panel'))
@section('sub_main', __('home'))
@section('sub_title', 'الإعدادات / نظام الرسائل')

@section('content')
<style>
    .sms-control-page { --sms-ink:#172554; --sms-muted:#64748b; --sms-line:#e5eaf2; --sms-blue:#2563eb; --sms-teal:#0f9f9a; }
    .sms-control-page .sms-hero { position:relative; overflow:hidden; border:0; border-radius:18px; color:#fff; background:linear-gradient(120deg,#10234d 0%,#174b79 58%,#087e83 100%); box-shadow:0 12px 30px rgba(15,35,75,.16); }
    .sms-control-page .sms-hero:after { content:""; position:absolute; width:240px; height:240px; border:1px solid rgba(255,255,255,.12); border-radius:50%; inset:-110px auto auto:-35px; box-shadow:0 0 0 28px rgba(255,255,255,.035),0 0 0 58px rgba(255,255,255,.025); }
    .sms-control-page .sms-hero-content { position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; gap:20px; padding:25px 28px; }
    .sms-control-page .sms-hero-title { display:flex; align-items:center; gap:16px; }
    .sms-control-page .sms-hero-icon { display:grid; place-items:center; width:54px; height:54px; border-radius:15px; background:rgba(255,255,255,.15); font-size:23px; }
    .sms-control-page .sms-hero h2 { color:#fff; font-weight:800; font-size:21px; margin:0 0 5px; }
    .sms-control-page .sms-hero p { color:rgba(255,255,255,.76); margin:0; font-size:13px; }
    .sms-control-page .sms-state { display:inline-flex; align-items:center; gap:8px; padding:9px 13px; border-radius:999px; color:#fff; font-size:13px; font-weight:700; background:rgba(255,255,255,.14); white-space:nowrap; }
    .sms-control-page .sms-state:before { content:""; width:8px; height:8px; border-radius:50%; background:#fbbf24; }
    .sms-control-page .sms-state.is-on:before { background:#4ade80; box-shadow:0 0 0 4px rgba(74,222,128,.16); }
    .sms-control-page .sms-panel { height:100%; border:1px solid var(--sms-line); border-radius:16px; background:#fff; box-shadow:0 5px 18px rgba(15,23,42,.035); }
    .sms-control-page .sms-panel-head { display:flex; align-items:center; gap:12px; padding:19px 21px 15px; border-bottom:1px solid #f0f3f8; }
    .sms-control-page .sms-panel-icon { display:grid; place-items:center; width:39px; height:39px; flex:0 0 39px; border-radius:12px; color:var(--sms-blue); background:#eff6ff; font-size:16px; }
    .sms-control-page .sms-panel-icon.teal { color:var(--sms-teal); background:#e8fbf8; }
    .sms-control-page .sms-panel-head h3 { margin:0 0 3px; color:var(--sms-ink); font-size:15px; font-weight:800; }
    .sms-control-page .sms-panel-head p { margin:0; color:var(--sms-muted); font-size:12px; }
    .sms-control-page .sms-panel-body { padding:20px 21px; }
    .sms-control-page label { color:#334155; font-size:12px; font-weight:700; margin-bottom:7px; }
    .sms-control-page .form-control { min-height:43px; border-color:#dfe6f0; border-radius:10px; color:#1e293b; font-size:13px; text-align:right; }
    .sms-control-page .form-control:focus { border-color:#78a9f8; box-shadow:0 0 0 3px rgba(37,99,235,.10); }
    .sms-control-page .sms-help { display:block; min-height:18px; color:#8793a5; font-size:11px; margin-top:5px; }
    .sms-control-page .sms-toggle-row { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:14px 16px; border:1px solid #e6edf6; border-radius:12px; background:#f8fafd; }
    .sms-control-page .sms-toggle-copy strong { display:block; color:var(--sms-ink); font-size:13px; margin-bottom:3px; }
    .sms-control-page .sms-toggle-copy span { color:var(--sms-muted); font-size:11px; }
    .sms-control-page .sms-switch { position:relative; display:inline-block; width:48px; height:26px; flex:0 0 48px; margin:0; }
    .sms-control-page .sms-switch input { opacity:0; width:0; height:0; }
    .sms-control-page .sms-switch-slider { position:absolute; inset:0; cursor:pointer; border-radius:20px; background:#cbd5e1; transition:.2s; }
    .sms-control-page .sms-switch-slider:before { content:""; position:absolute; width:20px; height:20px; right:3px; top:3px; border-radius:50%; background:#fff; box-shadow:0 1px 4px #64748b55; transition:.2s; }
    .sms-control-page .sms-switch input:checked + .sms-switch-slider { background:#0eaaa0; }
    .sms-control-page .sms-switch input:checked + .sms-switch-slider:before { transform:translateX(-22px); }
    .sms-control-page .sms-btn { min-height:40px; border-radius:10px; padding:9px 15px; font-size:12px; font-weight:700; }
    .sms-control-page .sms-save { color:#fff; border:0; background:linear-gradient(110deg,#3187f5,#0bb7ad); box-shadow:0 6px 14px rgba(27,139,205,.18); }
    .sms-control-page .sms-save:hover { color:#fff; filter:brightness(.97); }
    .sms-control-page .sms-tool-card { height:100%; padding:20px; border:1px solid var(--sms-line); border-radius:16px; background:#fff; }
    .sms-control-page .sms-tool-title { display:flex; align-items:center; gap:11px; margin-bottom:8px; }
    .sms-control-page .sms-tool-title h3 { margin:0; color:var(--sms-ink); font-size:14px; font-weight:800; }
    .sms-control-page .sms-tool-card p { min-height:36px; color:var(--sms-muted); font-size:12px; line-height:1.7; }
    .sms-control-page .sms-alert { border:0; border-radius:11px; font-size:12px; }
    @media(max-width:767px) { .sms-control-page .sms-hero-content { align-items:flex-start; flex-direction:column; padding:21px; } .sms-control-page .sms-panel-body { padding:17px; } }
</style>

<div class="container-fluid settings-page sms-control-page" dir="rtl">
    <div class="sms-hero mb-5">
        <div class="sms-hero-content">
            <div class="sms-hero-title">
                <div class="sms-hero-icon"><i class="fas fa-sms"></i></div>
                <div><h2>التحكم بنظام الرسائل</h2><p>إدارة الاتصال وإشعارات الطلبات عبر TweetSMS</p></div>
            </div>
            <span class="sms-state {{ $enabled ? 'is-on' : '' }}">{{ $enabled ? 'الإرسال مفعّل' : 'الإرسال متوقف' }}</span>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success sms-alert">{{ session('success') }}</div> @endif
    @if(session('sms_result')) <div class="alert alert-info sms-alert">{{ session('sms_result') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger sms-alert"><ul class="mb-0 pr-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="sms-panel mb-5">
        <div class="sms-panel-head">
            <div class="sms-panel-icon"><i class="fas fa-sliders-h"></i></div>
            <div><h3>إعدادات الاتصال</h3><p>أدخل بيانات الحساب واسم المرسل المعتمد من مزود الخدمة</p></div>
        </div>
        <div class="sms-panel-body">
            <form method="POST" action="{{ route('permission_ui.tweetsms.update') }}" autocomplete="off">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-lg-6 form-group">
                        <label for="username">اسم المستخدم</label>
                        <input class="form-control" id="username" name="username" value="{{ old('username') }}" autocomplete="off" placeholder="{{ $hasUsername ? 'محفوظ مسبقاً - اكتب قيمة جديدة للتغيير' : 'اسم مستخدم TweetSMS' }}">
                        <small class="sms-help">{{ $hasUsername ? 'اترك الحقل فارغاً للاحتفاظ باسم المستخدم الحالي.' : 'لم يُسجل اسم مستخدم بعد.' }}</small>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="password">كلمة المرور</label>
                        <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" placeholder="{{ $hasPassword ? 'محفوظة مسبقاً - اكتب قيمة جديدة للتغيير' : 'كلمة مرور TweetSMS' }}">
                        <small class="sms-help">{{ $hasPassword ? 'اترك الحقل فارغاً للاحتفاظ بكلمة المرور الحالية.' : 'لم تُسجل كلمة مرور بعد.' }}</small>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="sender">اسم المرسل المعتمد</label>
                        <input class="form-control" id="sender" name="sender" value="{{ old('sender', $sender) }}" maxlength="20" placeholder="مثال: JABALIA" required>
                        <small class="sms-help">يجب أن يطابق الاسم المعتمد لدى حساب TweetSMS.</small>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="base_url">رابط واجهة المزود</label>
                        <input class="form-control" id="base_url" name="base_url" type="url" value="{{ old('base_url', $baseUrl) }}" required>
                        <small class="sms-help">عنوان API المستخدم للإرسال والاستعلام عن الرصيد.</small>
                    </div>
                    <div class="col-12 mt-1 mb-4">
                        <div class="sms-toggle-row">
                            <div class="sms-toggle-copy"><strong>إشعارات الطلبات عبر SMS</strong><span>إرسال تنبيه للموظف المعيّن عند وصول طلب جديد يحتاج للمعالجة.</span></div>
                            <label class="sms-switch" aria-label="تفعيل رسائل الطلبات"><input type="checkbox" name="enabled" value="1" {{ old('enabled', $enabled) ? 'checked' : '' }}><span class="sms-switch-slider"></span></label>
                        </div>
                    </div>
                </div>
                <button class="btn sms-btn sms-save" type="submit"><i class="fas fa-save ml-2"></i>حفظ إعدادات الرسائل</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="sms-tool-card">
                <div class="sms-tool-title"><div class="sms-panel-icon teal"><i class="fas fa-wallet"></i></div><h3>رصيد الرسائل</h3></div>
                <p>استعلم عن الرصيد المتاح في حساب TweetSMS باستخدام بيانات الاتصال المحفوظة.</p>
                <form method="POST" action="{{ route('permission_ui.tweetsms.balance') }}">@csrf
                    <button class="btn btn-light-success sms-btn" type="submit"><i class="fas fa-sync-alt ml-2"></i>فحص الرصيد</button>
                </form>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="sms-tool-card">
                <div class="sms-tool-title"><div class="sms-panel-icon"><i class="fas fa-paper-plane"></i></div><h3>رسالة تجريبية</h3></div>
                <p>أرسل رسالة قصيرة إلى هاتفك للتحقق من بيانات الحساب والمرسل والاتصال.</p>
                <form method="POST" action="{{ route('permission_ui.tweetsms.test') }}" class="form-row align-items-end">@csrf
                    <div class="col-sm-7 form-group mb-2"><label for="test-phone">رقم الهاتف</label><input id="test-phone" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="05xxxxxxxx" maxlength="30" required></div>
                    <div class="col-sm-5 form-group mb-2"><button class="btn btn-light-primary sms-btn btn-block" type="submit"><i class="fas fa-sms ml-2"></i>إرسال تجريبي</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
