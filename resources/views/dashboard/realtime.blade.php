@extends('layout.master')

@section('title')
    {{ __('مركز التحكم التفاعلي') }}
@endsection

@section('page_title')
    {{ __('مركز التحكم التفاعلي') }}
@endsection
@section('sub_main')
    {{ __('لوحة التحكم') }}
@endsection
@section('sub_title')
    {{ __('Real-time Operations Center') }}
@endsection

@push('css')
<style>
.realtime-dashboard { --rt-ink:#17233d; --rt-muted:#718096; --rt-line:#e7edf5; --rt-blue:#3b82f6; }
.realtime-hero { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:22px 26px; margin-bottom:24px; border:1px solid #e7edf5; border-radius:18px; background:linear-gradient(115deg,#fff 15%,#f4f8ff 100%); box-shadow:0 8px 24px rgba(31,55,90,.06); }
.realtime-hero-copy { display:flex; align-items:center; gap:15px; }
.realtime-hero-icon { display:grid; place-items:center; width:52px; height:52px; border-radius:15px; color:#2563eb; background:#eaf2ff; font-size:21px; }
.realtime-hero h1 { color:var(--rt-ink); font-size:21px; font-weight:800; margin:0; }
.realtime-hero p { margin:5px 0 0; color:var(--rt-muted); font-size:12px; }
.realtime-hero-actions { display:flex; align-items:center; gap:10px; }
.realtime-status { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; color:#087c61; background:#e8faf4; font-size:12px; font-weight:700; }
.realtime-status .live-indicator,.live-indicator { display:inline-block; width:8px; height:8px; flex:0 0 8px; background:#10b981; border-radius:50%; box-shadow:0 0 0 4px rgba(16,185,129,.12); animation:rt-pulse 2s infinite; }
@keyframes rt-pulse { 50% { opacity:.55; } }
.stats-card { --rt-accent:#3b82f6; position:relative; height:100%; overflow:hidden; padding:21px; border:1px solid var(--rt-line); border-top:3px solid var(--rt-accent); border-radius:15px; color:var(--rt-ink)!important; background:#fff!important; box-shadow:0 6px 20px rgba(31,55,90,.045); transition:transform .2s ease,box-shadow .2s ease; }
.stats-card:hover { transform:translateY(-3px); box-shadow:0 12px 26px rgba(31,55,90,.09); }
.stats-card-employees { --rt-accent:#3b82f6; }
.stats-card-requests { --rt-accent:#e65c78; }
.stats-card-crafts { --rt-accent:#d99a20; }
.stats-card-engineering { --rt-accent:#7c62d9; }
.stats-card-reservations { --rt-accent:#13a89e; }
.stats-card-notices { --rt-accent:#ee8a37; }
.stats-card-imprisonment { --rt-accent:#d85666; }
.stats-card .font-size-h2 { color:var(--rt-ink); font-size:29px; font-weight:800; letter-spacing:-.5px; }
.stats-card p,.stats-card>div span { color:var(--rt-muted)!important; font-size:12px; }
.stats-card .text-white-75,.stats-card .text-white-50,.stats-card .opacity-75 { color:var(--rt-muted)!important; }
.stats-icon { display:grid; place-items:center; width:43px; height:43px; border-radius:13px; color:var(--rt-accent); background:color-mix(in srgb,var(--rt-accent) 11%,white); }
.stats-icon i { font-size:18px; }
.stats-card .border-top { border-color:var(--rt-line)!important; }
.stats-card .progress { height:5px!important; overflow:hidden; border-radius:10px; background:#eef2f7; }
.stats-card .progress-bar { background:var(--rt-accent)!important; border-radius:10px; }
.realtime-dashboard .card-custom { height:100%; overflow:hidden; border:1px solid var(--rt-line); border-radius:16px; box-shadow:0 6px 22px rgba(31,55,90,.045); }
.realtime-dashboard .card-custom .card-header { min-height:unset; padding:19px 22px!important; border-bottom:1px solid #edf1f6!important; }
.realtime-dashboard .card-custom .card-title { color:var(--rt-ink)!important; font-size:15px; }
.heatmap-container { padding:15px; border:1px solid #edf1f6; border-radius:13px; background:#f8fafd; }
.timeline-item { padding:0 14px 0 0; margin:0 4px 18px 0; border-right:2px solid #e5eaf1; border-left:0; }
.timeline-item.priority-high { border-right-color:#ef4444; }
.alert-badge { animation:rt-pulse 2s infinite; }
.realtime-counter { font-variant-numeric:tabular-nums; }
.chart-container { position:relative; height:300px; }
.quick-action-link { min-height:105px; display:flex; flex-direction:column; align-items:center; justify-content:center; border-radius:13px; font-size:13px; }
.quick-action-link i { margin-bottom:9px!important; font-size:21px!important; }
.realtime-dashboard .card-body { padding:21px; }
.realtime-dashboard #systemAlerts .alert { display:flex; align-items:center; gap:12px; border-radius:12px; box-shadow:0 4px 14px rgba(31,55,90,.05); }
.realtime-dashboard .alert-icon { flex:0 0 auto; }
.realtime-dashboard #liveTimeline { min-height:90px; }
.realtime-dashboard .timeline-item:last-child { margin-bottom:0!important; }
.realtime-dashboard .quick-action-link { box-shadow:0 5px 14px rgba(31,55,90,.08); transition:transform .2s ease,box-shadow .2s ease; }
.realtime-dashboard .quick-action-link:hover { transform:translateY(-2px); box-shadow:0 9px 19px rgba(31,55,90,.13); }
@media(max-width:767px) { .realtime-hero { align-items:flex-start; flex-direction:column; padding:18px; } .realtime-hero-actions { width:100%; justify-content:space-between; } }
</style>
@endpush

@section('content')
<div class="container-fluid realtime-dashboard" dir="rtl">
    <!-- Header -->
    <div class="realtime-hero">
        <div class="realtime-hero-copy">
            <div class="realtime-hero-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <h1>{{ __('مركز التحكم التفاعلي') }}</h1>
                <p>{{ __('تحديثات فورية للإحصائيات والأنشطة') }} · تحديث تلقائي كل 30 ثانية</p>
            </div>
        </div>
        <div class="realtime-hero-actions">
            <span class="realtime-status"><span class="live-indicator"></span>{{ __('متصل') }}</span>
            <button class="btn btn-primary font-weight-bold" onclick="refreshAllData(this)">
                <i class="fas fa-sync-alt ml-2"></i>{{ __('تحديث الآن') }}
            </button>
        </div>
    </div>

    <!-- System Alerts -->
    <div id="systemAlerts" class="mb-8">
        <!-- Alerts will be loaded here -->
    </div>

    <!-- Stats Grid -->
    <div class="row mb-8">
        @can('index employees')
        <!-- Employees Stats -->
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="stats-card stats-card-employees">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalEmployees">-</h3>
                        <p class="text-white-75 mb-0">{{ __('إجمالي الموظفين') }}</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top border-white-25">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('الحضور اليوم') }}</span>
                        <span class="font-weight-bold realtime-counter" id="presentToday">-</span>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-white" id="attendanceProgress" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        @endcan
        @can('index requests')
        <!-- Requests Stats -->
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="stats-card stats-card-requests">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalRequests">-</h3>
                        <p class="text-white-75 mb-0">{{ __('إجمالي الطلبات') }}</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top border-white-25">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('طلبات معلقة') }}</span>
                        <span class="font-weight-bold realtime-counter" id="pendingRequests">-</span>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-white" id="requestsProgress" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        @endcan
        @can('index craft_licenses')
        <!-- Crafts Stats -->
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="stats-card stats-card-crafts">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalCrafts">-</h3>
                        <p class="text-white-75 mb-0">{{ __('الرخص الحرفية') }}</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-tools fa-2x"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top border-white-25">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('نشطة') }}</span>
                        <span class="font-weight-bold realtime-counter" id="activeCrafts">-</span>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-white" id="craftsProgress" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        @endcan
        @can('index engineerings')
        <!-- Engineering Stats -->
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="stats-card stats-card-engineering">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalEngineering">-</h3>
                        <p class="text-white-75 mb-0">{{ __('ملفات الهندسة') }}</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-drafting-compass fa-2x"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top border-white-25">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('مكتملة') }}</span>
                        <span class="font-weight-bold realtime-counter" id="completedEngineering">-</span>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-white" id="engineeringProgress" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    </div>

    <!-- Reservations, Notices, Imprisonment -->
    @canany(['index reservations', 'index notices', 'index imprisonment_orders'])
    <div class="row mb-8">
        @can('index reservations')
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="stats-card stats-card-reservations">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalReservations">-</h3>
                        <p class="mb-0 opacity-75">{{ __('الحجوزات') }}</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top border-dark-10">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('حجوزات اليوم') }}</span>
                        <span class="font-weight-bold realtime-counter" id="reservationsToday">-</span>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('index notices')
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="stats-card stats-card-notices">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalNotices">-</h3>
                        <p class="mb-0 opacity-75">{{ __('الإخطارات') }}</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-bell fa-2x"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top border-dark-10">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('جديدة اليوم') }}</span>
                        <span class="font-weight-bold realtime-counter" id="noticesToday">-</span>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('index imprisonment_orders')
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="stats-card stats-card-imprisonment">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalImprisonment">-</h3>
                        <p class="mb-0 opacity-75">{{ __('أوامر الحبس') }}</p>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-gavel fa-2x"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top border-dark-10">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('جديدة اليوم') }}</span>
                        <span class="font-weight-bold realtime-counter" id="imprisonmentToday">-</span>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>
    @endcanany

    <!-- Charts and Timeline -->
    <div class="row mb-8">
        <!-- Heatmap -->
        <div class="col-xl-8 mb-4">
            <div class="card card-custom">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        {{ __('خريطة التوزيع الجغرافي') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="heatmap-container">
                        <canvas id="heatmapChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Timeline -->
        <div class="col-xl-4 mb-4">
            <div class="card card-custom">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        {{ __('النشاط المباشر') }}
                        <span class="live-indicator ml-2"></span>
                    </h3>
                    @if(auth()->user()?->hasAnyRole(['admin', 'super-admin', 'super_user']))
                        <div class="card-toolbar">
                            <a href="{{ route('dashboard.realtime.activity-log') }}" class="btn btn-sm btn-light-primary font-weight-bold">
                                <i class="fas fa-list ml-1"></i> عرض الكل
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div id="liveTimeline">
                        <!-- Timeline items will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        @can('store requests')
        <div class="col-xl-12">
            <div class="card card-custom">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        {{ __('إجراءات سريعة') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('requests.index') }}" class="quick-action-link btn btn-primary btn-block btn-lg">
                                    <i class="fas fa-clipboard-list mb-2 fa-2x"></i><br>
                                    {{ __('الطلبات') }}
                                </a>
                            </div>
                        </div>
        @endcan
        @can('index employee_holiday')
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('EmployeeHolidays.index') }}" class="quick-action-link btn btn-warning btn-block btn-lg">
                                    <i class="fas fa-calendar-check mb-2 fa-2x"></i><br>
                                    {{ __('الموافقة على الإجازات') }}
                                    <span class="badge badge-danger ml-2 alert-badge" id="pendingHolidaysBadge">0</span>
                                </a>
                            </div>
                        </div>
        @endcan
        @can('index craft_licenses')
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('craft_licenses.index') }}" class="quick-action-link btn btn-success btn-block btn-lg">
                                    <i class="fas fa-tools mb-2 fa-2x"></i><br>
                                    {{ __('الرخص الحرفية') }}
                                    <span class="badge badge-warning ml-2 alert-badge" id="renewalPendingBadge">0</span>
                                </a>
                            </div>
                        </div>
        @endcan
        @can('index notifications')
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('notifications.index') }}" class="quick-action-link btn btn-info btn-block btn-lg">
                                    <i class="fas fa-bell mb-2 fa-2x"></i><br>
                                    {{ __('الإشعارات') }}
                                    <span class="badge badge-danger ml-2 alert-badge" id="unreadNotificationsBadge">0</span>
                                </a>
                            </div>
                        </div>
        @endcan
        @can('index reservations')
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('reservations.index') }}" class="quick-action-link btn btn-success btn-block btn-lg">
                                    <i class="fas fa-calendar-alt mb-2 fa-2x"></i><br>
                                    {{ __('الحجوزات') }}
                                </a>
                            </div>
                        </div>
        @endcan
        @can('index notices')
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('notices.index') }}" class="quick-action-link btn btn-warning btn-block btn-lg">
                                    <i class="fas fa-bell mb-2 fa-2x"></i><br>
                                    {{ __('الإخطارات') }}
                                </a>
                            </div>
                        </div>
        @endcan
        @can('index imprisonment_orders')
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('imprisonment_orders.index') }}" class="quick-action-link btn btn-danger btn-block btn-lg">
                                    <i class="fas fa-gavel mb-2 fa-2x"></i><br>
                                    {{ __('أوامر الحبس') }}
                                </a>
                            </div>
                        </div>
        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let dashboardData = null;
