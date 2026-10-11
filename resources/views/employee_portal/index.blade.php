    @extends('layout.master')
    @section('title')
        {{ __('بوابة الموظف') }}
    @endsection

    @section('page_title')
        {{ __('controll_panel') }}
    @endsection
    @section('sub_main')
        {{ __('home') }}
    @endsection
    @section('sub_title')
        {{ __('البوابة الشخصية') }}
    @endsection

    @section('content')
        <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('البوابة الشخصية') }}"
                subtitle="{{ __('Employee portal and personal dashboard') }}"
                icon="fa-user-circle"
            >
                <x-slot name="actions">
                    <a href="{{ route('employee.calendar') }}" class="btn btn-primary font-weight-bolder mr-2">
                        <i class="fa fa-calendar-alt"></i> تقويم الحضور
                    </a>
                    <a href="{{ route('employee.requests') }}" class="btn btn-success font-weight-bolder">
                        <i class="fa fa-paper-plane"></i> طلباتي
                    </a>
                </x-slot>
            </x-page-header>

            <!-- بيانات الموظف الأساسية -->
            <div class="card card-custom gutter-b">
                <div class="card-body d-flex align-items-center py-5">
                    <div class="symbol symbol-60 symbol-circle mr-5">
                        <div class="symbol-label" style="background-image:url('{{ asset('assets/media/users/default.jpg') }}')">
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="#" class="text-dark font-weight-bold font-size-h4 mb-0">{{ $employee->name }}</a>
                        <span class="text-muted font-weight-bold">{{ $employee->job->name }} |
                            {{ $employee->department?->name ?? '   ' }}</span>
                    </div>
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="row">
                <div class="col-xl-3">
                    <div class="card card-custom bg-light-primary gutter-b" style="height: 150px">
                        <div class="card-body d-flex flex-column">
                            <span
                                class="card-title font-weight-bolder text-primary font-size-h2 mt-3">{{ $stats['total_holidays'] }}</span>
                            <span class="text-primary font-weight-bold">{{ __('الإجازات المقبولة') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card card-custom bg-light-success gutter-b" style="height: 150px">
                        <div class="card-body d-flex flex-column">
                            <span
                                class="card-title font-weight-bolder text-success font-size-h2 mt-3">{{ $stats['total_permissions'] }}</span>
                            <span class="text-success font-weight-bold">{{ __('الأذونات المقبولة') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card card-custom bg-light-danger gutter-b" style="height: 150px">
                        <div class="card-body d-flex flex-column">
                            <span
                                class="card-title font-weight-bolder text-danger font-size-h2 mt-3">{{ $stats['total_late_minutes'] }}</span>
                            <span class="text-danger font-weight-bold">{{ __('إجمالي دقائق التأخير') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card card-custom bg-light-info gutter-b" style="height: 150px">
                        <div class="card-body d-flex flex-column">
                            <span
                                class="card-title font-weight-bolder text-info font-size-h2 mt-3">{{ $stats['today_attendance'] ? Carbon\Carbon::parse($stats['today_attendance']->arrival_time)->format('H:i') : '--:--' }}</span>
                            <span class="text-info font-weight-bold">{{ __('بصمة اليوم') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الدخول للنظام -->
            <div class="row">
                <div class="col-xl-4">
                    <div class="card card-custom bg-light-warning gutter-b" style="height: 120px">
                        <div class="card-body d-flex flex-column">
                            <span class="card-title font-weight-bolder text-warning font-size-h2 mt-3">{{ $login_stats['month_logins'] }}</span>
                            <span class="text-warning font-weight-bold">{{ __('مرات الدخول هذا الشهر') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card card-custom bg-light-primary gutter-b" style="height: 120px">
                        <div class="card-body d-flex flex-column">
                            <span class="card-title font-weight-bolder text-primary font-size-h2 mt-3">{{ $login_stats['total_hours'] }}</span>
                            <span class="text-primary font-weight-bold">{{ __('إجمالي ساعات العمل') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card card-custom bg-light-success gutter-b" style="height: 120px">
                        <div class="card-body d-flex flex-column">
                            <span class="card-title font-weight-bolder text-success font-size-h3 mt-3">
                                {{ $login_stats['last_login'] ? Carbon\Carbon::parse($login_stats['last_login']->arrival_time)->format('Y-m-d H:i') : '--' }}
                            </span>
                            <span class="text-success font-weight-bold">{{ __('آخر دخول للنظام') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- الرسم البياني للتأخير -->
                <div class="col-lg-12">
                    <div class="card card-custom gutter-b">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">مخطط التأخير (آخر 15 تسجيل)</span>
                                <span class="text-muted mt-3 font-weight-bold font-size-sm">عرض عدد دقائق التأخير لكل يوم</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="kt_late_chart" style="height: 250px"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- سجل الحضور الأخير -->
                <div class="col-lg-6">
                    <div class="card card-custom gutter-b">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">آخر تسجيلات الحضور</span>
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            <div class="table-responsive">
                                <table class="table table-borderless table-vertical-center">
                                    <thead>
                                        <tr>
                                            <th class="p-0" style="width: 50px"></th>
                                            <th class="p-0" style="min-width: 150px"></th>
                                            <th class="p-0" style="min-width: 100px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recent_attendance as $log)
                                            <tr>
                                                <td class="pl-0 py-4">
                                                    <div class="symbol symbol-40 symbol-light-info mr-5">
                                                        <span class="symbol-label">
                                                            <i class="fa fa-clock text-info"></i>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="pl-0">
                                                    <a href="#"
                                                        class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ Carbon\Carbon::parse($log->arrival_time)->format('Y-m-d') }}</a>
                                                    <span
                                                        class="text-muted font-weight-bold d-block">{{ Carbon\Carbon::parse($log->arrival_time)->format('H:i:s') }}</span>
                                                </td>
                                                <td class="text-right">
                                                    @if ($log->late_minutes > 0)
                                                        <span class="label label-lg label-light-danger label-inline">تأخير:
                                                            {{ $log->late_minutes }} د</span>
                                                    @else
                                                        <span
                                                            class="label label-lg label-light-success label-inline">منتظم</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أفراد العائلة -->
                <div class="col-lg-6">
                    <div class="card card-custom gutter-b">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">أفراد العائلة</span>
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            <div class="table-responsive">
                                <table class="table table-borderless table-vertical-center">
                                    <tbody>
                                        @foreach ($employee->employeeDependents as $relative)
                                            <tr>
                                                <td class="pl-0 py-4">
                                                    <span
                                                        class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $relative->name }}</span>
                                                    <span class="text-muted font-weight-bold">{{ $relative->relation }}</span>
                                                </td>
                                                <td class="text-right">
                                                    <span class="text-muted font-weight-bold">{{ $relative->birth }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- سجل النشاطات -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom gutter-b">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">سجل النشاطات والعمليات</span>
                                <span class="text-muted mt-3 font-weight-bold font-size-sm">آخر 20 عملية قمت بها في النظام</span>
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            @if($activities->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-borderless table-vertical-center">
                                    <thead>
                                        <tr>
                                            <th class="p-0" style="width: 50px"></th>
                                            <th class="p-0" style="min-width: 150px">التاريخ</th>
                                            <th class="p-0" style="min-width: 200px">العملية</th>
                                            <th class="p-0" style="min-width: 200px">النموذج</th>
                                            <th class="p-0 text-right">التفاصيل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($activities as $activity)
                                            <tr>
                                                <td class="pl-0 py-4">
                                                    <div class="symbol symbol-40 symbol-light-{{ $activity['color'] }} mr-5">
                                                        <span class="symbol-label">
                                                            <i class="fa {{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="pl-0">
                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                        {{ Carbon\Carbon::parse($activity['created_at'])->format('Y-m-d') }}
                                                    </span>
                                                    <span class="text-muted font-weight-bold">
                                                        {{ Carbon\Carbon::parse($activity['created_at'])->format('H:i:s') }}
                                                    </span>
                                                </td>
                                                <td class="pl-0">
                                                    <span class="label label-lg label-light-{{ $activity['color'] }} label-inline font-weight-bold">
                                                        {{ $activity['event_name'] }}
                                                    </span>
                                                </td>
                                                <td class="pl-0">
                                                    <span class="text-dark-75 font-weight-bolder d-block">
                                                        {{ $activity['subject_name'] }}
                                                    </span>
                                                    @if($activity['subject_id'])
                                                    <span class="text-muted font-weight-bold">ID: {{ $activity['subject_id'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    <span class="text-muted font-weight-bold">
                                                        {{ __($activity['description'] ?? 'No description') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-10">
                                <i class="fa fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p class="text-muted font-weight-bold">لا توجد نشاطات مسجلة بعد</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="{{ asset('assets/plugins/custom/apexcharts/apexcharts.js') }}"></script>
        <script>
            var element = document.getElementById("kt_late_chart");
            if (element) {
                var options = {
                    series: [{
                        name: 'دقائق التأخير',
                        data: {!! json_encode($chart_data->pluck('late')) !!}
                    }],
                    chart: {
                        type: 'area',
                        height: 250,
                        toolbar: {
                            show: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        categories: {!! json_encode($chart_data->pluck('date')) !!},
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#7E8299'
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.3,
                            stops: [0, 90, 100]
                        }
                    },
                    colors: ['#F64E60']
                };

                var chart = new ApexCharts(element, options);
                chart.render();
            }
        </script>
    @endsection
