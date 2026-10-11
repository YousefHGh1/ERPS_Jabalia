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
    <div id="kt_quick_user" class="p-10 offcanvas offcanvas-right">
        <!--begin::Header-->
        <div class="pb-5 offcanvas-header d-flex align-items-center justify-content-between">
            <h3 class="m-0 font-weight-bold text-white">{{ __('profile_page') }}
            </h3>
            <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
                <i class="fas fa-times icon-xs"></i>
            </a>
        </div>
        <!--end::Header-->
        <!--begin::Content-->
        <div class="pr-5 offcanvas-content mr-n5">
            <!--begin::Header-->
            <div class="mt-5 d-flex align-items-center">
                <div class="mr-5 symbol symbol-100">
                    <div class="symbol-label" style="background-image:url({{ asset('assets/bg/bg-1.jpg') }})"></div>
                    <i class="symbol-badge bg-success"></i>
                </div>
                <div class="d-flex flex-column">
                    <a href="{{ route('user.profile') }}"
                        class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary">{{ Auth::user()->name }}</a>
                    @php
                        $roleNames = Auth::user()->roles->pluck('name');
                        $totalRoles = $roleNames->count();
                        $displayRoles = $roleNames->take(3)->implode('، ');
                        if ($totalRoles > 3) {
                            $displayRoles .= ' +' . ($totalRoles - 3);
                        }
                    @endphp
                    <div class="mt-1 text-muted">{{ $displayRoles ?: '—' }}</div>
                    <div class="mt-2 navi">
                        <a href="#" class="navi-item">
                            <span class="p-0 pb-2 navi-link">
                                <span class="mr-1 navi-icon">
                                    <span class="svg-icon svg-icon-lg svg-icon-primary">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-notification.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <path
                                                    d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z"
                                                    fill="#000000" />
                                                <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5"
                                                    r="2.5" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </span>
                                <span class="navi-text text-muted text-hover-primary">{{ Auth::user()->email }}</span>
                            </span>
                        </a>
                        <a class="btn btn-danger text-white" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
            <!--end::Header-->
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
