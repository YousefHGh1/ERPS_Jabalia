@extends('layout.master')

@section('title', 'تقويم الحضور')
@section('page_title', 'تقويم الحضور الشخصي')

@section('css')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<style>
    .fc-event { cursor: pointer; }
    .fc-toolbar-title { font-size: 1.5rem !important; font-weight: bold; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="card card-custom gutter-b">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">سجل الحضور الشهري</h3>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('employee.portal') }}" class="btn btn-light-primary font-weight-bold">
                    <i class="fa fa-arrow-right"></i> العودة للبوابة
                </a>
            </div>
        </div>
        <div class="card-body">
            <div id="kt_calendar"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/ar.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('kt_calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ar',
            direction: 'rtl',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: "{{ route('employee.attendance.data') }}",
            eventDidMount: function(info) {
                $(info.el).popover({
                    title: info.event.title,
                    content: info.event.extendedProps.description,
                    trigger: 'hover',
                    placement: 'top',
                    container: 'body'
                });
            }
        });
        calendar.render();
    });
</script>
@endsection