let updateInterval = null;
let heatmapChart = null;

// Initialize dashboard
$(document).ready(function() {
    loadDashboardData();
    startRealTimeUpdates();
});

function loadDashboardData() {
    $.get('/dashboard/realtime-stats')
        .done(function(data) {
            dashboardData = data;
            updateAllStats(data);
            updateCharts(data);
            updateTimeline(data.timeline);
            updateAlerts(data.system_alerts || []);
        })
        .fail(function() {
            console.error('Failed to load dashboard data');
        });
}

function updateAllStats(data) {
    // Employee stats
    $('#totalEmployees').text(data.employees.total);
    $('#presentToday').text(data.employees.present_today);
    $('#attendanceProgress').css('width', data.employees.attendance_rate + '%');

    // Request stats
    $('#totalRequests').text(data.requests.total);
    $('#pendingRequests').text(data.requests.pending);
    $('#requestsProgress').css('width', data.requests.completion_rate + '%');

    // Craft stats
    $('#totalCrafts').text(data.crafts.total);
    $('#activeCrafts').text(data.crafts.active);
    $('#craftsProgress').css('width', (data.crafts.total > 0 ? ((data.crafts.active / data.crafts.total) * 100) : 0) + '%');

    // Engineering stats
    $('#totalEngineering').text(data.engineering.total);
    $('#completedEngineering').text(data.engineering.completed);
    $('#engineeringProgress').css('width', (data.engineering.total > 0 ? ((data.engineering.completed / data.engineering.total) * 100) : 0) + '%');

    // Reservations, Notices, Imprisonment
    if (data.reservations) {
        $('#totalReservations').text(data.reservations.total);
        $('#reservationsToday').text(data.reservations.today);
    }
    if (data.notices) {
        $('#totalNotices').text(data.notices.total);
        $('#noticesToday').text(data.notices.new_today);
    }
    if (data.imprisonment) {
        $('#totalImprisonment').text(data.imprisonment.total);
        $('#imprisonmentToday').text(data.imprisonment.new_today);
    }

    // Update badges
    $('#pendingHolidaysBadge').text(data.holidays.pending_holidays);
    $('#renewalPendingBadge').text(data.crafts.renewal_pending);
    $('#unreadNotificationsBadge').text(data.notifications.unread);

    // Add animation to counters
    $('.realtime-counter').addClass('text-primary');
    setTimeout(() => {
        $('.realtime-counter').removeClass('text-primary');
    }, 500);
}

