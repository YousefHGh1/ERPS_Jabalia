@extends('layout.master')

@section('title')
{{ __('Public Office') }}
@endsection

@section('page_title')
{{ __('controll_panel') }}
@endsection

@section('sub_main')
{{ __('home') }}
@endsection

@section('sub_title')
{{ __('Public Office') }}
@endsection

@section('content')
    <div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('Public Office') }}"
            subtitle="{{ __('Overview of taxpayers requests and public office management') }}"
        />

        @cannot('index requests')
            <div class="alert alert-warning" role="alert">
                {{ __('ليس لديك صلاحية كافية لعرض لوحة قلم الجمهور.') }}
            </div>
        @endcannot

        @can('index requests')
        <!-- Public Office Stats -->
        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Total Requests')"
                    :value="$stats['total_requests'] ?? 0"
                    icon="fa-file-alt"
                    color="primary"
                    :link="route('requests.index')"
                    :linkText="__('View All')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Pending Requests')"
                    :value="$stats['pending_requests'] ?? 0"
                    icon="fa-clock"
                    color="warning"
                    :link="route('requests.index')"
                    :linkText="__('View Pending')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Urgent Requests')"
                    :value="$stats['urgent_requests'] ?? 0"
                    icon="fa-exclamation-triangle"
                    color="danger"
                    :link="route('requests.index')"
                    :linkText="__('View Urgent')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Overdue Requests')"
                    :value="$stats['overdue_requests'] ?? 0"
                    icon="fa-calendar-times"
                    color="dark"
                    :link="route('requests.index')"
                    :linkText="__('View Overdue')"
                />
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Taxpayers')"
                    :value="$stats['mokalaf_count'] ?? 0"
                    icon="fa-users"
                    color="info"
                    :link="route('mokalafs.index')"
                    :linkText="__('View All')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Request Types')"
                    :value="$stats['request_types_count'] ?? 0"
                    icon="fa-list"
                    color="success"
                    :link="route('request-types.index')"
                    :linkText="__('View Types')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Total Revenue')"
                    :value="$stats['total_revenue'] ?? 0"
                    icon="fa-coins"
                    color="success"
                    :link="route('requests.index')"
                    :linkText="__('View Payments')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Unpaid Requests')"
                    :value="$stats['unpaid_requests'] ?? 0"
                    icon="fa-dollar-sign"
                    color="warning"
                    :link="route('requests.index')"
                    :linkText="__('View Unpaid')"
                />
            </div>
        </div>

        <div class="row">
            <!-- Charts Section -->
            <div class="col-lg-8">
                <!-- Requests by Type -->
                <div class="card card-custom shadow-sm gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-chart-bar text-primary mr-2"></i>{{ __('Requests by Type') }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body" style="min-height: 380px;">
                        <div id="request_type_chart" class="admin-chart-container" style="height: 350px;"></div>
                    </div>
                </div>

                <!-- Latest Requests -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-file-contract text-info mr-2"></i>{{ __('Latest Requests') }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('requests.index') }}" class="btn btn-light-info btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive admin-table">
                            <table class="table table-head-custom table-vertical-center table-borderless">
                                <thead>
                                    <tr class="text-left text-muted font-weight-bolder">
                                        <th class="pl-0" style="min-width: 50px">{{ __('ID') }}</th>
                                        <th style="min-width: 150px">{{ __('Taxpayer') }}</th>
                                        <th style="min-width: 100px" class="text-center">{{ __('Type') }}</th>
                                        <th style="min-width: 100px" class="text-center">{{ __('Status') }}</th>
                                        <th style="min-width: 100px" class="text-right">{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($latest_requests) && $latest_requests->count() > 0)
                                        @foreach($latest_requests as $request)
                                        <tr>
                                            <td class="pl-0 py-5">
                                                <span class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg">#{{ $request->id }}</span>
                                            </td>
                                            <td class="pl-0">
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $request->mokalaf_name ?? '---' }}</span>
                                                <span class="text-muted font-weight-bold">{{ $request->identity_number ?? $request->license_number ?? '---' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-lg label-light-info label-inline font-weight-bold">{{ $request->request_type_name ?? '---' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-lg label-light-{{ $request->status_color ?? 'primary' }} label-inline font-weight-bold">
                                                    {{ $request->status_name ?? '---' }}
                                                </span>
                                            </td>
                                            <td class="text-right pr-0">
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</span>
                                                <span class="text-muted font-weight-bold">{{ \Carbon\Carbon::parse($request->created_at)->format('Y/m/d') }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center py-10">
                                                <span class="text-muted">{{ __('No requests found') }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Panels -->
            <div class="col-lg-4">
                <!-- Quick Actions Grid -->
                <div class="card card-custom shadow-sm gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-bolt text-warning mr-2"></i>{{ __('Quick Actions') }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="row">
                            <div class="col-6 mb-4">
                                <a href="{{ route('requests.create') }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-file-circle-plus text-info"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('New Request') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('mokalafs.create') }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-user-plus text-success"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Add Taxpayer') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('requests.index', ['status' => 'pending']) }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-clock text-warning"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Pending') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('requests.index', ['urgent' => 'true']) }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-exclamation-triangle text-danger"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Urgent') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requests by Status -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-chart-pie text-success mr-2"></i>{{ __('Requests by Status') }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body" style="min-height: 340px;">
                        <div id="request_status_chart" style="height: 300px; width: 100%;"></div>
                    </div>
                </div>

                <!-- Urgent & Overdue Requests -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-fire text-danger mr-2"></i>{{ __('Urgent & Overdue') }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('requests.index', ['urgent' => 'true']) }}" class="btn btn-light-danger btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive admin-table">
                            <table class="table table-sm table-borderless">
                                <thead>
                                    <tr class="text-left text-muted font-weight-bolder font-size-xs">
                                        <th>{{ __('Request') }}</th>
                                        <th class="text-center">{{ __('Type') }}</th>
                                        <th class="text-right">{{ __('Deadline') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($urgent_overdue_requests) && $urgent_overdue_requests->count() > 0)
                                        @foreach($urgent_overdue_requests as $request)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($request->is_urgent)
                                                        <span class="label label-light-danger label-inline font-weight-bolder mr-2" style="font-size: 0.7rem">{{ __('Urgent') }}</span>
                                                    @endif
                                                    <span class="text-dark-75 font-weight-bolder font-size-sm">#{{ $request->id }}</span>
                                                </div>
                                                <span class="text-muted font-weight-bold font-size-xs">{{ $request->mokalaf_name ?? '---' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-dark font-weight-bolder font-size-xs">{{ $request->request_type_name ?? '---' }}</span>
                                            </td>
                                            <td class="text-right">
                                                @if($request->deadline)
                                                    <span class="font-weight-bolder {{ $request->deadline < now() ? 'text-danger' : 'text-warning' }} font-size-xs">
                                                        {{ \Carbon\Carbon::parse($request->deadline)->format('Y/m/d') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted font-size-xs">---</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <span class="text-muted font-size-sm">{{ __('No urgent or overdue requests') }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
    <script>
        $(document).ready(function() {
            var Apex = window.ApexCharts || (typeof ApexCharts !== 'undefined' ? ApexCharts : null);
            if (!Apex) { console.error('فشل تحميل مكتبة ApexCharts'); return; }
            var noDataOpt = { text: 'لا توجد بيانات', align: 'center', verticalAlign: 'middle', style: { color: '#888', fontSize: '16px', fontFamily: 'Cairo, sans-serif' } };
            var requestTypeData = {!! json_encode($request_type_data ?? []) !!};
            if (!Array.isArray(requestTypeData)) requestTypeData = [];
            var typeNames = requestTypeData.map(function(i) { return i.name; });
            var typeCounts = requestTypeData.map(function(i) { return i.count; });
            if (typeNames.length === 0) typeNames = ['-'];
            var requestTypeOptions = {
                chart: { type: 'bar', height: 350, fontFamily: 'Cairo, sans-serif', toolbar: { show: false } },
                series: [{ name: '{{ __("Requests") }}', data: typeCounts }],
                xaxis: { categories: typeNames, axisBorder: { show: false }, axisTicks: { show: false } },
                colors: ['#3699FF'],
                plotOptions: { bar: { borderRadius: 10, columnWidth: '45%', dataLabels: { position: 'top' } } },
                dataLabels: { enabled: true, formatter: function (val) { return val; }, offsetY: -20, style: { fontSize: '12px', colors: ["#304758"] } },
                yaxis: { axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#6E7988' } } },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3, stops: [0, 90, 100] } },
                tooltip: { y: { formatter: function (val) { return val + " {{ __("Requests") }}"; } } },
                noData: noDataOpt,
                states: { hover: { filter: { type: 'none' } } }
            };
            var requestStatusData = {!! json_encode($request_status_data ?? []) !!};
            if (!Array.isArray(requestStatusData)) requestStatusData = [];
            var statusLabels = requestStatusData.map(function(i) { return i.name; });
            var statusSeries = requestStatusData.map(function(i) { return i.count; });
            if (statusSeries.length === 0) { statusLabels = ['{{ __("No data") }}']; statusSeries = [0]; }
            var requestStatusOptions = {
                chart: { type: 'donut', height: 300, fontFamily: 'Cairo, sans-serif', toolbar: { show: false } },
                series: statusSeries,
                labels: statusLabels,
                colors: ['#1BC5BD', '#F64E60', '#FFA800', '#3699FF', '#8950FC'],
                legend: { position: 'bottom', fontSize: '12px' },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '14px', fontWeight: 600 },
                                value: { show: true, fontSize: '18px', fontWeight: 700 },
                                total: { show: true, label: "{{ __('Total') }}", formatter: function (w) {
                                    var arr = w.globals.series || []; return arr.reduce(function(a, b) { return a + b; }, 0);
                                }}
                            }
                        }
                    }
                },
                noData: noDataOpt,
                states: { hover: { filter: { type: 'none' } } }
            };
            function renderCharts() {
                var elType = document.getElementById('request_type_chart'), elStatus = document.getElementById('request_status_chart');
                if (elType && elType.offsetParent) { var t = new Apex(elType, requestTypeOptions); t.render(); }
                if (elStatus && elStatus.offsetParent) { var s = new Apex(elStatus, requestStatusOptions); s.render(); }
            }
            setTimeout(renderCharts, 250);
        });
    </script>
@endsection
