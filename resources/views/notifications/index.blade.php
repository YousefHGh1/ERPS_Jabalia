@extends('layout.master')

@section('title', __('الإشعارات'))

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الإشعارات') }}
@endsection

@section('content')
<div class="container-fluid">
    {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
    <x-page-header
        title="{{ __('الإشعارات') }}"
        subtitle="{{ __('عرض وإدارة جميع إشعاراتك') }}"
        icon="fa-bell"
    >
        <x-slot name="actions">
            @if(($unreadCount ?? 0) > 0)
                <button type="button" class="btn btn-light-success font-weight-bold btn-sm mr-2" id="btn-mark-all-read">
                    <i class="fas fa-check icon-sm"></i> {{ __('تعيين الكل كمقروء') }}
                </button>
            @endif
            <a href="{{ route('notifications.index') }}" class="btn btn-light-primary font-weight-bold btn-sm">{{ __('الكل') }}</a>
        </x-slot>
    </x-page-header>

    <div class="card card-custom gutter-b shadow-sm">
        <div class="card-body pt-4">
            {{-- فلاتر --}}
            <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-2x mb-5" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ !request('filter') ? 'active' : '' }}" href="{{ route('notifications.index') }}">{{ __('الكل') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('filter') === 'unread' ? 'active' : '' }}" href="{{ route('notifications.index', ['filter' => 'unread']) }}">{{ __('غير المقروءة') }}</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">{{ __('حسب النوع') }}</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('notifications.index', ['filter' => 'request']) }}">{{ __('طلب') }}</a>
                        <a class="dropdown-item" href="{{ route('notifications.index', ['filter' => 'system']) }}">{{ __('النظام') }}</a>
                        <a class="dropdown-item" href="{{ route('notifications.index', ['filter' => 'holiday']) }}">{{ __('إجازة') }}</a>
                        <a class="dropdown-item" href="{{ route('notifications.index', ['filter' => 'permission']) }}">{{ __('إذن') }}</a>
                        <a class="dropdown-item" href="{{ route('notifications.index', ['filter' => 'attendance']) }}">{{ __('الحضور') }}</a>
                    </div>
                </li>
            </ul>

            @if(($unreadCount ?? 0) > 0 && !request('filter'))
                <div class="alert alert-light-info alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle icon-2x text-info mr-3"></i>
                    <div>
                        {{ __('لديك') }} <strong>{{ $unreadCount }}</strong> {{ __('إشعارات غير مقروءة') }}
                    </div>
                </div>
            @endif

            <div class="timeline timeline-3" id="notifications-list">
                <div class="timeline-items">
                    @forelse($notifications as $notification)
                        <div class="timeline-item notification-row {{ $notification->is_read ? '' : 'bg-light-warning' }}" data-id="{{ $notification->id }}">
                            <div class="timeline-media">
                                <i class="fa {{ $notification->icon }} {{ $notification->priority_color }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <a href="{{ $notification->link ?? '#' }}" class="text-dark-75 text-hover-primary font-weight-bold font-size-lg">
                                            {{ $notification->title }}
                                        </a>
                                        @if(!$notification->is_read)
                                            <span class="label label-light-danger label-inline font-weight-bold ml-2">{{ __('جديد') }}</span>
                                        @endif
                                        <span class="text-muted font-size-sm ml-2">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="dropdown dropdown-inline mt-2 mt-md-0">
                                        <a href="#" class="btn btn-sm btn-light-primary btn-icon" data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                            @if(!$notification->is_read)
                                                <a href="javascript:;" class="dropdown-item js-mark-read" data-id="{{ $notification->id }}">
                                                    <i class="fas fa-check text-success mr-2"></i> {{ __('تعيين كمقروء') }}
                                                </a>
                                            @endif
                                            <a href="javascript:;" class="dropdown-item js-delete-notification text-danger" data-id="{{ $notification->id }}">
                                                <i class="fas fa-trash mr-2"></i> {{ __('حذف') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-muted font-size-sm mb-0">{{ $notification->message }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <i class="fas fa-bell icon-5x text-muted"></i>
                            <p class="text-muted font-size-lg mt-3">{{ __('لا توجد إشعارات') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
<script>
(function() {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var urlMarkRead = '{{ url("/notifications") }}';
    var urlMarkAllRead = '{{ route("notifications.mark-all-read") }}';
    var urlDelete = '{{ url("/notifications") }}';

    function notify(msg, type) {
        if (typeof toastr !== 'undefined') toastr[type || 'success'](msg);
        else alert(msg);
    }

    function markRead(id) {
        fetch(urlMarkRead + '/' + id + '/mark-read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({})
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var row = document.querySelector('.notification-row[data-id="' + id + '"]');
                if (row) {
                    row.classList.remove('bg-light-warning');
                    var badge = row.querySelector('.label-light-danger');
                    if (badge) badge.remove();
                    var link = row.querySelector('.dropdown-item.js-mark-read');
                    if (link) link.closest('.dropdown-menu').querySelector('.dropdown-item.js-mark-read')?.remove();
                }
                notify('{{ __("تم تعيين الإشعار كمقروء") }}');
            }
        })
        .catch(function() { notify('{{ __("حدث خطأ") }}', 'error'); });
    }

    function deleteNotification(id) {
        fetch(urlDelete + '/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var row = document.querySelector('.notification-row[data-id="' + id + '"]');
                if (row) row.remove();
                notify('{{ __("تم حذف الإشعار") }}');
            }
        })
        .catch(function() { notify('{{ __("حدث خطأ أثناء الحذف") }}', 'error'); });
    }

    document.querySelectorAll('.js-mark-read').forEach(function(el) {
        el.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            if (id) markRead(id);
        });
    });

    document.querySelectorAll('.js-delete-notification').forEach(function(el) {
        el.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            if (id && confirm('{{ __("هل أنت متأكد من حذف هذا الإشعار؟") }}')) deleteNotification(id);
        });
    });

    var btnMarkAll = document.getElementById('btn-mark-all-read');
    if (btnMarkAll) {
        btnMarkAll.addEventListener('click', function() {
            fetch(urlMarkAllRead, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    document.querySelectorAll('.notification-row.bg-light-warning').forEach(function(row) {
                        row.classList.remove('bg-light-warning');
                        var b = row.querySelector('.label-light-danger');
                        if (b) b.remove();
                    });
                    var btn = document.getElementById('btn-mark-all-read');
                    if (btn) btn.remove();
                    notify('{{ __("تم تعيين الكل كمقروء") }}');
                }
            })
            .catch(function() { notify('{{ __("حدث خطأ") }}', 'error'); });
        });
    }
})();
</script>
@endpush
