@extends('layout.master')

@section('title')
{{ __('Administrative Affairs') }}
@endsection

@section('page_title')
{{ __('controll_panel') }}
@endsection

@section('sub_main')
{{ __('home') }}
@endsection

@section('sub_title')
{{ __('Administrative Affairs') }}
@endsection


@section('content')
    <div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('Administrative Affairs') }}"
            subtitle="{{ __('Overview of administrative affairs and employee management') }}"
        />

        <!-- Main Stats -->
        <div class="row mb-5">
            @can('index employees')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('employees')"
                    :value="$stats['employees_count']"
                    icon="fa-users"
                    color="primary"
                    :link="route('employees.index')"
                    :linkText="__('View Details')"
                />
            </div>
            @endcan

            @can('index zk')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Today Attendance')"
                    :value="$stats['today_attendance']"
                    icon="fa-fingerprint"
                    color="success"
                    :link="route('zk')"
                    :linkText="__('View Attendance')"
                />
            </div>
            @endcan

            @can('index employee_holiday')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Pending Holidays')"
                    :value="$stats['pending_holidays']"
                    icon="fa-calendar-check"
                    color="warning"
                    :link="route('EmployeeHolidays.index')"
                    :linkText="__('View Requests')"
                />
            </div>
            @endcan

            @can('index EmployeePermissions')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Pending Permissions')"
                    :value="$stats['pending_permissions'] ?? 0"
                    icon="fa-clock"
                    color="info"
                    :link="route('EmployeePermissions.index')"
                    :linkText="__('View Requests')"
                />
            </div>
            @endcan
        </div>

        <!-- Secondary Stats -->
        <div class="row mb-5">
            @can('index jobs')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('jobs')"
                    :value="$stats['jobs_count'] ?? 0"
                    icon="fa-briefcase"
                    color="danger"
                    :link="route('jobs.index')"
                    :linkText="__('View All')"
                />
            </div>
            @endcan

            @can('index vehicle')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('vehicles')"
                    :value="$stats['vehicles_count'] ?? 0"
                    icon="fa-car"
                    color="dark"
                    :link="route('vehicle.index')"
                    :linkText="__('View Details')"
                />
            </div>
            @endcan

            @can('index departments')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('departments')"
                    :value="$stats['departments_count'] ?? 0"
                    icon="fa-building"
                    color="primary"
                    :link="route('departments.index')"
                    :linkText="__('View All')"
                />
            </div>
            @endcan

            @can('index sections')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('sections')"
                    :value="$stats['sections_count'] ?? 0"
                    icon="fa-network-wired"
                    color="success"
                    :link="route('sections.index')"
                    :linkText="__('View All')"
                />
            </div>
            @endcan
        </div>

        <!-- Secondary Stats -->
        <div class="row mb-5">
            @can('index jobs')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('jobs')"
                    :value="$stats['jobs_count'] ?? 0"
                    icon="fa-briefcase"
                    color="danger"
                    :link="route('jobs.index')"
                    :linkText="__('View All')"
                />
            </div>
            @endcan

            @can('index vehicle')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('vehicles')"
                    :value="$stats['vehicles_count'] ?? 0"
                    icon="fa-car"
                    color="dark"
                    :link="route('vehicle.index')"
                    :linkText="__('View Details')"
                />
            </div>
            @endcan

            @can('index departments')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('departments')"
                    :value="$stats['departments_count'] ?? 0"
                    icon="fa-building"
                    color="primary"
                    :link="route('departments.index')"
                    :linkText="__('View All')"
                />
            </div>
            @endcan

            @can('index sections')
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('sections')"
                    :value="$stats['sections_count'] ?? 0"
                    icon="fa-network-wired"
                    color="success"
                    :link="route('sections.index')"
                    :linkText="__('View All')"
                />
            </div>
            @endcan
        </div>

        <div class="row">
            <!-- Charts Section -->
            <div class="col-lg-8">
                <div class="card card-custom shadow-sm gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-chart-area text-primary mr-2"></i>{{ __('Attendance Analytics') }}
                            </span>
                            <span class="text-muted mt-2 font-weight-bold font-size-sm">{{ __('Last 7 days performance') }}</span>
                        </h3>
                    </div>
                    <div class="card-body" style="min-height: 380px;">
                        <div id="attendance_chart" class="admin-chart-container" style="height: 350px;"></div>
                    </div>
                </div>

                <!-- Latest Holidays -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-calendar-check text-warning mr-2"></i>{{ __('Latest Holiday Requests') }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('EmployeeHolidays.index') }}" class="btn btn-light-warning btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive admin-table">
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
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ \Carbon\Carbon::parse($holiday->created_at)->diffForHumans() }}</span>
                                                <span class="text-muted font-weight-bold">{{ \Carbon\Carbon::parse($holiday->created_at)->format('Y/m/d') }}</span>
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

                <!-- Latest Activities -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-user-plus text-primary mr-2"></i>{{ __('Latest Employee Registrations') }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('employees.index') }}" class="btn btn-light-primary btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive admin-table">
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
                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ \Carbon\Carbon::parse($emp->created_at)->diffForHumans() }}</span>
                                            <span class="text-muted font-weight-bold">{{ \Carbon\Carbon::parse($emp->created_at)->format('Y/m/d') }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Panels -->
            <div class="col-lg-4">
                <!-- Department Distribution -->
                <div class="card card-custom shadow-sm gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-sitemap text-primary mr-2"></i>{{ __('Employees by Dept.') }}
                            </span>
                            <span class="text-muted mt-2 font-weight-bold font-size-sm">{{ __('Total: ') }} {{ number_format($dept_distribution->sum('employees_count')) }}</span>
                        </h3>
                    </div>
                    <div class="card-body" style="min-height: 700px;">
                        <div id="dept_chart" style="height: 640px; width: 100%;"></div>
                        
                        <!-- Department Statistics Table -->
                        <div class="mt-5">
                            <div class="table-responsive">
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

                <!-- Quick Actions Grid -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark"><i class="fas fa-bolt text-warning mr-2"></i>{{ __('Quick Actions') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="row">
                            <div class="col-6 mb-4">
                                <a href="{{ route('employees.index') }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fas fa-users text-primary"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Add Employee') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('EmployeeHolidays.index') }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fas fa-calendar text-success"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Holidays') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('employee_reports') }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fas fa-chart-line text-warning"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Reports') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- تحميل ApexCharts من CDN (نسخة UMD) لضمان ظهور الرسوم البيانية --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
    <script>
        $(document).ready(function() {
            var Apex = window.ApexCharts || (typeof ApexCharts !== 'undefined' ? ApexCharts : null);
            if (!Apex) {
                console.error('فشل تحميل مكتبة ApexCharts');
                return;
            }
            var commonOptions = {
                chart: {
                    fontFamily: 'Cairo, Tajawal, sans-serif',
                    toolbar: { show: false }
                },
                states: {
                    hover: { filter: { type: 'none' } }
                },
                noData: {
                    text: 'لا توجد بيانات',
                    align: 'center',
                    verticalAlign: 'middle',
                    offsetX: 0,
                    offsetY: 0,
                    style: {
                        color: '#888',
                        fontSize: '16px',
                        fontFamily: 'Cairo, sans-serif'
                    }
                }
            };

            var attendanceData = {!! json_encode($attendance_data) !!};
            if (!Array.isArray(attendanceData)) attendanceData = [];
            var attendanceCategories = attendanceData.map(function(item) { return item.date; });
            var attendanceCounts = attendanceData.map(function(item) { return item.count; });
            var attendanceLate = attendanceData.map(function(item) { return item.total_late || 0; });

            var attendanceOptions = {
                chart: {
                    type: 'area',
                    height: 350,
                    fontFamily: commonOptions.chart.fontFamily,
                    toolbar: { show: false }
                },
                series: [
                    { name: '{{ __("Attendance") }}', data: attendanceCounts },
                    { name: '{{ __("Total Late Minutes") }}', data: attendanceLate }
                ],
                xaxis: {
                    type: 'category',
                    categories: attendanceCategories.length ? attendanceCategories : ['-'],
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                colors: ['#3699FF', '#FFA800'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100]
                    }
                },
                stroke: { curve: 'smooth', width: 3 },
                noData: commonOptions.noData,
                states: commonOptions.states
            };

            var deptData = {!! json_encode($dept_distribution) !!};
            if (!Array.isArray(deptData)) deptData = [];
            var deptLabels = deptData.map(function(item) { return item.name; });
            var deptSeries = deptData.map(function(item) { return Number(item.employees_count) || 0; });

            if (deptSeries.length === 0 || deptSeries.every(function(val) { return val === 0; })) {
                deptLabels = ['{{ __("No data") }}'];
                deptSeries = [1];
            }

            var deptOptions = {
                chart: {
                    type: 'donut',
                    height: 640,
                    fontFamily: commonOptions.chart.fontFamily,
                    toolbar: { show: false }
                },
                series: deptSeries,
                labels: deptLabels,
                colors: ['#3699FF', '#1BC5BD', '#8950FC', '#FFA800', '#F64E60', '#3F4254', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#008FFB', '#00D9E9', '#FF9800', '#EA454C', '#2E93fA', '#66DA26', '#546E7A', '#E91E63', '#FF9800'],
                legend: { 
                    position: 'bottom', 
                    fontSize: '11px',
                    itemMargin: { horizontal: 5, vertical: 2 },
                    height: 200
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '14px', fontWeight: 600 },
                                value: { show: true, fontSize: '18px', fontWeight: 700 },
                                total: {
                                    show: true,
                                    label: "{{ __('Total') }}",
                                    fontSize: '16px',
                                    fontWeight: 600,
                                    formatter: function (w) {
                                        var arr = w.globals.series || [];
                                        return arr.reduce(function(a, b) { return a + b; }, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                noData: commonOptions.noData,
                states: commonOptions.states,
                fill: {
                    opacity: 1,
                    type: 'solid'
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['#fff']
                }
            };

            function renderCharts() {
                var elAttendance = document.getElementById('attendance_chart');
                var elDept = document.getElementById('dept_chart');

                if (elAttendance) {
                    try {
                        var attendanceChart = new Apex(elAttendance, attendanceOptions);
                        attendanceChart.render();
                    } catch(e) {
                        console.error('Error rendering attendance chart:', e);
                    }
                }
                if (elDept) {
                    try {
                        var deptChart = new Apex(elDept, deptOptions);
                        deptChart.render();
                    } catch(e) {
                        console.error('Error rendering dept chart:', e);
                    }
                }
            }

            setTimeout(renderCharts, 250);
        });
    </script>
@endsection