function updateCharts(data) {
    const heatmapCtx = document.getElementById('heatmapChart').getContext('2d');
    const chartData = {
        labels: (data.heatmap || []).map(item => item.area),
        datasets: [{
            label: '{{ __('الكثافة') }}',
            data: (data.heatmap || []).map(item => item.count),
            backgroundColor: 'rgba(59, 130, 246, .72)',
            hoverBackgroundColor: 'rgba(37, 99, 235, .9)',
            borderRadius: 6,
            maxBarThickness: 34
        }]
    };

    if (heatmapChart) {
        heatmapChart.data = chartData;
        heatmapChart.update('none');
        return;
    }

    heatmapChart = new Chart(heatmapCtx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#718096' } },
                y: { beginAtZero: true, grid: { color: '#edf1f6' }, ticks: { precision: 0, color: '#718096' } }
            }
        }
    });
}

function updateTimeline(timeline) {
    const iconMap = { activity: 'fa-history', request: 'fa-clipboard-list', holiday: 'fa-calendar', reservation: 'fa-calendar-alt', notice: 'fa-bell' };
    const bgMap = { activity: 'bg-primary', request: 'bg-primary', holiday: 'bg-warning', reservation: 'bg-info', notice: 'bg-secondary' };
    const timelineHtml = timeline.map(item => `
        <div class="timeline-item ${item.priority === 'high' ? 'priority-high' : ''} mb-4">
            <div class="d-flex align-items-start">
                <div class="symbol symbol-40 mr-3">
                    <div class="symbol-label ${bgMap[item.type] || 'bg-light'}">
                        <i class="fas ${iconMap[item.type] || 'fa-circle'} text-white"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-1">${item.title}</h6>
                            <p class="text-muted mb-1">${item.description}</p>
                            <small class="text-muted">${item.user} • ${item.time}</small>
                        </div>
                        ${item.priority === 'high' ? '<span class="badge badge-danger">عاجل</span>' : ''}
                    </div>
                </div>
            </div>
        </div>
    `).join('');

    $('#liveTimeline').html(timelineHtml || '<p class="text-muted text-center">{{ __('لا توجد أنشطة حديثة') }}</p>');
}

function updateAlerts(alerts) {
    const alertsHtml = alerts.map(alert => `
        <div class="alert alert-${alert.type} alert-dismissible fade show" role="alert">
            <div class="alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="alert-text">
                ${alert.message}
            </div>
            <div class="alert-actions">
                <a href="${alert.action_url}" class="btn btn-sm btn-${alert.type}">
                    {{ __('عرض التفاصيل') }}
                </a>
            </div>
        </div>
    `).join('');

    $('#systemAlerts').html(alertsHtml);
}

function startRealTimeUpdates() {
    // Update every 30 seconds
    updateInterval = setInterval(() => {
        loadDashboardData();
    }, 30000);
}

function refreshAllData(btn) {
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('جاري التحديث...') }}';
    btn.disabled = true;

    loadDashboardData();
    setTimeout(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }, 1000);
}

// Clean up on page unload
$(window).on('beforeunload', function() {
    if (updateInterval) {
        clearInterval(updateInterval);
    }
});
</script>
@endpush
