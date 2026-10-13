    <link href="{{ asset('assets/bg/favicon.png') }}" rel="icon" type="image/png" />
    {{-- PWA: نسخة موبايل — تثبيت التطبيق على الشاشة الرئيسية --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#3699FF">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ asset('assets/bg/favicon.png') }}">

    <!--begin::Fonts (font-display: swap لتحسين الأداء - النص يظهر فوراً بخط احتياطي ثم يُستبدل)-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900;1000&display=swap"
        rel="stylesheet">
    <!--end::Fonts-->

    <!--begin::Icons - توحيد مكتبة الأيقونات على Font Awesome 6 -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!--end::Icons-->

    {{-- title --}}
    <title>@yield('title')</title>
    <meta name="description" content="بلدية جباليا النزلة - الشؤون الإدارية. نظام إدارة الخدمات والمرافق البلدية والطلبات والشكاوى.">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="canonical" href="#">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- title --}}

    {{-- RTL Style Links: في الإنتاج يُحمّل ملف واحد مجمّع إذا وُجد (npm run build:css) --}}
    @php
        $cssBundlePath = public_path('assets/css/vendor.bundle.rtl.min.css');
        $useCssBundle = app()->environment('production') && file_exists($cssBundlePath);
    @endphp
    @if($useCssBundle)
    <link href="{{ asset('assets/css/vendor.bundle.rtl.min.css') }}" rel="stylesheet" type="text/css">
    @else
    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.rtl.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css">
    <!--end::Global Theme Styles-->
    <!--begin::Layout Themes(used by all pages)-->
    <link href="{{ asset('assets/css/themes/layout/header/base/light.rtl.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/themes/layout/header/menu/light.rtl.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/themes/layout/brand/dark.rtl.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/themes/layout/aside/dark.rtl.css') }}" rel="stylesheet" type="text/css">
    @endif
    <!--end::Layout Themes-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}">

    @include('sweetalert::alert')

    <link rel="stylesheet" href="{{ asset('assets/css/custom-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app-shell.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/views-theme.css') }}">
    {{-- هوية كل قسم (config/module_themes.php) — تعتمد متغيرات --module-* من <body> --}}
    <link rel="stylesheet" href="{{ asset('assets/css/module-themes.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/user-panel.css') }}">
    {{-- أنماط صفحات الـ admin الموحدة --}}
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dashboard.css') }}">
    {{-- أنماط بوابة الموظف --}}
    <link rel="stylesheet" href="{{ asset('assets/css/employee-portal.css') }}">
    @yield('css')
    @stack('css')
    <style>
        /* إعدادات حجم الخط من الملف الشخصي */
        body.app-font-small { font-size: 14px; }
        body.app-font-small .font-size-sm { font-size: 12px; }
        body.app-font-small .font-size-lg { font-size: 15px; }
        body.app-font-medium { font-size: 16px; }
        body.app-font-medium .font-size-sm { font-size: 13px; }
        body.app-font-medium .font-size-lg { font-size: 17px; }
        body.app-font-large { font-size: 18px; }
        body.app-font-large .font-size-sm { font-size: 14px; }
        body.app-font-large .font-size-lg { font-size: 20px; }
    </style>
