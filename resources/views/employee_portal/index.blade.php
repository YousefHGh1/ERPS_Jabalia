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
                module="administrative"
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

            {{-- بطاقة تعريف الموظف — Hero --}}
            <div class="card card-custom gutter-b portal-hero-card">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center py-6">
                    @php
                        $portalUser = $employee->user;
                        $portalInitial = mb_substr($employee->name ?? '؟', 0, 1);
                        $portalWorkRatio = ($stats['attendance_rate'] ?? 0) >= 90 ? 'success' : ((($stats['attendance_rate'] ?? 0) >= 70) ? 'warning' : 'danger');
                    @endphp
                    <div class="symbol symbol-70 symbol-circle symbol-xl-85 mr-0 mr-md-6 mb-4 mb-md-0 portal-hero-avatar">
                        @if($portalUser && $portalUser->avatar)
                            <div class="symbol-label"
                                style="background-image:url('{{ $portalUser->avatar }}'); background-size: cover; background-position: center;">
                            </div>
                        @else
                            <div class="symbol-label bg-light-primary">
                                <span class="font-size-h2 font-weight-bolder text-primary">{{ $portalInitial }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="d-flex flex-column flex-grow-1">
                        <span class="text-dark font-weight-bolder font-size-h3 mb-1">{{ $employee->name }}</span>
                        <span class="text-muted font-weight-bold font-size-lg mb-2">{{ $employee->job->name ?? '---' }}</span>
                        <div class="d-flex flex-wrap" style="gap: 6px;">
                            <span class="label label-lg label-light-primary label-inline font-weight-bolder">
                                <i class="fa fa-building mr-1"></i>
                                {{ $employee->department?->name ?? 'غير محدد' }}
                            </span>
                            @if($employee->section?->name)
                                <span class="label label-lg label-light-info label-inline font-weight-bolder">
                                    <i class="fa fa-sitemap mr-1"></i>
                                    {{ $employee->section->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-column mt-4 mt-md-0 text-md-left" style="gap: 8px;">
                        <span class="text-muted font-weight-bold font-size-sm">
                            <i class="fa fa-id-badge text-primary mr-1"></i>
                            الرقم الوظيفي:
                            <span class="text-dark-75 font-weight-bolder" dir="ltr">{{ $employee->emp_number ?? $employee->czn_id ?? '---' }}</span>
                        </span>
                        <span class="label label-lg label-light-{{ $portalWorkRatio }} label-inline font-weight-bolder">
                            <i class="fa fa-chart-line mr-1"></i>
                            الالتزام: {{ number_format($stats['attendance_rate'] ?? 0, 1) }}%
                        </span>
                    </div>
                </div>
                <div class="card-footer d-flex flex-column flex-md-row" style="gap: 10px;">
                    <a href="{{ route('employee.calendar') }}" class="portal-quick-link flex-fill">
                        <span class="portal-quick-icon" style="background: linear-gradient(135deg, #3699FF, #1BC5BD);">
                            <i class="fa fa-calendar-alt"></i>
                        </span>
                        تقويم الحضور الشهري
                    </a>
                    <a href="{{ route('employee.requests') }}" class="portal-quick-link flex-fill">
                        <span class="portal-quick-icon" style="background: linear-gradient(135deg, #8950FC, #E4E6EF);">
                            <i class="fa fa-paper-plane"></i>
                        </span>
                        طلباتي (إجازات وأذونات)
                    </a>
                </div>
            </div>

            {{-- STATS_NEW --}}
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-5">
                    <x-admin.stat-card
                        title="أيام الحضور"
                        :value="$stats['attendance_days']"
                        icon="fa-calendar-check"
                        color="success"
                        :link="route('employee.calendar')"
                        linkText="عرض التقويم"
                    />
                </div>
                <div class="col-xl-3 col-md-6 mb-5">
                    <x-admin.stat-card
                        title="أيام الغياب"
                        :value="$stats['absence_days']"
                        icon="fa-calendar-times"
                        color="danger"
                        :link="route('employee.calendar')"
                        linkText="عرض التقويم"
                    />
                </div>
                <div class="col-xl-3 col-md-6 mb-5">
                    <x-admin.stat-card
                        title="نسبة الحضور"
                        :value="$stats['attendance_rate']"
                        :decimals="1"
                        suffix="%"
                        icon="fa-chart-line"
                        color="primary"
                        :link="route('employee.calendar')"
                        linkText="سجل الحضور"
                    />
                </div>
                <div class="col-xl-3 col-md-6 mb-5">
                    <x-admin.stat-card
                        title="إجمالي دقائق التأخير"
                        :value="$stats['total_late_minutes']"
                        icon="fa-clock"
                        color="warning"
                        :link="route('employee.calendar')"
                        linkText="تفاصيل التأخير"
                    />
                </div>
            </div>


            {{-- إحصائيات الدخول للنظام --}}
            <div class="row">
                <div class="col-xl-4 col-md-6 mb-5">
                    <x-admin.stat-card
                        title="مرات الدخول هذا الشهر"
                        :value="$login_stats['month_logins']"
                        icon="fa-sign-in-alt"
                        color="warning"
                        link="#"
                        linkText=""
                        :border="false"
                    />
                </div>
                <div class="col-xl-4 col-md-6 mb-5">
                    <x-admin.stat-card
                        title="إجمالي ساعات العمل"
                        :value="$login_stats['total_hours']"
                        icon="fa-business-time"
                        color="primary"
                        link="#"
                        linkText=""
                        :border="false"
                    />
                </div>
                <div class="col-xl-4 col-md-6 mb-5">
                    <x-admin.stat-card
                        title="آخر دخول للنظام"
                        :value="$login_stats['last_login'] ? \Carbon\Carbon::parse($login_stats['last_login']->arrival_time)->format('Y-m-d H:i') : '--'"
                        icon="fa-user-clock"
                        color="success"
                        link="#"
                        linkText=""
                        :border="false"
                    />
                </div>
            </div>

            {{-- مخطط التأخير --}}
            <div class="row">
                <div class="col-12 mb-5">
                    <div class="card card-custom gutter-b">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">
                                    <i class="fa fa-chart-line text-danger mr-2"></i>مخطط التأخير (آخر 15 تسجيل)
                                </span>
                                <span class="text-muted mt-2 font-weight-bold font-size-sm">عرض عدد دقائق التأخير لكل يوم</span>
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            <div class="d-flex flex-wrap mb-4" style="gap: 8px;">
                                <span class="label label-lg label-light-danger label-inline font-weight-bolder">مجموع التأخير: {{ $stats['total_late_minutes'] }} دقيقة</span>
                                <span class="label label-lg label-light-warning label-inline font-weight-bolder">متوسط اليوم: {{ number_format($chart_data->avg('late') ?? 0, 1) }} دقيقة</span>
                                <span class="label label-lg label-light-info label-inline font-weight-bolder">أسوأ يوم: {{ $chart_data->max('late') ?? 0 }} دقيقة</span>
                            </div>
                            <div id="kt_late_chart" style="height: 280px"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- سجل الحضور الأخير --}}
                <div class="col-lg-6 mb-5">
                    <div class="card card-custom gutter-b h-100">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">
                                    <i class="fa fa-history text-primary mr-2"></i>آخر تسجيلات الحضور
                                </span>
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            @if($recent_attendance->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-borderless table-vertical-center portal-table">
                                        <thead>
                                            <tr class="text-muted text-uppercase">
                                                <th class="p-0" style="width: 50px"></th>
                                                <th class="p-0" style="min-width: 150px">{{ __('التاريخ والوقت') }}</th>
                                                <th class="p-0 text-right" style="min-width: 110px">{{ __('الحالة') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recent_attendance as $log)
                                                <tr>
                                                    <td class="pl-0 py-4">
                                                        <div class="symbol symbol-40 symbol-light-info mr-3">
                                                            <span class="symbol-label">
                                                                <i class="fa fa-clock text-info"></i>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="pl-0">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg" dir="ltr">
                                                            {{ Carbon\Carbon::parse($log->arrival_time)->format('Y-m-d') }}
                                                        </span>
                                                        <span class="text-muted font-weight-bold d-block" dir="ltr">
                                                            {{ Carbon\Carbon::parse($log->arrival_time)->format('H:i:s') }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        @if ($log->late_minutes > 0)
                                                            <span class="label label-lg label-light-danger label-inline font-weight-bold">
                                                                <span class="portal-status-dot is-late mr-1"></span>تأخير: {{ $log->late_minutes }} د
                                                            </span>
                                                        @else
                                                            <span class="label label-lg label-light-success label-inline font-weight-bold"><span class="portal-status-dot is-present mr-1"></span>منتظم</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <i class="fa fa-clock fa-3x text-muted mb-4"></i>
                                    <p class="text-muted font-weight-bold mb-3">لا توجد تسجيلات حضور</p>
                                    <a href="{{ route('employee.calendar') }}" class="btn btn-sm btn-light-primary font-weight-bolder">
                                        <i class="fa fa-calendar-alt mr-1"></i> عرض تقويم الحضور
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- أفراد العائلة --}}
                <div class="col-lg-6 mb-5">
                    <div class="card card-custom gutter-b h-100">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">
                                    <i class="fa fa-users text-success mr-2"></i>أفراد العائلة
                                </span>
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            @if($employee->employeeDependents->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-borderless table-vertical-center portal-table">
                                        <thead>
                                            <tr class="text-muted text-uppercase">
                                                <th class="p-0" style="min-width: 150px">{{ __('الاسم والصلة') }}</th>
                                                <th class="p-0 text-right" style="min-width: 120px">{{ __('تاريخ الميلاد') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($employee->employeeDependents as $relative)
                                                <tr>
                                                    <td class="pl-0 py-4">
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $relative->name }}</span>
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
                            @else
                                <div class="text-center py-10">
                                    <i class="fa fa-users fa-3x text-muted mb-4"></i>
                                    <p class="text-muted font-weight-bold mb-0">لا يوجد أفراد عائلة مسجلون</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- سجل النشاطات --}}
            <div class="row">
                <div class="col-12 mb-5">
                    <div class="card card-custom gutter-b">
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">
                                    <i class="fa fa-tasks text-info mr-2"></i>سجل النشاطات والعمليات
                                </span>
                                <span class="text-muted mt-2 font-weight-bold font-size-sm">آخر 20 عملية قمت بها في النظام</span>
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            @if($activities->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-borderless table-vertical-center portal-table">
                                    <thead>
                                        <tr class="text-muted text-uppercase">
                                            <th class="p-0" style="width: 50px"></th>
                                            <th class="p-0" style="min-width: 130px">{{ __('التاريخ') }}</th>
                                            <th class="p-0" style="min-width: 120px">{{ __('العملية') }}</th>
                                            <th class="p-0" style="min-width: 180px">{{ __('النموذج') }}</th>
                                            <th class="p-0 text-right">{{ __('التفاصيل') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($activities as $activity)
                                            <tr>
                                                <td class="pl-0 py-4">
                                                    <div class="symbol symbol-40 symbol-light-{{ $activity['color'] }} mr-3">
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
                                <i class="fa fa-clipboard-list fa-3x text-muted mb-4"></i>
                                <p class="text-muted font-weight-bold mb-0">لا توجد نشاطات مسجلة بعد — تظهر هنا آخر عملياتك في النظام</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script>
            (function () {
                var element = document.getElementById("kt_late_chart");
                if (!element) {
                    return;
                }

                var renderLateChart = function () {
                    var Apex = window.ApexCharts || (typeof ApexCharts !== 'undefined' ? ApexCharts : null);
                    if (!Apex) {
                        element.innerHTML =
                            '<div class="d-flex align-items-center justify-content-center h-100 text-muted font-weight-bold">تعذر تحميل المخطط البياني</div>';
                        return;
                    }

                    var options = {
                        series: [{
                            name: 'دقائق التأخير',
                            data: {!! json_encode($chart_data->pluck('late')) !!}
                        }],
                        chart: {
                            type: 'area',
                            height: 280,
                            fontFamily: 'Cairo, sans-serif',
                            toolbar: { show: false }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 3 },
                        xaxis: {
                            categories: {!! json_encode($chart_data->pluck('date')) !!},
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: {
                            labels: { style: { colors: '#7E8299' } }
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
                        colors: ['#F64E60'],
                        noData: { text: 'لا توجد بيانات لعرضها', align: 'center', verticalAlign: 'middle' },
                        tooltip: { theme: 'light', y: { title: { formatter: function () { return 'دقائق التأخير:'; } } } }
                    };

                    var chart = new Apex(element, options);
                    chart.render();
                };

                if (window.ApexCharts) {
                    renderLateChart();
                } else {
                    window.addEventListener('load', renderLateChart);
                }
            })();
        </script>
    @endsection
