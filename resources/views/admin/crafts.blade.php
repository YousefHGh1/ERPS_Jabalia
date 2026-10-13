@extends('layout.master')

@section('title')
{{ __('Crafts and Industries') }}
@endsection

@section('page_title')
{{ __('controll_panel') }}
@endsection

@section('sub_main')
{{ __('home') }}
@endsection

@section('sub_title')
{{ __('Crafts and Industries') }}
@endsection

@section('content')
    <div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('Crafts and Industries') }}"
            subtitle="{{ __('Overview of craft licenses and industrial activities') }}"
        />

        @cannot('index craft_licenses')
            <div class="alert alert-warning" role="alert">
                {{ __('ليس لديك صلاحية كافية لعرض لوحة الحرف والصناعات.') }}
            </div>
        @endcannot

        @can('index craft_licenses')
        <!-- Craft Licenses Stats -->
        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Craft Licenses')"
                    :value="$stats['craft_licenses_count'] ?? 0"
                    icon="fa-screwdriver-wrench"
                    color="dark"
                    :link="route('craft_licenses.index')"
                    :linkText="__('View Licenses')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Total Payments')"
                    :value="$stats['craft_payments_count'] ?? 0"
                    icon="fa-receipt"
                    color="info"
                    :link="route('craft_licenses.index')"
                    :linkText="__('View All')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Craft Revenue')"
                    :value="$stats['craft_revenue'] ?? 0"
                    icon="fa-sack-dollar"
                    color="success"
                    :link="route('craft_licenses.export')"
                    :linkText="__('Export Reports')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Unpaid Licenses')"
                    :value="$stats['unpaid_licenses'] ?? 0"
                    icon="fa-triangle-exclamation"
                    color="danger"
                    :link="route('craft_licenses.index', ['status' => 'unpaid'])"
                    :linkText="__('View Unpaid')"
                />
            </div>
        </div>

        <div class="row">
            <!-- Charts Section -->
            <div class="col-lg-8">
                <!-- Craft Licenses by Category -->
                <div class="card card-custom shadow-sm gutter-b">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-chart-bar text-primary mr-2"></i>{{ __('Craft Licenses by Category') }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body" style="min-height: 380px;">
                        <div id="craft_category_chart" class="admin-chart-container" style="height: 350px;"></div>
                    </div>
                </div>

                <!-- Latest Craft Licenses -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-file-contract text-info mr-2"></i>{{ __('Latest Craft Licenses') }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('craft_licenses.index') }}" class="btn btn-light-info btn-sm font-weight-bolder">{{ __('View All') }}</a>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive admin-table">
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
                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ \Carbon\Carbon::parse($license->created_at)->diffForHumans() }}</span>
                                                <span class="text-muted font-weight-bold">{{ \Carbon\Carbon::parse($license->created_at)->format('Y/m/d') }}</span>
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
                                <a href="{{ route('craft_licenses.create') }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-file-circle-plus text-info"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('New License') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('craft_licenses.export') }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-file-arrow-down text-success"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Export') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('craft_licenses.index', ['status' => 'pending']) }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-clock text-warning"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Pending') }}</span>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="{{ route('craft_licenses.index', ['status' => 'active']) }}" class="admin-quick-action-card text-decoration-none">
                                    <i class="fa fa-circle-check text-primary"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Active') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Craft Licenses Distribution -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">
                                <i class="fa fa-chart-pie text-success mr-2"></i>{{ __('Licenses by Status') }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body" style="min-height: 340px;">
                        <div id="craft_status_chart" style="height: 300px; width: 100%;"></div>
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
            var craftCategoryData = {!! json_encode($craft_category_data ?? []) !!};
            if (!Array.isArray(craftCategoryData)) craftCategoryData = [];
            var categoryNames = craftCategoryData.map(function(i) { return i.name; });
            var categoryCounts = craftCategoryData.map(function(i) { return i.count; });
            if (categoryNames.length === 0) categoryNames = ['-'];
            var craftCategoryOptions = {
                chart: { type: 'bar', height: 350, fontFamily: 'Cairo, sans-serif', toolbar: { show: false } },
                series: [{ name: '{{ __("Licenses") }}', data: categoryCounts }],
                xaxis: { categories: categoryNames, axisBorder: { show: false }, axisTicks: { show: false } },
                colors: ['#3699FF'],
                plotOptions: { bar: { borderRadius: 10, columnWidth: '45%', dataLabels: { position: 'top' } } },
                dataLabels: { enabled: true, formatter: function (val) { return val; }, offsetY: -20, style: { fontSize: '12px', colors: ["#304758"] } },
                yaxis: { axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#6E7988' } } },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3, stops: [0, 90, 100] } },
                tooltip: { y: { formatter: function (val) { return val + " {{ __("Licenses") }}"; } } },
                noData: noDataOpt,
                states: { hover: { filter: { type: 'none' } } }
            };
            var craftStatusData = {!! json_encode($craft_status_data ?? []) !!};
            if (!Array.isArray(craftStatusData)) craftStatusData = [];
            var statusLabels = craftStatusData.map(function(i) { return i.name; });
            var statusSeries = craftStatusData.map(function(i) { return i.count; });
            if (statusSeries.length === 0) { statusLabels = ['{{ __("No data") }}']; statusSeries = [0]; }
            var craftStatusOptions = {
                chart: { type: 'donut', height: 300, fontFamily: 'Cairo, sans-serif', toolbar: { show: false } },
                series: statusSeries,
                labels: statusLabels,
                colors: ['#1BC5BD', '#F64E60', '#FFA800'],
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
                var elCat = document.getElementById('craft_category_chart'), elStatus = document.getElementById('craft_status_chart');
                if (elCat && elCat.offsetParent) { var c = new Apex(elCat, craftCategoryOptions); c.render(); }
                if (elStatus && elStatus.offsetParent) { var s = new Apex(elStatus, craftStatusOptions); s.render(); }
            }
            setTimeout(renderCharts, 250);
        });
    </script>
@endsection
