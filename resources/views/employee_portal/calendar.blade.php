@extends('layout.master')

@section('title', 'تقويم الحضور')
@section('page_title', 'تقويم الحضور الشخصي')
@section('sub_main', 'البوابة الشخصية')
@section('sub_title', 'تقويم الحضور')

@section('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <style>
        .fc-event { cursor: pointer; }
        .fc-toolbar-title { font-size: 1.5rem !important; font-weight: bold; }
        .fc { direction: rtl; text-align: right; }
        .fc .fc-toolbar.fc-header-toolbar { flex-wrap: wrap; }
        #kt_calendar { min-height: 520px; }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <x-page-header
            title="{{ __('تقويم الحضور الشخصي') }}"
            subtitle="{{ __('Monthly attendance records and check-in history') }}"
            icon="fa-calendar-alt"
            module="administrative"
        >
            <x-slot name="actions">
                <a href="{{ route('employee.portal') }}" class="btn btn-light-primary font-weight-bolder">
                    <i class="fa fa-arrow-right"></i> العودة للبوابة
                </a>
            </x-slot>
        </x-page-header>

        <div class="card card-custom gutter-b">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">
                        <i class="fa fa-calendar-check text-primary mr-2"></i>سجل الحضور الشهري
                    </span>
                    <span class="text-muted mt-2 font-weight-bold font-size-sm">
                        اضغط على أي يوم لعرض تفاصيل البصمة — البرتقالي يعني وجود تأخير
                    </span>
                </h3>
                <div class="card-toolbar">
                    <span class="label label-lg label-inline font-weight-bolder mr-2"
                        style="background:#C9F7F5;color:#0BB7AF;">
                        <i class="fa fa-check mr-1"></i> في الموعد
                    </span>
                    <span class="label label-lg label-inline font-weight-bolder"
                        style="background:#FFF4DE;color:#FFA800;">
                        <i class="fa fa-clock mr-1"></i> تأخير
                    </span>
                </div>
            </div>
            <div class="card-body pt-2">
                <div id="kt_calendar_loading" class="text-center py-6 text-muted font-weight-bold">
                    <span class="spinner spinner-primary mr-2"></span> جارٍ تحميل سجل الحضور...
                </div>
                <div id="kt_calendar"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/ar.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('kt_calendar');
            if (!calendarEl || typeof FullCalendar === 'undefined') {
                if (calendarEl) {
                    calendarEl.innerHTML =
                        '<div class="text-center py-10 text-muted font-weight-bold">تعذر تحميل التقويم</div>';
                }
                var failedLoading = document.getElementById('kt_calendar_loading');
                if (failedLoading) { failedLoading.style.display = 'none'; }
                return;
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'ar',
                direction: 'rtl',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                navLinks: true,
                dayMaxEvents: true,
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                events: function (fetchInfo, successCallback, failureCallback) {
                    fetch("{{ route('employee.attendance.data') }}?start=" + encodeURIComponent(fetchInfo.startStr) + "&end=" + encodeURIComponent(fetchInfo.endStr))
                        .then(function (res) {
                            if (!res.ok) { throw new Error('request failed'); }
                            return res.json();
                        })
                        .then(function (data) {
                            var loading = document.getElementById('kt_calendar_loading');
                            if (loading) { loading.style.display = 'none'; }
                            successCallback(data);
                        })
                        .catch(function () {
                            var loading = document.getElementById('kt_calendar_loading');
                            if (loading) {
                                loading.innerHTML =
                                    '<div class="alert alert-light-danger font-weight-bold mb-0">' +
                                    'تعذر تحميل بيانات الحضور ' +
                                    '<button type="button" class="btn btn-sm btn-danger mr-2" onclick="location.reload()">إعادة المحاولة</button>' +
                                    '</div>';
                            }
                            failureCallback();
                        });
                },
                eventDidMount: function (info) {
                    if (window.jQuery && jQuery.fn.popover) {
                        jQuery(info.el).popover({
                            title: info.event.title,
                            content: info.event.extendedProps.description,
                            trigger: 'hover',
                            placement: 'top',
                            container: 'body'
                        });
                    }
                }
            });
            calendar.render();
        });
    </script>
@endsection

