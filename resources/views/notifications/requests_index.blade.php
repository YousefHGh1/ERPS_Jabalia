@extends('layout.master')

@section('title')
    {{ __('الإشعارات') }}
@endsection

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
                subtitle="{{ __('عرض جميع الإشعارات المستلمة') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mark-all-read">
                                            <i class="fas fa-check"></i> {{ __('تعيين الكل كمقروء') }}
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">

            <div class="card-body pt-7">
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
                        <div class="alert-text">
                            {{ __('لديك') }} {{ auth()->user()->unreadNotifications->count() }} {{ __('إشعارات غير مقروءة') }}
                        </div>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center">
                        <thead>
                            <tr>
                                <th>{{ __('الإشعار') }}</th>
                                <th>{{ __('النوع') }}</th>
                                <th>{{ __('من') }}</th>
                                <th>{{ __('التاريخ') }}</th>
                                <th>{{ __('الحالة') }}</th>
                                <th>{{ __('إجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(auth()->user()->notifications->reverse() as $notification)
                                <tr class="{{ $notification->read_at ? '' : 'bg-light-warning' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(!$notification->read_at)
                                                <span class="symbol symbol-20 symbol-light-danger mr-3">
                                                    <span class="symbol-label"></span>
                                                </span>
                                            @endif
                                            <div>
                                                <a href="{{ $notification->link ?? '#' }}" class="text-dark-75 font-weight-bolder text-hover-primary">
                                                    {{ $notification->title ?? $notification->message ?? 'إشعار' }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $notification->message ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ ($notification->type ?? '') === 'request' ? 'info' : 'secondary' }}">
                                            {{ ($notification->type ?? '') === 'request' ? 'طلب' : ($notification->type ?? 'عام') }}
                                        </span>
                                    </td>
                                    <td>{{ __('النظام') }}</td>
                                    <td>{{ $notification->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        @if($notification->read_at)
                                            <span class="badge badge-success">{{ __('مقروء') }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ __('غير مقروء') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown dropdown-inline">
                                            <a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                @if(!$notification->read_at)
                                                    <a href="javascript:;" class="dropdown-item mark-read" data-id="{{ $notification->id }}">
                                                        <i class="fas fa-check text-success"></i> {{ __('تعيين كمقروء') }}
                                                    </a>
                                                @endif
                                                <a href="javascript:;" class="dropdown-item delete-notification" data-id="{{ $notification->id }}">
                                                    <i class="fas fa-trash text-danger"></i> {{ __('حذف') }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('لا توجد إشعارات') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center py-3">
                        <span class="text-muted">
                            {{ __('إجمالي الإشعارات') }}: {{ auth()->user()->notifications->count() }} |
                            {{ __('غير المقروءة') }}: {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.mark-read').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: '/notifications/' + id + '/mark-read',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('{{ __('حدث خطأ') }}');
                }
            });
        });

        $('.delete-notification').click(function() {
            var id = $(this).data('id');
            if(confirm('{{ __('هل أنت متأكد من حذف هذا الإشعار؟') }}')) {
                $.ajax({
                    url: '/notifications/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('{{ __('حدث خطأ أثناء الحذف') }}');
                    }
                });
            }
        });

        $('.mark-all-read').click(function() {
            $.ajax({
                url: '/notifications/mark-all-read',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('{{ __('حدث خطأ') }}');
                }
            });
        });
    });
</script>
@endpush
