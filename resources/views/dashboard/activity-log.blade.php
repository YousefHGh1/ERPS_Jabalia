@extends('layout.master')

@section('title', 'سجل نشاط النظام')
@section('page_title', __('مركز التحكم التفاعلي'))
@section('sub_main', __('لوحة التحكم'))
@section('sub_title', 'سجل نشاط النظام')

@section('content')
<div class="container-fluid" dir="rtl">
    <x-page-header title="سجل نشاط النظام" subtitle="جميع الحركات المسجلة في النظام" icon="fa-history">
        <x-slot name="actions">
            <a href="{{ route('dashboard.realtime') }}" class="btn btn-light-primary font-weight-bold">
                <i class="fas fa-arrow-right ml-1"></i> العودة للوحة المتابعة
            </a>
        </x-slot>
    </x-page-header>

    <div class="card card-custom shadow-sm border-0">
        <div class="card-header border-0 py-5 d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bolder text-dark mb-0">الحركات المسجلة</h3>
            <span class="label label-light-primary label-inline">{{ number_format($activities->total()) }} حركة</span>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-hover table-vertical-center">
                    <thead>
                        <tr class="text-muted">
                            <th style="min-width:170px">التاريخ والوقت</th>
                            <th style="min-width:150px">المستخدم</th>
                            <th style="min-width:220px">الحركة</th>
                            <th style="min-width:180px">السجل المرتبط</th>
                            <th style="min-width:110px">المصدر</th>
                            <th style="min-width:100px">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            @php
                                $subjectLabel = $activity->subject_type
                                    ? \App\Services\ModelAuditLabels::get($activity->subject_type)
                                    : null;
                                $properties = $activity->properties?->toArray() ?? [];
                                $attributes = $properties['attributes'] ?? $properties;
                                $oldValues = $properties['old'] ?? [];
                                $sensitiveKeys = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];
                                $attributes = collect($attributes)->except($sensitiveKeys);
                                $oldValues = collect($oldValues)->except($sensitiveKeys);
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $activity->created_at?->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <span class="font-weight-bold text-dark">{{ $activity->causer?->name ?? 'النظام' }}</span>
                                </td>
                                <td>{{ $activity->description ?: 'حركة مسجلة' }}</td>
                                <td>
                                    @if($subjectLabel)
                                        <span class="label label-light-info label-inline">{{ $subjectLabel }}</span>
                                        @if($activity->subject_id)<span class="text-muted mr-1">#{{ $activity->subject_id }}</span>@endif
                                    @else
                                        <span class="text-muted">{{ $activity->log_name ?: 'عام' }}</span>
                                    @endif
                                </td>
                                <td><span class="text-muted">{{ $activity->log_name ?: 'النظام' }}</span></td>
                                <td>
                                    @if($attributes->isNotEmpty() || $oldValues->isNotEmpty())
                                        <details>
                                            <summary class="text-primary cursor-pointer">عرض التغييرات</summary>
                                            <div class="mt-2 small">
                                                @foreach($attributes as $key => $value)
                                                    <div class="mb-1"><strong>{{ $key }}:</strong> {{ is_scalar($value) || $value === null ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE) }}</div>
                                                @endforeach
                                                @if($oldValues->isNotEmpty())
                                                    <div class="text-muted mt-2">القيم السابقة</div>
                                                    @foreach($oldValues as $key => $value)
                                                        <div class="mb-1"><strong>{{ $key }}:</strong> {{ is_scalar($value) || $value === null ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE) }}</div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </details>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-10">لا توجد حركات مسجلة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($activities->hasPages())
                <div class="d-flex justify-content-center mt-5" dir="rtl">
                    {{ $activities->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
