@extends('layout.master')

@section('title')
{{ __('Municipality_ERP_System') }}
@endsection

@section('page_title')
{{ __('controll_panel') }}
@endsection

@section('sub_main')
{{ __('home') }}
@endsection

@section('sub_title')
{{ __('Municipality_ERP_System') }}
@endsection

@section('css')
<style>
    :root {
        --municipal-primary: #3699FF;
        --municipal-secondary: #1BC5BD;
        --municipal-accent: #FFA800;
        --municipal-danger: #F64E60;
        --municipal-success: #16A085;
        --municipal-info: #8950FC;
        --municipal-dark: #181C32;
        --dashboard-bg: #f4f7fb;
        --card-bg: rgba(255,255,255,.96);
        --card-border: rgba(24,28,50,.07);
        --soft-shadow: 0 8px 28px rgba(24,28,50,.06);
        --strong-shadow: 0 16px 42px rgba(24,28,50,.12);
        --primary-gradient: linear-gradient(135deg,#3699FF 0%,#1BC5BD 100%);
        --success-gradient: linear-gradient(135deg,#0f9b8e 0%,#38d996 100%);
        --warning-gradient: linear-gradient(135deg,#f2994a 0%,#FFA800 100%);
        --danger-gradient: linear-gradient(135deg,#eb3349 0%,#F64E60 100%);
        --info-gradient: linear-gradient(135deg,#8950FC 0%,#3699FF 100%);
        --dark-gradient: linear-gradient(135deg,#3F4254 0%,#181C32 100%);
        --muted: #7d8597;
        --surface-soft: #f7f9fc;
        --line: #edf0f5;
    }


    /* Self-contained SVG icon system — no icon font dependency */
    .dashboard-svg-sprite { position:absolute; width:0; height:0; overflow:hidden; pointer-events:none; }
    .svg-icon {
        width:1.2rem;
        height:1.2rem;
        min-width:1.2rem;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        vertical-align:middle;
        flex:0 0 auto;
        fill:none;
        stroke:currentColor;
        stroke-width:1.9;
        stroke-linecap:round;
        stroke-linejoin:round;
        color:inherit;
    }
    .svg-icon.icon-lg { width:1.45rem; height:1.45rem; min-width:1.45rem; }
    .svg-icon.attention-arrow { width:1rem; height:1rem; min-width:1rem; }
    .svg-icon.mr-1 { margin-right:.25rem; }
    .quick-action-card .quick-icon .svg-icon { width:1.2rem; height:1.2rem; min-width:1.2rem; }
    .dashboard-theme-toggle .theme-icon { width:1.2rem; height:1.2rem; }
    .chart-state .chart-state-icon { width:30px; height:30px; opacity:.55; }

    .dashboard-page {
        position: relative;
        min-height: 100vh;
        direction: rtl;
        background:
            radial-gradient(circle at 7% 7%,rgba(54,153,255,.09),transparent 21%),
            radial-gradient(circle at 94% 20%,rgba(27,197,189,.08),transparent 21%),
            radial-gradient(circle at 78% 90%,rgba(255,168,0,.06),transparent 19%),
            var(--dashboard-bg);
        overflow: clip;
        padding-bottom: 24px;
    }

    .dashboard-page > .container-fluid { position:relative; z-index:1; }
    .dashboard-page a { text-decoration:none; }
    .dashboard-page button,
    .dashboard-page a { -webkit-tap-highlight-color:transparent; }
    .dashboard-page :focus-visible {
        outline:3px solid rgba(54,153,255,.35);
        outline-offset:3px;
        border-radius:8px;
    }

    .dashboard-page::before,
    .dashboard-page::after {
        content:'';
        position:fixed;
        width:260px;
        height:260px;
        border-radius:50%;
        filter:blur(75px);
        pointer-events:none;
        opacity:.14;
        z-index:0;
    }
    .dashboard-page::before { background:#3699FF; top:90px; left:-150px; animation:dashboardFloat 16s ease-in-out infinite; }
    .dashboard-page::after { background:#1BC5BD; bottom:-140px; right:-130px; animation:dashboardFloat 19s ease-in-out infinite reverse; }

    @keyframes dashboardFloat {
        0%,100% { transform:translate3d(0,0,0); }
        50% { transform:translate3d(18px,-14px,0); }
    }

    .municipal-dashboard-header {
        position:relative;
        overflow:hidden;
        border-radius:22px;
        padding:26px 30px;
        margin-bottom:24px;
        color:#fff;
        background:linear-gradient(135deg,#3699FF 0%,#1BC5BD 74%,#159f9a 100%);
        box-shadow:0 16px 40px rgba(54,153,255,.20);
    }
    .municipal-dashboard-header::before,
    .municipal-dashboard-header::after {
        content:'';
        position:absolute;
        border-radius:50%;
        border:1px solid rgba(255,255,255,.15);
        pointer-events:none;
    }
    .municipal-dashboard-header::before { width:240px;height:240px;right:-85px;top:-120px; }
    .municipal-dashboard-header::after { width:150px;height:150px;left:33%;bottom:-105px; }
    .municipal-dashboard-header .header-content { position:relative;z-index:2; }
    .municipal-dashboard-header .brand-icon {
        width:60px;height:60px;border-radius:17px;
        display:inline-flex;align-items:center;justify-content:center;
        background:rgba(255,255,255,.16);
        border:1px solid rgba(255,255,255,.22);
        box-shadow:inset 0 1px 0 rgba(255,255,255,.16),0 8px 24px rgba(0,0,0,.08);
        backdrop-filter:blur(12px);
    }
    .municipal-dashboard-header .brand-icon i { font-size:26px;color:#fff; }
    .municipal-dashboard-header .welcome-title { font-size:1.5rem;font-weight:800;margin:0;letter-spacing:-.2px; }
    .municipal-dashboard-header .welcome-subtitle { opacity:.88;font-size:.9rem;margin-top:4px; }
    .municipal-dashboard-header .date-pill {
        display:inline-flex;align-items:center;gap:8px;
        padding:8px 13px;border-radius:11px;
        background:rgba(255,255,255,.12);
        border:1px solid rgba(255,255,255,.18);
        backdrop-filter:blur(10px);
        font-size:.82rem;font-weight:700;
    }

    .dashboard-section-title {
        display:flex;align-items:center;justify-content:space-between;
        gap:15px;margin-bottom:14px;
    }
    .dashboard-section-title .title-wrap { display:flex;align-items:center;gap:10px; }
    .dashboard-section-title .title-icon {
        width:38px;height:38px;border-radius:11px;
        display:flex;align-items:center;justify-content:center;
        background:rgba(54,153,255,.10);color:#3699FF;
    }
    .dashboard-section-title h3 { margin:0;font-weight:800;color:#181C32;font-size:1.08rem; }
    .dashboard-section-title p { margin:3px 0 0;color:#8b93a7;font-size:.76rem; }

    .card-custom {
        transition:transform .3s ease,box-shadow .3s ease,border-color .3s ease;
        border:1px solid var(--card-border) !important;
        border-radius:19px !important;
        overflow:hidden;
        background:var(--card-bg);
        box-shadow:var(--soft-shadow) !important;
    }
    .card-custom:hover { transform:translateY(-3px);box-shadow:var(--strong-shadow) !important; }

    .stat-card { position:relative;isolation:isolate; }
    .stat-card::after {
        content:'';
        position:absolute;inset:0 0 auto 0;height:3px;
        background:var(--primary-gradient);
        opacity:.9;
        transform:scaleX(.55);
        transform-origin:right;
        transition:transform .32s ease;
    }
    .stat-card:hover::after { transform:scaleX(1); }
    .stat-card .card-body { position:relative;z-index:1; }
    .stat-card .icon-box {
        width:54px;height:54px;border-radius:15px;
        display:flex;align-items:center;justify-content:center;
        box-shadow:0 8px 20px rgba(0,0,0,.10);
        transition:transform .3s ease;
        flex:0 0 54px;
    }
    .stat-card:hover .icon-box { transform:scale(1.06) rotate(-2deg); }
    .stat-card h2 { letter-spacing:-.5px;line-height:1.1; }
    .stat-card .kpi-description {
        color:#8b93a7;
        font-size:.74rem;
        line-height:1.55;
        margin-top:7px;
        min-height:23px;
    }
    .stat-card .btn {
        border-radius:10px;
        transition:all .25s ease;
    }
    .stat-card .btn:hover { transform:translateY(-1px); }

    .bg-primary-grad { background:var(--primary-gradient); }
    .bg-success-grad { background:var(--success-gradient); }
    .bg-warning-grad { background:var(--warning-gradient); }
    .bg-danger-grad { background:var(--danger-gradient); }
    .bg-info-grad { background:var(--info-gradient); }
    .bg-dark-grad { background:var(--dark-gradient); }

    .attention-card {
        border:1px solid rgba(255,168,0,.13);
        border-radius:19px;
        background:rgba(255,255,255,.97);
        box-shadow:var(--soft-shadow);
        overflow:hidden;
    }
    .attention-card .attention-head {
        padding:17px 20px;
        background:linear-gradient(135deg,rgba(255,168,0,.09),rgba(246,78,96,.035));
        border-bottom:1px solid rgba(255,168,0,.10);
    }
    .attention-item {
        display:flex;align-items:center;justify-content:space-between;gap:12px;
        min-height:76px;padding:13px 17px;
        text-decoration:none !important;
        border-bottom:1px solid var(--line);
        transition:background .22s ease,transform .22s ease;
    }
    .attention-item:last-child { border-bottom:0; }
    .attention-item:hover { background:#f8fbff;transform:translateX(-2px); }
    .attention-icon {
        width:40px;height:40px;border-radius:12px;
        display:flex;align-items:center;justify-content:center;
        flex:0 0 40px;
    }
    .attention-value { font-size:1.15rem;font-weight:800;color:#181C32;line-height:1.1; }
    .attention-label { color:#6f778a;font-size:.79rem;font-weight:700; }
    .attention-arrow { color:#a0a7b5;font-size:14px; }

    .dashboard-chart-card .card-header,
    .dashboard-table-card .card-header { padding:19px 21px 9px; }
    .dashboard-chart-card .card-body,
    .dashboard-table-card .card-body { padding:11px 21px 21px; }

    .chart-shell {
        position:relative;
        min-height:320px;
        border-radius:14px;
        background:linear-gradient(180deg,rgba(247,249,252,.72),rgba(255,255,255,.32));
        overflow:hidden;
    }
    .chart-container { min-height:320px;width:100%;position:relative; }
    #dept_chart { min-height:315px;width:100%;position:relative; }
    .chart-state {
        min-height:220px;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        text-align:center;
        color:#8b93a7;
        gap:7px;
    }
    .chart-state i { font-size:30px;opacity:.55; }
    .chart-state strong { color:#5e6678;font-size:.9rem; }
    .chart-state span { font-size:.75rem; }
    .chart-loading::after {
        content:'';
        width:28px;height:28px;
        border:3px solid rgba(54,153,255,.15);
        border-top-color:#3699FF;
        border-radius:50%;
        animation:dashboardSpin .8s linear infinite;
        position:absolute;
        top:50%;left:50%;
        margin:-14px 0 0 -14px;
    }
    @keyframes dashboardSpin { to { transform:rotate(360deg); } }

    .dashboard-table-card .table { margin-bottom:0; }
    .dashboard-table-card .table thead th {
        background:#f6f8fb;border:0;color:#7d8597;font-size:.7rem;
        font-weight:800;padding:11px 12px;white-space:nowrap;
    }
    .dashboard-table-card .table tbody tr {
        transition:background .2s ease;
    }
    .dashboard-table-card .table tbody tr:hover { background:#f9fbfe; }
    .dashboard-table-card .table tbody td { border-bottom:1px solid #f0f2f6;vertical-align:middle; }
    .dashboard-table-card .table tbody tr:last-child td { border-bottom:0; }

    .dashboard-table-scroll {
        overflow-x:auto;
        -webkit-overflow-scrolling:touch;
        scrollbar-width:thin;
    }
    .dashboard-table-scroll .table { min-width:620px; }

    .status-badge {
        display:inline-flex;align-items:center;gap:6px;
        border-radius:999px;padding:6px 10px;
        font-size:.7rem;font-weight:800;white-space:nowrap;
    }
    .status-badge::before {
        content:'';
        width:6px;height:6px;border-radius:50%;
        background:currentColor;
    }

    .quick-action-card {
        position:relative;
        min-height:145px;
        border:1px solid #e8edf5;
        border-radius:16px;
        padding:1rem .9rem;
        text-align:center;
        transition:all .28s ease;
        height:100%;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        background:#fff;
        overflow:hidden;
    }
    .quick-action-card::before {
        content:'';
        position:absolute;
        inset:0 auto 0 0;
        width:3px;
        background:var(--primary-gradient);
        transform:scaleY(0);
        transition:transform .25s ease;
    }
    .quick-action-card:hover {
        background:#fbfdff;
        border-color:rgba(54,153,255,.28);
        transform:translateY(-4px);
        box-shadow:0 12px 30px rgba(24,28,50,.08);
    }
    .quick-action-card:hover::before { transform:scaleY(1); }
    .quick-action-card .quick-icon {
        width:45px;height:45px;border-radius:13px;
        display:flex;align-items:center;justify-content:center;
        margin-bottom:9px;
        background:rgba(54,153,255,.09);
        transition:transform .25s ease;
    }
    .quick-action-card .quick-icon i { font-size:21px;margin:0; }
    .quick-action-card:hover .quick-icon { transform:scale(1.07); }
    .quick-action-card .quick-title { color:#30374a;font-weight:800;font-size:.82rem; }
    .quick-action-card .quick-description { color:#8b93a7;font-size:.69rem;margin-top:4px;line-height:1.45; }
    .quick-action-card .quick-arrow {
        position:absolute;left:12px;bottom:10px;color:#a7afbd;font-size:12px;
        transition:transform .25s ease,color .25s ease;
    }
    .quick-action-card:hover .quick-arrow { transform:translateX(-3px);color:#3699FF; }

    .dashboard-progress { height:5px !important;border-radius:999px;background:#edf1f6;overflow:hidden; }
    .dashboard-progress .progress-bar { border-radius:999px;background:var(--primary-gradient); }

    .dashboard-fade-in { animation:dashboardFadeIn .5s ease both; }
    @keyframes dashboardFadeIn { from { opacity:0;transform:translateY(8px); } to { opacity:1;transform:translateY(0); } }

    .dashboard-theme-toggle {
        border:1px solid rgba(255,255,255,.20);color:#fff;background:rgba(255,255,255,.12);
        width:40px;height:40px;border-radius:12px;
        display:inline-flex;align-items:center;justify-content:center;
        transition:all .2s ease;cursor:pointer;
    }
    .dashboard-theme-toggle:hover { background:rgba(255,255,255,.20);transform:translateY(-1px); }

    .dashboard-footer {
        margin-top:18px;
        padding:13px 17px;
        border:1px solid var(--card-border);
        border-radius:14px;
        background:rgba(255,255,255,.72);
        color:#8b93a7;
        font-size:.72rem;
        display:flex;align-items:center;justify-content:space-between;gap:10px;
    }

    .empty-state {
        padding:32px 20px;
        text-align:center;
        color:#8b93a7;
    }
    .empty-state .empty-icon {
        width:54px;height:54px;border-radius:16px;
        margin:0 auto 10px;
        display:flex;align-items:center;justify-content:center;
        background:#f3f6fa;color:#9aa3b4;font-size:22px;
    }
    .empty-state strong { display:block;color:#5e6678;font-size:.9rem;margin-bottom:4px; }
    .empty-state span { font-size:.75rem; }

    /* Dark mode — scoped to dashboard only */
    body.dashboard-dark .dashboard-page {
        --dashboard-bg:#0f1322;
        --card-bg:rgba(24,28,50,.96);
        --card-border:rgba(255,255,255,.07);
        --surface-soft:#20253e;
        --line:rgba(255,255,255,.07);
        background:
            radial-gradient(circle at 8% 8%,rgba(54,153,255,.09),transparent 22%),
            radial-gradient(circle at 94% 18%,rgba(27,197,189,.07),transparent 22%),
            #0f1322;
    }
    body.dashboard-dark .dashboard-section-title h3,
    body.dashboard-dark .stat-card h2,
    body.dashboard-dark .attention-value,
    body.dashboard-dark .quick-action-card .quick-title { color:#fff; }
    body.dashboard-dark .dashboard-section-title p,
    body.dashboard-dark .stat-card .kpi-description,
    body.dashboard-dark .quick-action-card .quick-description { color:#aeb6c8; }
    body.dashboard-dark .card-custom,
    body.dashboard-dark .attention-card,
    body.dashboard-dark .quick-action-card { background:#181c32;color:#e9edf5;border-color:rgba(255,255,255,.06) !important; }
    body.dashboard-dark .dashboard-chart-card .chart-shell { background:linear-gradient(180deg,rgba(32,37,62,.70),rgba(24,28,50,.35)); }
    body.dashboard-dark .dashboard-table-card .table thead th { background:#20253e;color:#aeb6c8; }
    body.dashboard-dark .dashboard-table-card .table tbody tr:hover,
    body.dashboard-dark .attention-item:hover { background:#20253e; }
    body.dashboard-dark .dashboard-table-card .table tbody td,
    body.dashboard-dark .attention-item { border-color:rgba(255,255,255,.06); }
    body.dashboard-dark .empty-state .empty-icon { background:#20253e;color:#aeb6c8; }
    body.dashboard-dark .empty-state strong { color:#e9edf5; }
    body.dashboard-dark .dashboard-footer { background:rgba(24,28,50,.75); }
    body.dashboard-dark .dashboard-table-card .text-dark,
    body.dashboard-dark .dashboard-table-card .text-dark-75 { color:#e9edf5 !important; }
    body.dashboard-dark .dashboard-table-card .text-muted,
    body.dashboard-dark .attention-label { color:#aeb6c8 !important; }

    @media (max-width:1199.98px) {
        .municipal-dashboard-header { padding:23px; }
        .stat-card .card-body { padding:22px !important; }
    }
    @media (max-width:991.98px) {
        .municipal-dashboard-header .welcome-title { font-size:1.3rem; }
        .chart-shell { min-height:290px; }
        .chart-container,#dept_chart { min-height:290px; }
    }
    @media (max-width:767.98px) {
        .dashboard-page { overflow-x:hidden; }
        .municipal-dashboard-header { border-radius:18px;padding:19px; }
        .municipal-dashboard-header .brand-icon { width:52px;height:52px;border-radius:14px; }
        .municipal-dashboard-header .brand-icon i { font-size:22px; }
        .municipal-dashboard-header .date-pill { width:auto;justify-content:center;margin-top:8px; }
        .dashboard-section-title { align-items:flex-start; }
        .dashboard-section-title h3 { font-size:1rem; }
        .dashboard-table-scroll .table { min-width:590px; }
        .attention-item { min-height:70px;padding:12px 14px; }
        .dashboard-footer { flex-direction:column;align-items:flex-start; }
    }
    @media (max-width:575.98px) {
        .municipal-dashboard-header { padding:17px; }
        .municipal-dashboard-header .date-pill { width:100%; }
        .stat-card .card-body { padding:19px !important; }
        .stat-card .icon-box { width:50px;height:50px;flex-basis:50px; }
        .chart-shell { min-height:260px; }
        .chart-container,#dept_chart { min-height:260px; }
        .quick-action-card { min-height:132px; }
    }
    @media (max-width:359.98px) {
        .quick-action-card { padding:.8rem .55rem; }
        .quick-action-card .quick-title { font-size:.76rem; }
        .quick-action-card .quick-description { font-size:.64rem; }
    }
    @media (prefers-reduced-motion:reduce) {
        *,*::before,*::after {
            animation-duration:.01ms !important;
            animation-iteration-count:1 !important;
            transition-duration:.01ms !important;
            scroll-behavior:auto !important;
        }
    }
</style>
@endsection


@section('content')
    <svg class="dashboard-svg-sprite" aria-hidden="true" focusable="false">
        <symbol id="icon-building" viewBox="0 0 24 24"><path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M2 21h20M8 7h4M8 11h4M8 15h4M18 21v-7a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v7"/></symbol>
        <symbol id="icon-user" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></symbol>
        <symbol id="icon-calendar" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
        <symbol id="icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
        <symbol id="icon-moon" viewBox="0 0 24 24"><path d="M20 15.5A8.5 8.5 0 0 1 8.5 4 8.5 8.5 0 1 0 20 15.5Z"/></symbol>
        <symbol id="icon-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></symbol>
        <symbol id="icon-dashboard" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></symbol>
        <symbol id="icon-architecture" viewBox="0 0 24 24"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M8 10h2M14 10h2"/></symbol>
        <symbol id="icon-time" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
        <symbol id="icon-tools" viewBox="0 0 24 24"><path d="M14.7 6.3a5 5 0 0 0-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 0 0 6.4-6.4l-3.2 3.2-2.1-2.1 3.2-3.1Z"/></symbol>
        <symbol id="icon-receipt" viewBox="0 0 24 24"><path d="M5 3h14v18l-3-2-4 2-4-2-3 2V3Z"/><path d="M8 8h8M8 12h8M8 16h5"/></symbol>
        <symbol id="icon-money" viewBox="0 0 24 24"><path d="M4 6h16v12H4z"/><circle cx="12" cy="12" r="3"/><path d="M7 9h0M17 15h0"/></symbol>
        <symbol id="icon-alert" viewBox="0 0 24 24"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 9v5M12 17h.01"/></symbol>
        <symbol id="icon-chart" viewBox="0 0 24 24"><path d="M4 19V5M4 19h17"/><path d="m7 15 4-4 3 2 5-6"/></symbol>
        <symbol id="icon-wrench" viewBox="0 0 24 24"><path d="M14.7 6.3a5 5 0 0 0-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 0 0 6.4-6.4l-3.2 3.2-2.1-2.1 3.2-3.1Z"/></symbol>
        <symbol id="icon-signal" viewBox="0 0 24 24"><path d="M4 20h2v-5H4v5ZM11 20h2V9h-2v11ZM18 20h2V4h-2v16Z"/></symbol>
        <symbol id="icon-clipboard" viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1M8 9h8M8 13h8M8 17h5"/></symbol>
        <symbol id="icon-bell" viewBox="0 0 24 24"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></symbol>
        <symbol id="icon-gavel" viewBox="0 0 24 24"><path d="m14 5 5 5M12 7l5 5M4 20l8-8M3 21h7M15 4l2-2 5 5-2 2"/></symbol>
        <symbol id="icon-exclamation" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 17h.01"/></symbol>
        <symbol id="icon-chevron-left" viewBox="0 0 24 24"><path d="m14 6-6 6 6 6"/></symbol>
        <symbol id="icon-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M17 4a4 4 0 0 1 0 8M17 14a6 6 0 0 1 5 7"/></symbol>
        <symbol id="icon-file-plus" viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6zM14 3v5h5M12 12v6M9 15h6"/></symbol>
        <symbol id="icon-document" viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6zM14 3v5h5M9 12h6M9 16h6"/></symbol>
        <symbol id="icon-analytics" viewBox="0 0 24 24"><path d="M4 19V5M4 19h17M8 16v-4M12 16V8M16 16v-6M20 16v-9"/></symbol>
        <symbol id="icon-notification" viewBox="0 0 24 24"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/><path d="M12 3V1"/></symbol>
        <symbol id="icon-list" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></symbol>
        <symbol id="icon-shield" viewBox="0 0 24 24"><path d="M12 3 20 6v5c0 5-3.2 8.2-8 10-4.8-1.8-8-5-8-10V6l8-3Z"/><path d="m9 12 2 2 4-4"/></symbol>
    </svg>
        <div class="dashboard-page">
    <div class="container-fluid">
        <!-- Municipal Banner -->
        <div class="municipal-dashboard-header dashboard-fade-in">
            <div class="header-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center">
                            <div class="brand-icon mr-4">
                                <svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-building"></use></svg>
                            </div>
                            <div>
                                <h2 class="welcome-title">بلدية جباليا النزلة</h2>
                                <div class="welcome-subtitle">نظام الإدارة الإلكترونية — لوحة التحكم المركزية</div>
                                <div class="mt-3 d-flex flex-wrap align-items-center" style="gap:8px;">
                                    <span class="date-pill"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-user"></use></svg> أهلاً بك، {{ auth()->user()->name ?? __('User') }}</span>
                                    <span class="date-pill"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-calendar"></use></svg> {{ now()->translatedFormat('l، d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-right mt-4 mt-lg-0">
                        <div class="d-inline-flex align-items-center" style="gap:8px;">
                            <span class="date-pill"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-clock"></use></svg> {{ now()->format('h:i A') }}</span>
                            <button type="button" class="dashboard-theme-toggle" id="dashboardThemeToggle" aria-label="تبديل الوضع الليلي" title="تبديل الوضع الليلي">
                                <svg class="svg-icon theme-icon" aria-hidden="true" focusable="false"><use href="#icon-moon"></use></svg>
                            </button>
                        </div>
                        <div class="mt-3 small" style="opacity:.78;">{{ __('Municipality_ERP_System') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Stats -->
        <div class="dashboard-section-title dashboard-fade-in">
            <div class="title-wrap">
                <div class="title-icon"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-dashboard"></use></svg></div>
                <div><h3>ملخص لوحة التحكم</h3><p>أهم المؤشرات التشغيلية المتاحة حسب صلاحيات المستخدم</p></div>
            </div>
        </div>
        <div class="row mb-5 dashboard-fade-in">
            @if(($dashboardSections['index_employees'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-primary border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('employees') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0">{{ number_format($stats['employees_count']) }}</h2>
                                <div class="kpi-description">إجمالي الموظفين المسجلين في النظام</div>
                            </div>
                            <div class="icon-box bg-primary-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('employees.index') }}" class="btn btn-block btn-sm btn-light-primary font-weight-bolder py-3 px-6">{{ __('View Details') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(($dashboardSections['index_zk'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-success border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Today Attendance') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-success">{{ number_format($stats['today_attendance']) }}</h2>
                                <div class="kpi-description">عدد حالات الحضور المسجلة اليوم</div>
                            </div>
                            <div class="icon-box bg-success-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('zk') }}" class="btn btn-block btn-sm btn-light-success font-weight-bolder py-3 px-6">{{ __('View Attendance') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(($dashboardSections['index_employee_holiday'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-warning border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Pending Holidays') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-warning">{{ number_format($stats['pending_holidays']) }}</h2>
                                <div class="kpi-description">طلبات الإجازات التي تحتاج إلى مراجعة</div>
                            </div>
                            <div class="icon-box bg-warning-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('EmployeeHolidays.index') }}" class="btn btn-block btn-sm btn-light-warning font-weight-bolder py-3 px-6">{{ __('View Requests') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(($dashboardSections['index_EmployeePermissions'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-info border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Pending Permissions') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-info">{{ number_format($stats['pending_permissions'] ?? 0) }}</h2>
                                <div class="kpi-description">أذونات الموظفين قيد المتابعة</div>
                            </div>
                            <div class="icon-box bg-info-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('EmployeePermissions.index') }}" class="btn btn-block btn-sm btn-light-info font-weight-bolder py-3 px-6">{{ __('View Requests') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Secondary Stats -->
        <div class="row mb-5">
            @if(($dashboardSections['index_jobs'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-danger border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('jobs') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-danger">{{ number_format($stats['jobs_count'] ?? 0) }}</h2>
                                <div class="kpi-description">إجمالي الوظائف والمهام المسجلة</div>
                            </div>
                            <div class="icon-box bg-danger-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('jobs.index') }}" class="btn btn-block btn-sm btn-light-danger font-weight-bolder py-3 px-6">{{ __('View All') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(($dashboardSections['index_vehicle'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-dark border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('vehicles') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-dark">{{ number_format($stats['vehicles_count'] ?? 0) }}</h2>
                                <div class="kpi-description">المركبات المسجلة ضمن النظام</div>
                            </div>
                            <div class="icon-box bg-dark-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-2.7-.6-4.5-1.1c-.8-.2-1.5-1-1.5-1.9v-3c0-.6.4-1 1-1h2"></path>
                                    <circle cx="7" cy="17" r="2"></circle>
                                    <path d="M9 17h6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('vehicle.index') }}" class="btn btn-block btn-sm btn-light-dark font-weight-bolder py-3 px-6">{{ __('View Details') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(($dashboardSections['index_departments'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-primary border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('departments') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-primary">{{ number_format($stats['departments_count'] ?? 0) }}</h2>
                                <div class="kpi-description">الأقسام الإدارية المسجلة</div>
                            </div>
                            <div class="icon-box bg-primary-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <path d="M3 21h18"></path>
                                    <path d="M5 21V7l8-4 8 4v14"></path>
                                    <path d="M17 21v-8.5a2.5 2.5 0 0 0-5 0V21"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('departments.index') }}" class="btn btn-block btn-sm btn-light-primary font-weight-bolder py-3 px-6">{{ __('View All') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(($dashboardSections['index_sections'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-success border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('sections') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-success">{{ number_format($stats['sections_count'] ?? 0) }}</h2>
                                <div class="kpi-description">الشعب والوحدات المرتبطة بالأقسام</div>
                            </div>
                            <div class="icon-box bg-success-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('sections.index') }}" class="btn btn-block btn-sm btn-light-success font-weight-bolder py-3 px-6">{{ __('View All') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Engineering Stats -->
        @if(!empty($dashboardSections['index_engineerings'] ?? null))
        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-info border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Engineering Files') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-info">{{ number_format($stats['engineering_count'] ?? 0) }}</h2>
                                <div class="kpi-description">ملفات هندسية متاحة حسب صلاحياتك</div>
                            </div>
                            <div class="icon-box bg-info-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-architecture"></use></svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('engineering.index') }}" class="btn btn-block btn-sm btn-light-info font-weight-bolder py-3 px-6">{{ __('View Files') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-primary border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Engineering Payments') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-primary">{{ number_format($stats['engineering_payments_count'] ?? 0) }}</h2>
                                <div class="kpi-description">عمليات الدفع المرتبطة بالملفات الهندسية</div>
                            </div>
                            <div class="icon-box bg-primary-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <rect x="2" y="2" width="20" height="20" rx="2" ry="2"></rect>
                                    <line x1="12" y1="6" x2="12" y2="18"></line>
                                    <line x1="6" y1="12" x2="18" y2="12"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('engineering.index') }}" class="btn btn-block btn-sm btn-light-primary font-weight-bolder py-3 px-6">{{ __('View Files') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-success border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Total Revenue') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-success">{{ number_format($stats['engineering_revenue'] ?? 0) }} {{ __('NIS') }}</h2>
                                <div class="kpi-description">الإيرادات الهندسية المسجلة من بيانات النظام</div>
                            </div>
                            <div class="icon-box bg-success-grad">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    <line x1="12" y1="20" x2="12" y2="10"></line>
                                    <line x1="18" y1="20" x2="18" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="16"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            @if(($dashboardSections['export_engineerings'] ?? null))
                            <a href="{{ route('engineering.export') }}" class="btn btn-block btn-sm btn-light-success font-weight-bolder py-3 px-6">{{ __('Export Reports') }}</a>
                            @else
                            <a href="{{ route('engineering.index') }}" class="btn btn-block btn-sm btn-light-success font-weight-bolder py-3 px-6">{{ __('View Files') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-warning border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Pending Payments') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-warning">{{ number_format($stats['pending_payments'] ?? 0) }}</h2>
                                <div class="kpi-description">دفعات لم تكتمل معالجتها بعد</div>
                            </div>
                            <div class="icon-box bg-warning-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-time"></use></svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('engineering.index', ['status' => 'pending']) }}" class="btn btn-block btn-sm btn-light-warning font-weight-bolder py-3 px-6">{{ __('View Pending') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Craft Licenses Stats -->
        @if(!empty($dashboardSections['index_craft_licenses'] ?? null))
        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-dark border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Craft Licenses') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-dark">{{ number_format($stats['craft_licenses_count'] ?? 0) }}</h2>
                                <div class="kpi-description">إجمالي رخص الحرف والصناعات</div>
                            </div>
                            <div class="icon-box bg-dark-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-tools"></use></svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('craft_licenses.index') }}" class="btn btn-block btn-sm btn-light-dark font-weight-bolder py-3 px-6">{{ __('View Licenses') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-info border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Total Payments') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-info">{{ number_format($stats['craft_payments_count'] ?? 0) }}</h2>
                                <div class="kpi-description">عمليات الدفع المسجلة للرخص</div>
                            </div>
                            <div class="icon-box bg-info-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-receipt"></use></svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('craft_licenses.index') }}" class="btn btn-block btn-sm btn-light-info font-weight-bolder py-3 px-6">{{ __('View All') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-success border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Craft Revenue') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-success">{{ number_format($stats['craft_revenue'] ?? 0) }} {{ __('NIS') }}</h2>
                                <div class="kpi-description">الإيرادات المرتبطة برخص الحرف</div>
                            </div>
                            <div class="icon-box bg-success-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-money"></use></svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            @if(($dashboardSections['export_craft_licenses'] ?? null))
                            <a href="{{ route('craft_licenses.export') }}" class="btn btn-block btn-sm btn-light-success font-weight-bolder py-3 px-6">{{ __('Export Reports') }}</a>
                            @else
                            <a href="{{ route('craft_licenses.index') }}" class="btn btn-block btn-sm btn-light-success font-weight-bolder py-3 px-6">{{ __('View All') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-danger border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Unpaid Licenses') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-danger">{{ number_format($stats['unpaid_licenses'] ?? 0) }}</h2>
                                <div class="kpi-description">رخص تحتاج إلى متابعة السداد</div>
                            </div>
                            <div class="icon-box bg-danger-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-alert"></use></svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('craft_licenses.index', ['status' => 'unpaid']) }}" class="btn btn-block btn-sm btn-light-danger font-weight-bolder py-3 px-6">{{ __('View Unpaid') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(($dashboardSections['index_engineerings'] ?? null) || ($dashboardSections['index_craft_licenses'] ?? null))
        @php
            $engineeringRevenue = ($dashboardSections['index_engineerings'] ?? null) ? (float) ($stats['engineering_revenue'] ?? 0) : 0;
            $craftRevenue = ($dashboardSections['index_craft_licenses'] ?? null) ? (float) ($stats['craft_revenue'] ?? 0) : 0;
            $visibleRevenueTotal = $engineeringRevenue + $craftRevenue;
        @endphp
        <div class="dashboard-section-title dashboard-fade-in mt-2">
            <div class="title-wrap">
                <div class="title-icon" style="background:rgba(27,197,189,.10);color:#1BC5BD;"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chart"></use></svg></div>
                <div>
                    <h3>ملخص الإيرادات</h3>
                    <p>القيم المعروضة مأخوذة مباشرة من مؤشرات النظام المتاحة للمستخدم</p>
                </div>
            </div>
        </div>
        <div class="row mb-5 dashboard-fade-in">
            @if(($dashboardSections['index_engineerings'] ?? null))
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card card-custom stat-card h-100">
                    <div class="card-body p-6">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">إيرادات الهندسة</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-info">{{ number_format($engineeringRevenue, 0) }} {{ __('NIS') }}</h2>
                                <div class="kpi-description">الإيراد الهندسي المتاح ضمن صلاحيات المستخدم</div>
                            </div>
                            <div class="icon-box bg-info-grad"><svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-building"></use></svg></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if(($dashboardSections['index_craft_licenses'] ?? null))
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card card-custom stat-card h-100">
                    <div class="card-body p-6">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">إيرادات الحرف والصناعات</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-success">{{ number_format($craftRevenue, 0) }} {{ __('NIS') }}</h2>
                                <div class="kpi-description">الإيراد المرتبط برخص الحرف المتاح للمستخدم</div>
                            </div>
                            <div class="icon-box bg-success-grad"><svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-wrench"></use></svg></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card card-custom stat-card h-100">
                    <div class="card-body p-6">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">إجمالي الإيرادات الظاهرة</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-primary">{{ number_format($visibleRevenueTotal, 0) }} {{ __('NIS') }}</h2>
                                <div class="kpi-description">مجموع المؤشرات المالية التي يملك المستخدم صلاحية رؤيتها</div>
                            </div>
                            <div class="icon-box bg-primary-grad"><svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-signal"></use></svg></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- الطلبات والحجوزات والإخطارات وأوامر الحبس -->
        <div class="row mb-5">
            @if(($dashboardSections['index_requests'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-primary border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Requests') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-primary">{{ number_format($stats['requests_count'] ?? 0) }}</h2>
                                <div class="kpi-description">إجمالي الطلبات مع إظهار المعلقة أدناه</div>
                            </div>
                            <div class="icon-box bg-primary-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-clipboard"></use></svg>
                            </div>
                        </div>
                        <div class="mt-3 text-muted font-size-sm">{{ __('Pending') }}: {{ number_format($stats['pending_requests_count'] ?? 0) }}</div>
                        <div class="mt-6">
                            <a href="{{ route('requests.index') }}" class="btn btn-block btn-sm btn-light-primary font-weight-bolder py-3 px-6">{{ __('View All') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if(($dashboardSections['index_reservations'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-info border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Reservations') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-info">{{ number_format($stats['reservations_count'] ?? 0) }}</h2>
                                <div class="kpi-description">إجمالي الحجوزات المسجلة</div>
                            </div>
                            <div class="icon-box bg-info-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-calendar"></use></svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('reservations.index') }}" class="btn btn-block btn-sm btn-light-info font-weight-bolder py-3 px-6">{{ __('View All') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if(($dashboardSections['index_notices'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-warning border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Notices') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-warning">{{ number_format($stats['notices_count'] ?? 0) }}</h2>
                                <div class="kpi-description">الإخطارات المسجلة في النظام</div>
                            </div>
                            <div class="icon-box bg-warning-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-bell"></use></svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('notices.index') }}" class="btn btn-block btn-sm btn-light-warning font-weight-bolder py-3 px-6">{{ __('View All') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if(($dashboardSections['index_imprisonment_orders'] ?? null))
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card card-custom stat-card shadow-sm h-100 border-top border-danger border-4">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-weight-bold font-size-sm">{{ __('Imprisonment Orders') }}</span>
                                <h2 class="font-weight-bolder mt-2 mb-0 text-danger">{{ number_format($stats['imprisonment_orders_count'] ?? 0) }}</h2>
                                <div class="kpi-description">أوامر الحبس المسجلة والمتاحة</div>
                            </div>
                            <div class="icon-box bg-danger-grad">
                                <svg class="svg-icon icon-lg" aria-hidden="true" focusable="false"><use href="#icon-gavel"></use></svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('imprisonment_orders.index') }}" class="btn btn-block btn-sm btn-light-danger font-weight-bolder py-3 px-6">{{ __('View All') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Requires Attention -->
        <div class="row mb-5 dashboard-fade-in">
            <div class="col-12">
                <div class="attention-card">
                    <div class="attention-head d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="attention-icon mr-3" style="background:rgba(255,168,0,.13);color:#FFA800;"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-exclamation"></use></svg></div>
                            <div><h3 class="font-weight-bolder mb-1" style="font-size:1rem;color:#181C32;">يحتاج إلى متابعة</h3><div class="text-muted font-size-sm">العناصر التي تتطلب إجراءً أو مراجعة</div></div>
                        </div>
                    </div>
                    <div class="row no-gutters">
                        @if(($dashboardSections['index_requests'] ?? null))
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('requests.index') }}" class="attention-item">
                                <div class="d-flex align-items-center">
                                    <div class="attention-icon mr-3" style="background:rgba(54,153,255,.10);color:#3699FF;"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-clipboard"></use></svg></div>
                                    <div><div class="attention-label">الطلبات المعلقة</div><div class="attention-value">{{ number_format($stats['pending_requests_count'] ?? 0) }}</div></div>
                                </div><svg class="svg-icon attention-arrow" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg>
                            </a>
                        </div>
                        @endif
                        @if(($dashboardSections['index_engineerings'] ?? null))
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('engineering.index', ['status' => 'pending']) }}" class="attention-item">
                                <div class="d-flex align-items-center">
                                    <div class="attention-icon mr-3" style="background:rgba(255,168,0,.10);color:#FFA800;"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-time"></use></svg></div>
                                    <div><div class="attention-label">الدفعات المعلقة</div><div class="attention-value">{{ number_format($stats['pending_payments'] ?? 0) }}</div></div>
                                </div><svg class="svg-icon attention-arrow" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg>
                            </a>
                        </div>
                        @endif
                        @if(($dashboardSections['index_employee_holiday'] ?? null))
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('EmployeeHolidays.index') }}" class="attention-item">
                                <div class="d-flex align-items-center">
                                    <div class="attention-icon mr-3" style="background:rgba(27,197,189,.10);color:#1BC5BD;"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-calendar"></use></svg></div>
                                    <div><div class="attention-label">طلبات الإجازات</div><div class="attention-value">{{ number_format($stats['pending_holidays'] ?? 0) }}</div></div>
                                </div><svg class="svg-icon attention-arrow" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg>
                            </a>
                        </div>
                        @endif
                        @if(($dashboardSections['index_EmployeePermissions'] ?? null))
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('EmployeePermissions.index') }}" class="attention-item">
                                <div class="d-flex align-items-center">
                                    <div class="attention-icon mr-3" style="background:rgba(137,80,252,.10);color:#8950FC;"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-clock"></use></svg></div>
                                    <div><div class="attention-label">أذونات الموظفين المعلقة</div><div class="attention-value">{{ number_format($stats['pending_permissions'] ?? 0) }}</div></div>
                                </div><svg class="svg-icon attention-arrow" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg>
                            </a>
                        </div>
                        @endif

                        @if(($dashboardSections['index_craft_licenses'] ?? null))
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('craft_licenses.index', ['status' => 'unpaid']) }}" class="attention-item">
                                <div class="d-flex align-items-center">
                                    <div class="attention-icon mr-3" style="background:rgba(246,78,96,.10);color:#F64E60;"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-alert"></use></svg></div>
                                    <div><div class="attention-label">الرخص غير المدفوعة</div><div class="attention-value">{{ number_format($stats['unpaid_licenses'] ?? 0) }}</div></div>
                                </div><svg class="svg-icon attention-arrow" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Charts Section -->
            <div class="col-lg-8">
                @if(($dashboardSections['index_zk'] ?? null))
                <div class="card card-custom shadow-sm gutter-b dashboard-chart-card">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Attendance Analytics') }}</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">{{ __('Last 7 days performance') }}</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-shell chart-loading" data-chart-shell="attendance">
                            <div id="attendance_chart" class="chart-container" aria-label="تحليلات الحضور لآخر 7 أيام"></div>
                        </div>
                    </div>
                </div>
                @endif

                @if(($dashboardSections['index_engineerings'] ?? null))
                <!-- Latest Engineering Files -->
                <div class="card card-custom shadow-sm gutter-b mt-5 dashboard-table-card">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Latest Engineering Files') }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('engineering.index') }}" class="btn btn-light-info btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive dashboard-table-scroll">
                            <table class="table table-head-custom table-vertical-center table-borderless">
                                <thead>
                                    <tr class="text-left text-muted font-weight-bolder">
                                        <th class="pl-0" style="min-width: 50px">{{ __('File Number') }}</th>
                                        <th style="min-width: 150px">{{ __('Owner Name') }}</th>
                                        <th style="min-width: 100px" class="text-center">{{ __('Status') }}</th>
                                        <th style="min-width: 100px" class="text-right">{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($latest_engineering_files) && $latest_engineering_files->count() > 0)
                                        @foreach($latest_engineering_files as $file)
                                        <tr>
                                            <td class="pl-0 py-5">
                                                <span class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">{{ $file->file_number ?? '---' }}</span>
                                            </td>
                                            <td class="pl-0">
                                                @php
                                                    $fullName = trim(implode(' ', array_filter([
                                                        $file->first_name ?? null,
                                                        $file->father_name ?? null,
                                                        $file->grandfather_name ?? null,
                                                        $file->family_name ?? null,
                                                    ])));
                                                    $ownerName = $file->owner_name ?? $file->owner_full_name ?? $fullName;
                                                @endphp
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $ownerName !== '' ? $ownerName : '---' }}</span>
                                                <span class="text-muted font-weight-bold">{{ $file->location ?? $file->neighborhood ?? '---' }}</span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $isPaid = isset($file->remaining_amount) && (float) $file->remaining_amount <= 0;
                                                    $isPartial = isset($file->paid_amount) && (float) $file->paid_amount > 0 && ! $isPaid;
                                                    $statusColor = $isPaid ? 'success' : ($isPartial ? 'warning' : 'danger');
                                                    $statusText = $isPaid ? __('Paid') : ($isPartial ? __('Partial') : __('Pending'));
                                                @endphp
                                                <span class="label label-lg label-light-{{ $statusColor }} label-inline font-weight-bold">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="text-right pr-0">
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $file->created_at->diffForHumans() }}</span>
                                                <span class="text-muted font-weight-bold">{{ $file->created_at->format('Y/m/d') }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center py-10">
                                                <span class="text-muted">{{ __('No engineering files found') }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if(($dashboardSections['index_requests'] ?? null))
                <!-- Latest Requests -->
                <div class="card card-custom shadow-sm gutter-b mt-5 dashboard-table-card">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Latest Requests') }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('requests.index') }}" class="btn btn-light-primary btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive dashboard-table-scroll">
                            <table class="table table-head-custom table-vertical-center table-borderless">
                                <thead>
                                    <tr class="text-left text-muted font-weight-bolder">
                                        <th class="pl-0" style="min-width: 80px">{{ __('Request') }}</th>
                                        <th style="min-width: 120px">{{ __('Type') }}</th>
                                        <th style="min-width: 100px" class="text-center">{{ __('Status') }}</th>
                                        <th style="min-width: 100px" class="text-right">{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($latest_requests) && $latest_requests->count() > 0)
                                        @foreach($latest_requests as $req)
                                        <tr>
                                            <td class="pl-0 py-5">
                                                <a href="{{ route('requests.show', $req->id) }}" class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">{{ \Illuminate\Support\Str::limit($req->text ?? '---', 40) }}</a>
                                                <span class="text-muted font-weight-bold d-block">{{ $req->creator->name ?? '---' }}</span>
                                            </td>
                                            <td>
                                                <span class="label label-lg label-light-info label-inline font-weight-bold">{{ $req->requestType->name ?? '---' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-lg label-light-{{ $req->status->name == 'قيد الانتظار' ? 'warning' : 'success' }} label-inline font-weight-bold">
                                                    {{ $req->status->name ?? '---' }}
                                                </span>
                                            </td>
                                            <td class="text-right pr-0">
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $req->created_at->diffForHumans() }}</span>
                                                <span class="text-muted font-weight-bold">{{ $req->created_at->format('Y/m/d') }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center py-10">
                                                <span class="text-muted">{{ __('No requests found') }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if(($dashboardSections['index_craft_licenses'] ?? null))
                <!-- Latest Craft Licenses -->
                <div class="card card-custom shadow-sm gutter-b mt-5 dashboard-table-card">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Latest Craft Licenses') }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('craft_licenses.index') }}" class="btn btn-light-dark btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive dashboard-table-scroll">
                            <table class="table table-head-custom table-vertical-center table-borderless">
                                <thead>
                                    <tr class="text-left text-muted font-weight-bolder">
                                        <th class="pl-0" style="min-width: 50px">{{ __('File Number') }}</th>
                                        <th style="min-width: 150px">{{ __('Citizen Name') }}</th>
                                        <th style="min-width: 100px" class="text-center">{{ __('Status') }}</th>
                                        <th style="min-width: 100px" class="text-right">{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($latest_craft_licenses) && $latest_craft_licenses->count() > 0)
                                        @foreach($latest_craft_licenses as $license)
                                        <tr>
                                            <td class="pl-0 py-5">
                                                <span class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">{{ $license->file_number ?? '---' }}</span>
                                            </td>
                                            <td class="pl-0">
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $license->owner_name ?? $license->property_owner ?? $license->commercial_name ?? '---' }}</span>
                                                <span class="text-muted font-weight-bold">{{ $license->property_owner ?? '---' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-lg label-light-{{ $license->status == 'active' ? 'success' : ($license->status == 'pending' ? 'warning' : 'danger') }} label-inline font-weight-bold">
                                                    {{ __('craft.status_' . $license->status) }}
                                                </span>
                                            </td>
                                            <td class="text-right pr-0">
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $license->created_at->diffForHumans() }}</span>
                                                <span class="text-muted font-weight-bold">{{ $license->created_at->format('Y/m/d') }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center py-10">
                                                <span class="text-muted">{{ __('No craft licenses found') }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if(($dashboardSections['index_employee_holiday'] ?? null))
                <!-- Latest Holidays -->
                <div class="card card-custom shadow-sm gutter-b mt-5 dashboard-table-card">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Latest Holiday Requests') }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('EmployeeHolidays.index') }}" class="btn btn-light-warning btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive dashboard-table-scroll">
                            <table class="table table-head-custom table-vertical-center table-borderless">
                                <thead>
                                    <tr class="text-left text-muted font-weight-bolder">
                                        <th class="pl-0" style="min-width: 50px">{{ __('employee') }}</th>
                                        <th style="min-width: 150px">{{ __('holiday_id') }}</th>
                                        <th style="min-width: 100px" class="text-center">{{ __('status') }}</th>
                                        <th style="min-width: 100px" class="text-right">{{ __('date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($latest_holidays) && $latest_holidays->count() > 0)
                                        @foreach($latest_holidays as $holiday)
                                        <tr>
                                            <td class="pl-0 py-5">
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-40 symbol-light-success mr-4">
                                                        <span class="symbol-label">
                                                            <span class="font-size-h5 font-weight-boldest text-success">{{ mb_substr($holiday->employee->name ?? '?', 0, 1) }}</span>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('employees.show', $holiday->employee->id ?? 0) }}" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $holiday->employee->name ?? '---' }}</a>
                                                        <span class="text-muted font-weight-bold d-block">{{ $holiday->employee->czn_id ?? '---' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $holiday->holiday->name ?? '---' }}</span>
                                                <span class="text-muted font-weight-bold">{{ $holiday->vacation_days }} {{ __('days') }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-lg label-light-{{ $holiday->status == 'accepted' ? 'success' : ($holiday->status == 'pending' ? 'warning' : 'danger') }} label-inline font-weight-bold">
                                                    {{ __($holiday->status) }}
                                                </span>
                                            </td>
                                            <td class="text-right pr-0">
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $holiday->created_at->diffForHumans() }}</span>
                                                <span class="text-muted font-weight-bold">{{ $holiday->created_at->format('Y/m/d') }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center py-10">
                                                <span class="text-muted">{{ __('No holiday requests found') }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if(($dashboardSections['index_employees'] ?? null))
                <!-- Latest Activities -->
                <div class="card card-custom shadow-sm gutter-b mt-5 dashboard-table-card">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Latest Employee Registrations') }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('employees.index') }}" class="btn btn-light-primary btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive dashboard-table-scroll">
                            <table class="table table-head-custom table-vertical-center table-borderless">
                                <thead>
                                    <tr class="text-left text-muted font-weight-bolder">
                                        <th class="pl-0" style="min-width: 50px">{{ __('employee') }}</th>
                                        <th style="min-width: 150px"></th>
                                        <th style="min-width: 100px" class="text-center">{{ __('job') }}</th>
                                        <th style="min-width: 100px" class="text-right">{{ __('date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latest_employees as $emp)
                                    <tr>
                                        <td class="pl-0 py-5">
                                            <div class="symbol symbol-50 symbol-light-primary">
                                                <span class="symbol-label">
                                                    <span class="font-size-h5 font-weight-boldest text-primary">{{ mb_substr($emp->name, 0, 1) }}</span>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="pl-0">
                                            <a href="{{ route('employees.show', $emp->id) }}" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $emp->name }}</a>
                                            <span class="text-muted font-weight-bold d-block">ID: {{ $emp->czn_id }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="label label-lg label-light-info label-inline font-weight-bold">{{ $emp->job->name ?? '---' }}</span>
                                        </td>
                                        <td class="text-right pr-0">
                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $emp->created_at->diffForHumans() }}</span>
                                            <span class="text-muted font-weight-bold">{{ $emp->created_at->format('Y/m/d') }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Side Panels -->
            <div class="col-lg-4">
                @if(($dashboardSections['index_employees'] ?? null))
                <!-- Department Distribution -->
                <div class="card card-custom shadow-sm gutter-b dashboard-table-card">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Employees by Dept.') }}</span>
                            <span class="text-muted mt-2 font-weight-bold font-size-sm">{{ __('Total: ') }} {{ number_format($dept_distribution->sum('employees_count')) }}</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-shell chart-loading" data-chart-shell="departments"><div id="dept_chart" aria-label="توزيع الموظفين حسب الأقسام"></div></div>
                        
                        <!-- Department Statistics Table -->
                        <div class="mt-5">
                            <div class="table-responsive dashboard-table-scroll">
                                <table class="table table-sm table-borderless">
                                    <thead>
                                        <tr class="text-left text-muted font-weight-bolder font-size-xs">
                                            <th>{{ __('Department') }}</th>
                                            <th class="text-right">{{ __('Count') }}</th>
                                            <th class="text-right">{{ __('%') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalEmployees = $dept_distribution->sum('employees_count');
                                        @endphp
                                        @foreach($dept_distribution->sortByDesc('employees_count')->take(6) as $dept)
                                            @php
                                                $percentage = $totalEmployees > 0 ? round(($dept->employees_count / $totalEmployees) * 100, 1) : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-30 symbol-light mr-2">
                                                            <span class="symbol-label font-size-xs font-weight-boldest text-primary">
                                                                {{ mb_substr($dept->name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <span class="text-dark-75 font-weight-bolder font-size-sm">{{ $dept->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-right">
                                                    <span class="text-dark font-weight-bolder">{{ number_format($dept->employees_count) }}</span>
                                                </td>
                                                <td class="text-right">
                                                    <span class="font-weight-bolder {{ $percentage > 15 ? 'text-primary' : 'text-muted' }}">{{ $percentage }}%</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="py-1">
                                                    <div class="progress" style="height: 4px;">
                                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($dept_distribution->count() > 6)
                                <div class="text-center mt-3">
                                    <a href="{{ route('departments.index') }}" class="btn btn-sm btn-light-primary font-weight-bolder">
                                        {{ __('View All Departments') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Actions Grid -->
                <div class="card card-custom shadow-sm gutter-b mt-5 dashboard-table-card">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Quick Actions') }}</span>
                            <span class="text-muted mt-2 font-weight-bold font-size-sm">اختصارات سريعة للعمليات المتاحة حسب صلاحياتك</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="row">
                            @if(($dashboardSections['index_employees'] ?? null))
                            <div class="col-6 mb-4">
                                <a href="{{ route('employees.index') }}" class="quick-action-card" aria-label="إدارة الموظفين">
                                    <span class="quick-icon" style="color:#3699FF;background:rgba(54,153,255,.10);"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-users"></use></svg></span>
                                    <span class="quick-title">{{ __('Add Employee') }}</span>
                                    <span class="quick-description">الوصول إلى ملفات الموظفين وإدارتها</span>
                                    <span class="quick-arrow"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg></span>
                                </a>
                            </div>
                            @endif

                            @if(($dashboardSections['store_requests'] ?? null))
                            <div class="col-6 mb-4">
                                <a href="{{ route('requests.create') }}" class="quick-action-card" aria-label="إنشاء طلب جديد">
                                    <span class="quick-icon" style="color:#3699FF;background:rgba(54,153,255,.10);"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-file-plus"></use></svg></span>
                                    <span class="quick-title">{{ __('New Request') }}</span>
                                    <span class="quick-description">إنشاء طلب جديد مباشرة</span>
                                    <span class="quick-arrow"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg></span>
                                </a>
                            </div>
                            @endif

                            @if(($dashboardSections['index_engineerings'] ?? null))
                            <div class="col-6 mb-4">
                                <a href="{{ route('engineering.index') }}" class="quick-action-card" aria-label="الملفات الهندسية">
                                    <span class="quick-icon" style="color:#8950FC;background:rgba(137,80,252,.10);"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-architecture"></use></svg></span>
                                    <span class="quick-title">ملفات هندسية</span>
                                    <span class="quick-description">عرض ومتابعة المعاملات الهندسية</span>
                                    <span class="quick-arrow"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg></span>
                                </a>
                            </div>
                            @endif

                            @if(($dashboardSections['create_craft_licenses'] ?? null))
                            <div class="col-6 mb-4">
                                <a href="{{ route('craft_licenses.create') }}" class="quick-action-card" aria-label="إنشاء رخصة حرفة">
                                    <span class="quick-icon" style="color:#1BC5BD;background:rgba(27,197,189,.10);"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-document"></use></svg></span>
                                    <span class="quick-title">{{ __('New License') }}</span>
                                    <span class="quick-description">إضافة رخصة حرفة أو صناعة</span>
                                    <span class="quick-arrow"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg></span>
                                </a>
                            </div>
                            @endif

                            @if(($dashboardSections['index_employee_reports'] ?? null))
                            <div class="col-6 mb-4">
                                <a href="{{ route('employee_reports') }}" class="quick-action-card" aria-label="التقارير">
                                    <span class="quick-icon" style="color:#FFA800;background:rgba(255,168,0,.11);"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-analytics"></use></svg></span>
                                    <span class="quick-title">{{ __('Reports') }}</span>
                                    <span class="quick-description">الوصول إلى التقارير المتاحة</span>
                                    <span class="quick-arrow"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg></span>
                                </a>
                            </div>
                            @endif

                            @if(($dashboardSections['index_employee_holiday'] ?? null))
                            <div class="col-6 mb-4">
                                <a href="{{ route('EmployeeHolidays.index') }}" class="quick-action-card" aria-label="طلبات الإجازات">
                                    <span class="quick-icon" style="color:#16A085;background:rgba(22,160,133,.10);"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-calendar"></use></svg></span>
                                    <span class="quick-title">{{ __('Holidays') }}</span>
                                    <span class="quick-description">متابعة طلبات الإجازات</span>
                                    <span class="quick-arrow"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg></span>
                                </a>
                            </div>
                            @endif

                            @if(($dashboardSections['index_notices'] ?? null))
                            <div class="col-6 mb-4">
                                <a href="{{ route('notices.index') }}" class="quick-action-card" aria-label="الإخطارات">
                                    <span class="quick-icon" style="color:#FFA800;background:rgba(255,168,0,.11);"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-notification"></use></svg></span>
                                    <span class="quick-title">{{ __('Notices') }}</span>
                                    <span class="quick-description">عرض الإخطارات والتنبيهات</span>
                                    <span class="quick-arrow"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg></span>
                                </a>
                            </div>
                            @endif

                            @if(($dashboardSections['index_imprisonment_orders'] ?? null))
                            <div class="col-6 mb-4">
                                <a href="{{ route('imprisonment_orders.index') }}" class="quick-action-card" aria-label="أوامر الحبس">
                                    <span class="quick-icon" style="color:#F64E60;background:rgba(246,78,96,.10);"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-list"></use></svg></span>
                                    <span class="quick-title">{{ __('Imprisonment Orders') }}</span>
                                    <span class="quick-description">مراجعة أوامر الحبس المسجلة</span>
                                    <span class="quick-arrow"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg></span>
                                </a>
                            </div>
                            @endif

                            @if(($dashboardSections['index_reservations'] ?? null))
                            <div class="col-6 mb-4">
                                <a href="{{ route('reservations.index') }}" class="quick-action-card" aria-label="الحجوزات">
                                    <span class="quick-icon" style="color:#3699FF;background:rgba(54,153,255,.10);"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-calendar"></use></svg></span>
                                    <span class="quick-title">{{ __('Reservations') }}</span>
                                    <span class="quick-description">عرض وإدارة الحجوزات</span>
                                    <span class="quick-arrow"><svg class="svg-icon" aria-hidden="true" focusable="false"><use href="#icon-chevron-left"></use></svg></span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Recent Activity / Notifications intentionally remain data-driven.
                     The current backend view does not provide activity/notification collections,
                     so no synthetic records are rendered here. Add the real collections later
                     without changing the dashboard layout or authorization rules. --}}
            </div>
        </div>

        <div class="dashboard-footer" aria-label="معلومات النظام">
            <span><svg class="svg-icon mr-1" aria-hidden="true" focusable="false"><use href="#icon-shield"></use></svg> الواجهة تعمل وفق صلاحيات المستخدم الحالية</span>
            <span>{{ __('Municipality_ERP_System') }}</span>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';

    var dashboardState = {
        attendanceChart: null,
        departmentChart: null,
        themeKey: 'municipality_dashboard_theme'
    };

    function getDashboardTheme() {
        return document.body.classList.contains('dashboard-dark') ? 'dark' : 'light';
    }

    function showChartState(elementId, type, title, description) {
        var el = document.getElementById(elementId);
        if (!el) return;

        var icon = type === 'error' ? 'alert' : 'chart';
        el.innerHTML =
            '<div class="chart-state" role="status">' +
                '<svg class="svg-icon chart-state-icon" aria-hidden="true" focusable="false"><use href="#icon-' + icon + '"></use></svg>' +
                '<strong>' + title + '</strong>' +
                '<span>' + description + '</span>' +
            '</div>';
    }

    function clearChartState(elementId) {
        var el = document.getElementById(elementId);
        if (el) el.innerHTML = '';
    }

    function finishChartLoading(name) {
        var shell = document.querySelector('[data-chart-shell="' + name + '"]');
        if (shell) shell.classList.remove('chart-loading');
    }

    function destroyDashboardCharts() {
        if (dashboardState.attendanceChart) {
            dashboardState.attendanceChart.destroy();
            dashboardState.attendanceChart = null;
        }
        if (dashboardState.departmentChart) {
            dashboardState.departmentChart.destroy();
            dashboardState.departmentChart = null;
        }
    }

    function initDashboardCharts() {
        var Apex = window.ApexCharts;
        if (!Apex) {
            showChartState('attendance_chart', 'error', 'تعذر تحميل المخطط', 'يمكنك إعادة تحميل الصفحة للمحاولة مرة أخرى.');
            showChartState('dept_chart', 'error', 'تعذر تحميل المخطط', 'يمكنك إعادة تحميل الصفحة للمحاولة مرة أخرى.');
            return;
        }

        destroyDashboardCharts();

        var attendanceData = {!! json_encode($attendance_data ?? []) !!};
        var deptData = {!! json_encode($dept_distribution ?? []) !!};

        if (!Array.isArray(attendanceData)) attendanceData = [];
        if (!Array.isArray(deptData)) deptData = [];

        /* Attendance */
        var attendanceEl = document.getElementById('attendance_chart');
        if (attendanceEl) {
            clearChartState('attendance_chart');

            var attendanceCategories = attendanceData.map(function (item) {
                return item.date || '-';
            });
            var attendanceCounts = attendanceData.map(function (item) {
                return Number(item.count || 0);
            });
            var attendanceLate = attendanceData.map(function (item) {
                return Number(item.total_late || 0);
            });

            if (!attendanceData.length) {
                showChartState(
                    'attendance_chart',
                    'empty',
                    'لا توجد بيانات حضور حالياً',
                    'لا توجد سجلات متاحة للفترة المعروضة.'
                );
                finishChartLoading('attendance');
            } else {
                var attendanceOptions = {
                    chart: {
                        type: 'area',
                        height: 320,
                        fontFamily: 'Cairo, sans-serif',
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        foreColor: getDashboardTheme() === 'dark' ? '#aeb6c8' : '#7d8597'
                    },
                    series: [
                        { name: '{{ __("Attendance") }}', data: attendanceCounts },
                        { name: '{{ __("Total Late Minutes") }}', data: attendanceLate }
                    ],
                    colors: ['#3699FF', '#FFA800'],
                    stroke: {
                        curve: 'smooth',
                        width: [3, 2.5],
                        lineCap: 'round'
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.42,
                            opacityTo: 0.03,
                            stops: [0, 90, 100]
                        }
                    },
                    markers: {
                        size: 0,
                        hover: { size: 5 }
                    },
                    grid: {
                        borderColor: getDashboardTheme() === 'dark' ? '#303650' : '#edf0f5',
                        strokeDashArray: 4,
                        padding: { left: 8, right: 8 }
                    },
                    xaxis: {
                        categories: attendanceCategories,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: {
                                colors: getDashboardTheme() === 'dark' ? '#aeb6c8' : '#7d8597',
                                fontFamily: 'Cairo, sans-serif',
                                fontSize: '11px'
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        labels: {
                            formatter: function (value) { return Math.round(value); },
                            style: {
                                colors: [getDashboardTheme() === 'dark' ? '#aeb6c8' : '#7d8597'],
                                fontFamily: 'Cairo, sans-serif'
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '11px',
                        fontFamily: 'Cairo, sans-serif',
                        markers: { width: 9, height: 9, radius: 9 }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        theme: getDashboardTheme()
                    },
                    noData: {
                        text: 'لا توجد بيانات',
                        align: 'center',
                        verticalAlign: 'middle',
                        style: {
                            color: '#8b93a7',
                            fontSize: '14px',
                            fontFamily: 'Cairo, sans-serif'
                        }
                    },
                    responsive: [{
                        breakpoint: 575,
                        options: {
                            chart: { height: 260 },
                            legend: { position: 'bottom' }
                        }
                    }]
                };

                dashboardState.attendanceChart = new Apex(attendanceEl, attendanceOptions);
                dashboardState.attendanceChart.render().then(function () {
                    finishChartLoading('attendance');
                }).catch(function () {
                    finishChartLoading('attendance');
                    showChartState('attendance_chart', 'error', 'تعذر تحميل البيانات', 'حدث خطأ أثناء عرض المخطط.');
                });
            }
        }

        /* Department distribution — keep the real data, but collapse long tails into "أخرى". */
        var deptEl = document.getElementById('dept_chart');
        if (deptEl) {
            clearChartState('dept_chart');

            var normalizedDepartments = deptData
                .map(function (item) {
                    return {
                        name: item.name || '---',
                        value: Number(item.employees_count || 0)
                    };
                })
                .filter(function (item) { return item.value > 0; })
                .sort(function (a, b) { return b.value - a.value; });

            if (!normalizedDepartments.length) {
                showChartState(
                    'dept_chart',
                    'empty',
                    'لا توجد بيانات أقسام حالياً',
                    'لا توجد سجلات موظفين مرتبطة بالأقسام لعرضها.'
                );
                finishChartLoading('departments');
            } else {
                var topDepartments = normalizedDepartments.slice(0, 6);
                var otherTotal = normalizedDepartments.slice(6).reduce(function (sum, item) {
                    return sum + item.value;
                }, 0);

                if (otherTotal > 0) {
                    topDepartments.push({ name: 'أخرى', value: otherTotal });
                }

                var deptLabels = topDepartments.map(function (item) { return item.name; });
                var deptSeries = topDepartments.map(function (item) { return item.value; });
                var deptTotal = deptSeries.reduce(function (sum, value) { return sum + value; }, 0);

                var deptOptions = {
                    chart: {
                        type: 'donut',
                        height: 320,
                        fontFamily: 'Cairo, sans-serif',
                        toolbar: { show: false },
                        foreColor: getDashboardTheme() === 'dark' ? '#aeb6c8' : '#7d8597'
                    },
                    series: deptSeries,
                    labels: deptLabels,
                    colors: ['#3699FF', '#1BC5BD', '#8950FC', '#FFA800', '#F64E60', '#3F4254', '#7C8AA5'],
                    stroke: {
                        width: 4,
                        colors: [getDashboardTheme() === 'dark' ? '#181C32' : '#ffffff']
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '11px',
                        fontFamily: 'Cairo, sans-serif',
                        formatter: function (seriesName, opts) {
                            var value = opts.w.globals.series[opts.seriesIndex] || 0;
                            var percentage = deptTotal ? ((value / deptTotal) * 100).toFixed(1) : 0;
                            return seriesName + ' — ' + value + ' (' + percentage + '%)';
                        },
                        itemMargin: { horizontal: 8, vertical: 4 }
                    },
                    plotOptions: {
                        pie: {
                            expandOnClick: true,
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '12px',
                                        fontWeight: 700,
                                        offsetY: -2
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '19px',
                                        fontWeight: 800,
                                        offsetY: 5,
                                        formatter: function (value) {
                                            return Number(value).toLocaleString('en-US');
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'إجمالي الموظفين',
                                        color: getDashboardTheme() === 'dark' ? '#aeb6c8' : '#7d8597',
                                        formatter: function () {
                                            return deptTotal.toLocaleString('en-US');
                                        }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    tooltip: {
                        theme: getDashboardTheme(),
                        y: {
                            formatter: function (value) {
                                var percentage = deptTotal ? ((value / deptTotal) * 100).toFixed(1) : 0;
                                return value.toLocaleString('en-US') + ' — ' + percentage + '%';
                            }
                        }
                    },
                    noData: {
                        text: 'لا توجد بيانات',
                        align: 'center',
                        verticalAlign: 'middle',
                        style: {
                            color: '#8b93a7',
                            fontSize: '14px',
                            fontFamily: 'Cairo, sans-serif'
                        }
                    },
                    responsive: [{
                        breakpoint: 575,
                        options: {
                            chart: { height: 270 },
                            legend: { fontSize: '10px' }
                        }
                    }]
                };

                dashboardState.departmentChart = new Apex(deptEl, deptOptions);
                dashboardState.departmentChart.render().then(function () {
                    finishChartLoading('departments');
                }).catch(function () {
                    finishChartLoading('departments');
                    showChartState('dept_chart', 'error', 'تعذر تحميل البيانات', 'حدث خطأ أثناء عرض المخطط.');
                });
            }
        }
    }

    function applyTheme(theme) {
        var isDark = theme === 'dark';
        document.body.classList.toggle('dashboard-dark', isDark);
        document.body.classList.toggle('app-theme-dark', isDark);

        var button = document.getElementById('dashboardThemeToggle');
        if (button) {
            var icon = button.querySelector('.theme-icon use');
            if (icon) icon.setAttribute('href', isDark ? '#icon-sun' : '#icon-moon');
            button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            button.setAttribute('title', isDark ? 'تفعيل الوضع الفاتح' : 'تفعيل الوضع الليلي');
        }

        try {
            localStorage.setItem(dashboardState.themeKey, isDark ? 'dark' : 'light');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        } catch (e) {}

        if (window.ApexCharts) {
            setTimeout(initDashboardCharts, 80);
        }
    }

    function initTheme() {
        var saved = null;
        try { saved = localStorage.getItem(dashboardState.themeKey); } catch (e) {}

        var systemDark = window.matchMedia &&
            window.matchMedia('(prefers-color-scheme: dark)').matches;

        applyTheme(saved || (systemDark ? 'dark' : 'light'));
    }

    function loadScript(src, onLoad, onError) {
        var existing = document.querySelector('script[data-dashboard-apex]');
        if (existing) {
            if (window.ApexCharts) onLoad();
            else existing.addEventListener('load', onLoad, { once: true });
            return;
        }

        var script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.dataset.dashboardApex = 'true';
        script.onload = onLoad;
        script.onerror = onError;
        document.head.appendChild(script);
    }

    function ensureApexCharts() {
        if (window.ApexCharts) {
            initDashboardCharts();
            return;
        }

        loadScript(
            "{{ asset('assets/plugins/global/plugins.bundle.js') }}",
            function () {
                if (window.ApexCharts) {
                    initDashboardCharts();
                } else {
                    loadScript(
                        'https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js',
                        function () {
                            if (window.ApexCharts) initDashboardCharts();
                            else {
                                showChartState('attendance_chart', 'error', 'تعذر تحميل المخطط', 'يمكنك إعادة تحميل الصفحة للمحاولة مرة أخرى.');
                                showChartState('dept_chart', 'error', 'تعذر تحميل المخطط', 'يمكنك إعادة تحميل الصفحة للمحاولة مرة أخرى.');
                            }
                        },
                        function () {
                            showChartState('attendance_chart', 'error', 'تعذر تحميل المخطط', 'يمكنك إعادة تحميل الصفحة للمحاولة مرة أخرى.');
                            showChartState('dept_chart', 'error', 'تعذر تحميل المخطط', 'يمكنك إعادة تحميل الصفحة للمحاولة مرة أخرى.');
                        }
                    );
                }
            },
            function () {
                loadScript(
                    'https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js',
                    function () {
                        if (window.ApexCharts) initDashboardCharts();
                    },
                    function () {
                        showChartState('attendance_chart', 'error', 'تعذر تحميل المخطط', 'يمكنك إعادة تحميل الصفحة للمحاولة مرة أخرى.');
                        showChartState('dept_chart', 'error', 'تعذر تحميل المخطط', 'يمكنك إعادة تحميل الصفحة للمحاولة مرة أخرى.');
                    }
                );
            }
        );
    }

    document.addEventListener('DOMContentLoaded', function () {
        initTheme();

        var toggle = document.getElementById('dashboardThemeToggle');
        if (toggle) {
            toggle.addEventListener('click', function () {
                applyTheme(getDashboardTheme() === 'dark' ? 'light' : 'dark');
            });
        }

        ensureApexCharts();
    });
})();
</script>
@endsection
