@extends('layout.master')

@section('title')
{{ __('Engineering and Organization') }}
@endsection

@section('page_title')
{{ __('controll_panel') }}
@endsection

@section('sub_main')
{{ __('home') }}
@endsection

@section('sub_title')
{{ __('Engineering and Organization') }}
@endsection

@section('content')
    <div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('Engineering and Organization') }}"
            subtitle="{{ __('Overview of engineering files and organization management') }}"
        />

        @cannot('index engineerings')
            <div class="alert alert-warning" role="alert">
                {{ __('ليس لديك صلاحية كافية لعرض لوحة الهندسة والتنظيم.') }}
            </div>
        @endcannot

        @can('index engineerings')
        <!-- Engineering Stats -->
        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Engineering Files')"
                    :value="$stats['engineering_count'] ?? 0"
                    icon="fa-drafting-compass"
                    color="info"
                    :link="route('engineering.index')"
                    :linkText="__('View Files')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Engineering Payments')"
                    :value="$stats['engineering_payments_count'] ?? 0"
                    icon="fa-money-bill-wave"
                    color="primary"
                    :link="route('engineering.index')"
                    :linkText="__('View Files')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Total Revenue')"
                    :value="$stats['engineering_revenue'] ?? 0"
                    icon="fa-chart-line"
                    color="success"
                    :link="route('engineering.export')"
                    :linkText="__('Export Reports')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Pending Payments')"
                    :value="$stats['pending_payments'] ?? 0"
                    icon="fa-hourglass-half"
                    color="warning"
                    :link="route('engineering.index', ['status' => 'pending'])"
                    :linkText="__('View Pending')"
                />
            </div>
        </div>

        <div class="row">
            <!-- Charts Section -->
            <div class="col-lg-8">
                <!-- Engineering Files by Status -->
                <div class="card card-custom shadow-sm gutter-b">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Engineering Files by Status') }}</span>
                        </h3>
                    </div>
                    <div class="card-body" style="min-height: 380px;">
                        <div id="engineering_status_chart" class="admin-chart-container" style="height: 350px;"></div>
                    </div>
                </div>

                <!-- Latest Engineering Files -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Latest Engineering Files') }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('engineering.index') }}" class="btn btn-light-info btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive admin-table">
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
                                                <span class="text-muted font-weight-bold">{{ $file->neighborhood ?? '---' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-lg label-light-{{ $file->remaining_amount == 0 ? 'success' : ($file->paid_amount > 0 ? 'warning' : 'danger') }} label-inline font-weight-bold">
                                                    {{ $file->remaining_amount == 0 ? __('Paid') : ($file->paid_amount > 0 ? __('Partial') : __('Pending')) }}
                                                </span>
                                            </td>
                                            <td class="text-right pr-0">
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ \Carbon\Carbon::parse($file->created_at)->diffForHumans() }}</span>
                                                <span class="text-muted font-weight-bold">{{ \Carbon\Carbon::parse($file->created_at)->format('Y/m/d') }}</span>
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
            </div>

            <!-- Side Panels -->
            <div class="col-lg-4">
                <!-- Quick Actions Grid -->
                <div class="card card-custom shadow-sm gutter-b">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Quick Actions') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="row">
                            <div class="col-6 mb-4">
                                <a href="{{ route('engineering.create') }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-file-circle-plus text-info"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('New File') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('engineering.export') }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-file-arrow-down text-success"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Export') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('engineering.index', ['status' => 'pending']) }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-clock text-warning"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Pending') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('engineering.index', ['status' => 'completed']) }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-circle-check text-primary"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Completed') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue by Month -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Revenue by Month') }}</span>
                        </h3>
                    </div>
                    <div class="card-body" style="min-height: 340px;">
                        <div id="revenue_chart" style="height: 300px; width: 100%;"></div>
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
            var engineeringStatusData = {!! json_encode($engineering_status_data ?? []) !!};
            if (!Array.isArray(engineeringStatusData)) engineeringStatusData = [];
            var statusLabels = engineeringStatusData.map(function(i) { return i.status; });
            var statusSeries = engineeringStatusData.map(function(i) { return i.count; });
            if (statusSeries.length === 0) { statusLabels = ['{{ __("No data") }}']; statusSeries = [0]; }
            var statusOptions = {
                chart: { type: 'donut', height: 350, fontFamily: 'Cairo, sans-serif', toolbar: { show: false } },
                series: statusSeries,
                labels: statusLabels,
                colors: ['#1BC5BD', '#F64E60', '#FFA800', '#3699FF'],
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
            var revenueData = {!! json_encode($revenue_by_month ?? []) !!};
            if (!Array.isArray(revenueData)) revenueData = [];
            var revenueMonths = revenueData.map(function(i) { return i.month; });
            var revenueAmounts = revenueData.map(function(i) { return i.amount; });
            if (revenueMonths.length === 0) revenueMonths = ['-'];
            var revenueOptions = {
                chart: { type: 'bar', height: 300, fontFamily: 'Cairo, sans-serif', toolbar: { show: false } },
                series: [{ name: '{{ __("Revenue") }}', data: revenueAmounts }],
                xaxis: { categories: revenueMonths, axisBorder: { show: false }, axisTicks: { show: false } },
                colors: ['#1BC5BD'],
                plotOptions: { bar: { borderRadius: 10, columnWidth: '60%' } },
                yaxis: { labels: { formatter: function (v) { return v.toLocaleString(); } } },
                noData: noDataOpt,
                states: { hover: { filter: { type: 'none' } } }
            };
            function renderCharts() {
                var elStatus = document.getElementById('engineering_status_chart'), elRev = document.getElementById('revenue_chart');
                if (elStatus && elStatus.offsetParent) { var c = new Apex(elStatus, statusOptions); c.render(); }
                if (elRev && elRev.offsetParent) { var r = new Apex(elRev, revenueOptions); r.render(); }
            }
            setTimeout(renderCharts, 250);
        });
    </script>
@endsection
