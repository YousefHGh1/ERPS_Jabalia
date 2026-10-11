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
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
    transition: transform 0.3s ease;
}
.stats-card:hover {
    transform: translateY(-5px);
}
.live-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
.heatmap-container {
    background: #f3f4f6;
    border-radius: 10px;
    padding: 1rem;
}
.timeline-item {
    border-left: 3px solid #e5e7eb;
    padding-left: 1rem;
    margin-left: 0.5rem;
}
.timeline-item.priority-high {
    border-left-color: #ef4444;
}
.alert-badge {
    animation: bounce 2s infinite;
}
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}
.realtime-counter {
    font-variant-numeric: tabular-nums;
}
.chart-container {
    position: relative;
    height: 300px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-8">
        <div>
            <h1 class="h3 mb-0 text-dark font-weight-bolder">
                {{ __('مركز التحكم التفاعلي') }}
                <span class="live-indicator ml-2"></span>
            </h1>
            <p class="text-muted mt-2">{{ __('تحديثات فورية للإحصائيات والأنشطة') }}</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge badge-success mr-3">
                <i class="fas fa-circle text-success mr-1" style="font-size: 8px;"></i>
                {{ __('متصل') }}
            </span>
            <button class="btn btn-light-primary" onclick="refreshAllData()">
                <i class="fas fa-sync-alt mr-2"></i>
                {{ __('تحديث') }}
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
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalEmployees">-</h3>
                        <p class="text-white-75 mb-0">{{ __('إجمالي الموظفين') }}</p>
                    </div>
                    <div class="text-white-50">
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
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalRequests">-</h3>
                        <p class="text-white-75 mb-0">{{ __('إجمالي الطلبات') }}</p>
                    </div>
                    <div class="text-white-50">
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
            <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalCrafts">-</h3>
                        <p class="text-white-75 mb-0">{{ __('الرخص الحرفية') }}</p>
                    </div>
                    <div class="text-white-50">
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
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalEngineering">-</h3>
                        <p class="text-white-75 mb-0">{{ __('ملفات الهندسة') }}</p>
                    </div>
                    <div class="text-white-50">
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
            <div class="stats-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalReservations">-</h3>
                        <p class="mb-0 opacity-75">{{ __('الحجوزات') }}</p>
                    </div>
                    <div class="opacity-75">
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
            <div class="stats-card" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalNotices">-</h3>
                        <p class="mb-0 opacity-75">{{ __('الإخطارات') }}</p>
                    </div>
                    <div class="opacity-75">
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
            <div class="stats-card" style="background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: #333;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="font-size-h2 mb-2 realtime-counter" id="totalImprisonment">-</h3>
                        <p class="mb-0 opacity-75">{{ __('أوامر الحبس') }}</p>
                    </div>
                    <div class="opacity-75">
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
                                <a href="{{ route('requests.index') }}" class="btn btn-primary btn-block btn-lg">
                                    <i class="fas fa-clipboard-list mb-2 fa-2x"></i><br>
                                    {{ __('الطلبات') }}
                                </a>
                            </div>
                        </div>
        @endcan
        @can('index employee_holiday')
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('EmployeeHolidays.index') }}" class="btn btn-warning btn-block btn-lg">
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
                                <a href="{{ route('craft_licenses.index') }}" class="btn btn-success btn-block btn-lg">
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
                                <a href="{{ route('notifications.index') }}" class="btn btn-info btn-block btn-lg">
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
                                <a href="{{ route('reservations.index') }}" class="btn btn-success btn-block btn-lg">
                                    <i class="fas fa-calendar-alt mb-2 fa-2x"></i><br>
                                    {{ __('الحجوزات') }}
                                </a>
                            </div>
                        </div>
        @endcan
        @can('index notices')
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('notices.index') }}" class="btn btn-warning btn-block btn-lg">
                                    <i class="fas fa-bell mb-2 fa-2x"></i><br>
                                    {{ __('الإخطارات') }}
                                </a>
                            </div>
                        </div>
        @endcan
        @can('index imprisonment_orders')
                        <div class="col-md-3 mb-4">
                            <div class="text-center">
                                <a href="{{ route('imprisonment_orders.index') }}" class="btn btn-danger btn-block btn-lg">
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
    // Heatmap Chart
    const heatmapCtx = document.getElementById('heatmapChart').getContext('2d');
    new Chart(heatmapCtx, {
        type: 'bar',
        data: {
            labels: data.heatmap.map(item => item.area),
            datasets: [{
                label: '{{ __('الكثافة') }}',
                data: data.heatmap.map(item => item.count),
                backgroundColor: data.heatmap.map(item => 
                    `rgba(255, ${255 - item.intensity * 2}, ${255 - item.intensity * 2}, 0.8)`
                ),
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateTimeline(timeline) {
    const iconMap = { request: 'fa-clipboard-list', holiday: 'fa-calendar', reservation: 'fa-calendar-alt', notice: 'fa-bell' };
    const bgMap = { request: 'bg-primary', holiday: 'bg-warning', reservation: 'bg-info', notice: 'bg-secondary' };
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

function refreshAllData() {
    const btn = event.target;
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
