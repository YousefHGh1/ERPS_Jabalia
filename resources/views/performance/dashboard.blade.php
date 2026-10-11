@extends('layout.master')

@section('title')
{{ __('Performance Monitoring') }}
@endsection

@section('page_title')
{{ __('controll_panel') }}
@endsection

@section('sub_main')
{{ __('home') }}
@endsection

@section('sub_title')
{{ __('Performance Monitoring') }}
@endsection

@section('content')
    <div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('Performance Monitoring') }}"
            subtitle="{{ __('Monitor system performance, database, cache, and server metrics') }}"
        />

        @cannot('monitor performance')
            <div class="alert alert-warning" role="alert">
                {{ __('ليس لديك صلاحية كافية لعرض لوحة مراقبة الأداء.') }}
            </div>
        @endcannot

        @can('monitor performance')
        <!-- Quick Stats -->
        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Server Load')"
                    :value="'--'"
                    icon="fa-server"
                    color="primary"
                    :link="route('performance.metrics')"
                    :linkText="__('View Details')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Memory Usage')"
                    :value="'--'"
                    icon="fa-memory"
                    color="warning"
                    :link="route('performance.metrics')"
                    :linkText="__('View Details')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Database Size')"
                    :value="'--'"
                    icon="fa-database"
                    color="success"
                    :link="route('performance.metrics')"
                    :linkText="__('View Details')"
                />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-admin.stat-card
                    :title="__('Cache Hit Rate')"
                    :value="'--'"
                    icon="fa-bolt"
                    color="info"
                    :link="route('performance.metrics')"
                    :linkText="__('View Details')"
                />
            </div>
        </div>

        <div class="row">
            <!-- Charts Section -->
            <div class="col-lg-8">
                <!-- Memory Usage Chart -->
                <div class="card card-custom shadow-sm gutter-b">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Memory Usage Over Time') }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <button class="btn btn-light-primary btn-sm font-weight-bolder" onclick="refreshMetrics()">
                                <i class="fas fa-sync-alt mr-2"></i>{{ __('Refresh') }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="min-height: 380px;">
                        <div id="memory_chart" class="admin-chart-container" style="height: 350px;"></div>
                    </div>
                </div>

                <!-- Database Performance -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Database Performance') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive admin-table">
                            <table class="table table-head-custom table-vertical-center table-borderless">
                                <thead>
                                    <tr class="text-left text-muted font-weight-bolder">
                                        <th class="pl-0">{{ __('Metric') }}</th>
                                        <th class="text-center">{{ __('Value') }}</th>
                                        <th class="text-right">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="db_metrics_table">
                                    <tr>
                                        <td class="pl-0 py-5">
                                            <span class="text-dark-75 font-weight-bolder">{{ __('Connections') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-dark-75 font-weight-bolder" id="db_connections">--</span>
                                        </td>
                                        <td class="text-right pr-0">
                                            <span class="label label-lg label-light-success label-inline font-weight-bold">{{ __('Good') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 py-5">
                                            <span class="text-dark-75 font-weight-bolder">{{ __('Slow Queries') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-dark-75 font-weight-bolder" id="slow_queries">--</span>
                                        </td>
                                        <td class="text-right pr-0">
                                            <span class="label label-lg label-light-success label-inline font-weight-bold" id="slow_queries_status">{{ __('Good') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 py-5">
                                            <span class="text-dark-75 font-weight-bolder">{{ __('Tables Count') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-dark-75 font-weight-bolder" id="tables_count">--</span>
                                        </td>
                                        <td class="text-right pr-0">
                                            <span class="label label-lg label-light-info label-inline font-weight-bold">{{ __('Normal') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pl-0 py-5">
                                            <span class="text-dark-75 font-weight-bolder">{{ __('Total Size') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-dark-75 font-weight-bolder" id="db_size">--</span>
                                        </td>
                                        <td class="text-right pr-0">
                                            <span class="label label-lg label-light-info label-inline font-weight-bold">{{ __('Normal') }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Panels -->
            <div class="col-lg-4">
                <!-- System Health -->
                <div class="card card-custom shadow-sm gutter-b">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('System Health') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-dark-75 font-weight-bolder">{{ __('Disk Usage') }}</span>
                                <span class="text-dark-75 font-weight-bolder" id="disk_usage">--</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" id="disk_progress" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-dark-75 font-weight-bolder">{{ __('Memory Usage') }}</span>
                                <span class="text-dark-75 font-weight-bolder" id="memory_usage">--</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" id="memory_progress" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-dark-75 font-weight-bolder">{{ __('Cache Hit Rate') }}</span>
                                <span class="text-dark-75 font-weight-bolder" id="cache_hit_rate">--</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" id="cache_progress" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('Quick Actions') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="row">
                            <div class="col-6 mb-4">
                                <button onclick="clearCache()" class="admin-quick-action-card w-100 border-0 bg-transparent">
                                    <i class="fa fa-broom text-info"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Clear Cache') }}</span>
                                </button>
                            </div>
                            <div class="col-6 mb-4">
                                <button onclick="generateReport()" class="admin-quick-action-card w-100 border-0 bg-transparent">
                                    <i class="fa fa-file-pdf text-success"></i>
                                    <span class="font-weight-bolder text-dark-75">{{ __('Generate Report') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <div class="card card-custom shadow-sm gutter-b mt-5">
                    <div class="card-header border-0 pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">{{ __('System Alerts') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5" id="alerts_container">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <p>{{ __('No alerts') }}</p>
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
            loadMetrics();
        });

        function loadMetrics() {
            $.get('{{ route("performance.metrics") }}')
                .done(function(data) {
                    updateSystemStats(data);
                    updateDatabaseStats(data.database);
                    updateMemoryChart(data.memory);
                    updateSystemHealth(data);
                    updateAlerts(data.alerts);
                })
                .fail(function() {
                    console.error('Failed to load performance metrics');
                });
        }

        function updateSystemStats(data) {
            if (data.system) {
                // Update stat cards would be done here
            }
        }

        function updateDatabaseStats(db) {
            if (db.error) {
                console.error('Database metrics error:', db.error);
                return;
            }

            $('#db_connections').text(db.connections || '--');
            $('#slow_queries').text(db.slow_queries || '--');
            $('#tables_count').text(db.tables_count || '--');
            $('#db_size').text(formatBytes(db.total_size || 0));

            // Update slow queries status
            const slowStatus = db.slow_queries > 10 ? 'danger' : 'success';
            const slowText = db.slow_queries > 10 ? '{{ __('High') }}' : '{{ __('Good') }}';
            $('#slow_queries_status')
                .removeClass('label-light-success label-light-danger')
                .addClass('label-light-' + slowStatus)
                .text(slowText);
        }

        function updateMemoryChart(memory) {
            if (!memory) return;

            const memoryPercent = memory.usage_percentage || 0;
            const peakPercent = memory.peak_usage ? (memory.peak_usage / memory.limit * 100) : 0;

            const options = {
                chart: {
                    type: 'area',
                    height: 350,
                    fontFamily: 'Cairo, sans-serif',
                    toolbar: { show: false }
                },
                series: [{
                    name: '{{ __("Current Usage") }}',
                    data: [memoryPercent]
                }, {
                    name: '{{ __("Peak Usage") }}',
                    data: [peakPercent]
                }],
                xaxis: {
                    categories: ['{{ __("Now") }}']
                },
                colors: ['#FFA800', '#F64E60'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth' },
                yaxis: {
                    labels: {
                        formatter: function (val) { return val + '%'; }
                    }
                }
            };

            const chart = new ApexCharts(document.getElementById('memory_chart'), options);
            chart.render();
        }

        function updateSystemHealth(data) {
            if (data.system && data.system.disk_usage) {
                const diskPercent = data.system.disk_usage.usage_percentage || 0;
                $('#disk_usage').text(diskPercent + '%');
                $('#disk_progress').css('width', diskPercent + '%')
                    .removeClass('bg-primary bg-warning bg-danger')
                    .addClass(diskPercent > 85 ? 'bg-danger' : (diskPercent > 70 ? 'bg-warning' : 'bg-primary'));
            }

            if (data.memory) {
                const memPercent = data.memory.usage_percentage || 0;
                $('#memory_usage').text(memPercent + '%');
                $('#memory_progress').css('width', memPercent + '%')
                    .removeClass('bg-primary bg-warning bg-danger')
                    .addClass(memPercent > 80 ? 'bg-danger' : (memPercent > 60 ? 'bg-warning' : 'bg-primary'));
            }

            if (data.cache) {
                const cacheRate = data.cache.hit_rate || 0;
                $('#cache_hit_rate').text(cacheRate + '%');
                $('#cache_progress').css('width', cacheRate + '%')
                    .removeClass('bg-primary bg-warning bg-danger')
                    .addClass(cacheRate < 70 ? 'bg-warning' : 'bg-success');
            }
        }

        function updateAlerts(alerts) {
            const container = $('#alerts_container');
            
            if (!alerts || alerts.length === 0) {
                container.html(`
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                        <p>{{ __('No alerts') }}</p>
                    </div>
                `);
                return;
            }

            const alertsHtml = alerts.map(alert => `
                <div class="alert alert-${alert.type === 'critical' ? 'danger' : 'warning'} d-flex align-items-center mb-3">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <div>
                        <strong>${alert.message}</strong>
                        <span class="badge badge-${alert.type === 'critical' ? 'danger' : 'warning'} mr-2">${alert.value}</span>
                    </div>
                </div>
            `).join('');

            container.html(alertsHtml);
        }

        function refreshMetrics() {
            const btn = event.target;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Loading...') }}';
            btn.disabled = true;

            loadMetrics();

            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 1000);
        }

        function clearCache() {
            if (!confirm('{{ __('Are you sure you want to clear the performance cache?') }}')) {
                return;
            }

            $.post('{{ route("performance.clear-cache") }}')
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('Success') }}',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadMetrics();
                    }
                })
                .fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('Error') }}',
                        text: '{{ __('Failed to clear cache') }}'
                    });
                });
        }

        function generateReport() {
            window.open('{{ route("performance.report") }}', '_blank');
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    </script>
@endsection
