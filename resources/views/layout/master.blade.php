@php
    $appearanceLocale = app()->getLocale();
    $appearanceDir = $appearanceLocale === 'ar' ? 'rtl' : 'ltr';
    // الوضع الليلي فقط عندما يكون الاختيار المحفوظ للمستخدم هو 'dark' فعلاً (أي قيمة أخرى أو فارغة = وضع فاتح)
    $userTheme = auth()->check() ? (auth()->user()->theme ?? '') : '';
    $appearanceTheme = ($userTheme === 'dark') ? 'app-theme-dark' : '';
    $appearanceFont = auth()->check() && auth()->user()->font_size ? 'app-font-' . auth()->user()->font_size : '';
    // قسم الصفحة الحالية (config/module_themes.php) — يمنح كل قسم هويته اللونية
    $moduleKey = module_theme_key();
    $moduleClass = module_theme_class($moduleKey);
    $moduleStyleVars = module_theme_style_vars($moduleKey);
@endphp
<!DOCTYPE html>
<html lang="{{ $appearanceLocale }}" dir="{{ $appearanceDir }}">
<!--begin::Head-->

<head>
    @include('layout.head')
</head>


<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" data-module="{{ $moduleKey }}"
    style="{{ $moduleStyleVars }}"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading {{ $moduleClass }} {{ $appearanceTheme }} {{ $appearanceFont }}">
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('municipality_dashboard_theme') || localStorage.getItem('theme');
                if (saved === 'dark') {
                    document.body.classList.add('app-theme-dark', 'dashboard-dark');
                } else if (saved === 'light') {
                    document.body.classList.remove('app-theme-dark', 'dashboard-dark');
                }
            } catch (e) {}
        })();
    </script>
    <!--begin::Main-->
    <!--begin::Header Mobile-->
    <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
        <!--begin::Logo-->
        <a href="{{ route('home.index') }}" class="app-mobile-brand">
            <img src="{{ asset('assets/bg/logo-light.png') }}" alt="{{ __('Municipality of Jabalia Al-Nazlah') }}" />
            <div class="d-flex flex-column">
                <span class="font-weight-bolder text-dark font-size-sm">{{ __('Municipality of Jabalia Al-Nazlah') }}</span>
                <span class="text-muted font-size-xs">{{ __('Administrative Affairs') }}</span>
            </div>
        </a>
        <!--end::Logo-->
        <!--begin::Toolbar-->
        <div class="d-flex align-items-center">
            <!--begin::Aside Mobile Toggle-->
            <button class="p-0 btn burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
                <span></span>
            </button>
            <!--end::Aside Mobile Toggle-->
            <!--begin::Topbar Mobile Toggle-->
            <button class="p-0 ml-2 btn btn-hover-text-primary" id="kt_header_mobile_topbar_toggle">
                <span class="svg-icon svg-icon-xl">
                    <!--begin::Svg Icon | path:assets/media/svg/icons/General/User.svg-->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                        height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24" />
                            <path
                                d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                fill="#000000" fill-rule="nonzero" opacity="0.3" />
                            <path
                                d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                fill="#000000" fill-rule="nonzero" />
                        </g>
                    </svg>
                    <!--end::Svg Icon-->
                </span>
            </button>
            <!--end::Topbar Mobile Toggle-->
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Header Mobile-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="flex-row d-flex flex-column-fluid page">

            <!--begin::Aside-->
            @include('layout.main-sidebar')
            <!--end::Aside-->

            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <!--begin::Header-->
                @include('layout.main-header')
                <!--end::Header-->

                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Subheader-->
                    <div class="py-2 subheader py-lg-4 subheader-solid" id="kt_subheader">
                        <div class="container-fluid">
                            <div class="app-page-heading">
                                <h5>@yield('page_title')</h5>
                                <span class="app-heading-sep" aria-hidden="true"></span>
                                <span class="app-crumb">@yield('sub_main')</span>
                                <a href="{{ url()->current() }}" class="app-crumb-current">@yield('sub_title')</a>
                            </div>
                        </div>
                    </div>
                    <!--end::Subheader-->
                    <div class="container-fluid px-3">
                        @includeIf('layout.error')
                    </div>
                    <!--begin::Entry-->
                    <div class="d-flex flex-column-fluid">
                        <!--begin::Container (max-width للمحتوى النصي على الشاشات الكبيرة)-->
                        <div class="content-max-width-wrapper">
                            @yield('content')
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Entry-->
                </div>

                <!--end::Content-->

                <!--begin::Footer-->
                @include('layout.footer')
                <!--end::Footer-->

            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Main-->

    {{-- Floating Action Button placeholder (mobile only) --}}
    @hasSection('fab')
        <div class="fab-container d-lg-none">
            @yield('fab')
        </div>
    @endif
    <!-- begin::User Panel الصفحة الشخصية-->
    <div id="kt_quick_user" class="offcanvas offcanvas-right user-panel">
        <!--begin::Header-->
        <div class="offcanvas-header user-panel-head">
            <div class="user-panel-head-title">
                <span class="user-panel-head-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <h3 class="m-0 font-weight-bold text-white">{{ __('profile_page') }}
                </h3>
            </div>
            <a href="#" class="btn btn-xs btn-icon user-panel-close" id="kt_quick_user_close" aria-label="{{ __('إغلاق') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </a>
        </div>
        <!--end::Header-->
        <!--begin::Content-->
        <div class="offcanvas-content user-panel-body">
            @php
                $qpUser = Auth::user();
                $qpName = $qpUser->name ?? '';
                $qpInitial = $qpName !== '' ? mb_substr($qpName, 0, 1) : '?';
                $qpRoles = $qpUser->roles->pluck('name');
                $qpTotalRoles = $qpRoles->count();
                $qpDisplayRoles = $qpRoles->take(3)->implode('، ');
                if ($qpTotalRoles > 3) { $qpDisplayRoles .= ' +' . ($qpTotalRoles - 3); }
                $qpEmpNo = $qpUser->employee_number ?? null;
                $qpSince = $qpUser->created_at ? $qpUser->created_at->format('Y/m/d') : null;
            @endphp
            <!--begin::User Card-->
            <div class="user-panel-card">
                <div class="user-panel-cover"></div>
                <div class="user-panel-card-inner">
                    <div class="user-panel-avatar">
                        <span class="user-panel-avatar-text">{{ $qpInitial }}</span>
                        <i class="user-panel-status" title="{{ __('متصل') }}"></i>
                    </div>
                    <div class="user-panel-info">
                        <a href="{{ route('user.profile') }}" class="user-panel-name">{{ $qpName }}</a>
                        <div class="user-panel-roles">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.5 13 17 22l-5-3-5 3 1.5-9"/></svg>
                            <span>{{ $qpDisplayRoles ?: '—' }}</span>
                        </div>
                        <div class="user-panel-email">
                            <span class="user-panel-email-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>
                            </span>
                            <span class="user-panel-email-text" dir="ltr">{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                </div>
                @if($qpEmpNo || $qpSince)
                <div class="user-panel-meta">
                    @if($qpEmpNo)
                    <div class="user-panel-meta-item">
                        <span class="user-panel-meta-label">{{ __('الرقم الوظيفي') }}</span>
                        <span class="user-panel-meta-value" dir="ltr">{{ $qpEmpNo }}</span>
                    </div>
                    @endif
                    @if($qpSince)
                    <div class="user-panel-meta-item">
                        <span class="user-panel-meta-label">{{ __('عضو منذ') }}</span>
                        <span class="user-panel-meta-value">{{ $qpSince }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            <!--end::User Card-->
            <!--begin::Actions-->
            <div class="user-panel-actions">
                <a href="{{ route('user.profile') }}" class="user-panel-btn user-panel-btn-profile">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>{{ __('الملف الشخصي') }}</span>
                </a>
                <a class="user-panel-btn user-panel-btn-logout" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>
                    <span>{{ __('Logout') }}</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
            <!--end::Actions-->
            <p class="user-panel-foot">{{ __('Municipality_ERP_System') }} · {{ date('Y') }}</p>
        </div>
        <!--end::Content-->
    </div>
    <!-- end::User Panel-->

    <!--begin::Scrolltop السهم الأزرق -->
    <div id="kt_scrolltop" class="scrolltop">
        <span class="svg-icon">
            <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Up-2.svg-->
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24" />
                    <rect fill="#000000" opacity="0.3" x="11" y="10" width="2"
                        height="10" rx="1" />
                    <path
                        d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z"
                        fill="#000000" fill-rule="nonzero" />
                </g>
            </svg>
            <!--end::Svg Icon-->
        </span>
    </div>
    <!--end::Scrolltop-->

    <!--begin::Page Scripts -->
    @include('layout.script')
    <!--end::Page Scripts -->

</body>
<!--end::Body-->

</html>
