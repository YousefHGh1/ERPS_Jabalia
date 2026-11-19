@extends('layout.master')

@section('title')
    {{ __('سجل الحركات') }} - {{ __('الطلب') }} #{{ $request->id }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الطلبات') }} / {{ __('سجل الحركات') }}
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Card -->
        <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
            <div class="card-body py-8">
                <div class="d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap">
                    <div class="d-flex align-items-center mr-5">
                        <div class="symbol symbol-60 symbol-light-primary mr-5">
                            <span class="symbol-label">
                                <i class="fas fa-history text-primary font-size-h3"></i>
                            </span>
                        </div>
                        <div>
                            <h2 class="font-weight-bolder text-dark font-size-h2 mb-2">
                                {{ __('سجل حركات الطلب') }} #{{ $request->id }}
                            </h2>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="text-muted font-weight-bold font-size-sm mr-4">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ $request->created_at->format('d-m-Y H:i') }}
                                </span>
                                <span class="badge badge-lg badge-{{ $request->status->color ?? 'secondary' }} font-weight-bold">
                                    {{ $request->status->name ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('requests.show', $request) }}" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="fas fa-arrow-right"></i> {{ __('عودة للطلب') }}
                        </a>
                        <a href="{{ route('requests.print', $request) }}" class="btn btn-light-success font-weight-bolder mr-2" target="_blank">
                            <i class="fas fa-print"></i> {{ __('طباعة') }}
                        </a>
                        <a href="{{ route('requests.index') }}" class="btn btn-light-danger font-weight-bolder">
                            <i class="fas fa-list"></i> {{ __('قائمة الطلبات') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Info Card -->
        <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
            <div class="card-header border-0 pt-7 pb-0">
                <h3 class="card-title font-weight-bolder text-dark">
                    <i class="fas fa-file-alt text-primary mr-2"></i>
                    {{ __('معلومات الطلب') }}
                </h3>
            </div>
            <div class="card-body pt-6">
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="info-item">
                            <label class="font-weight-bold text-muted mb-2">{{ __('نوع الطلب') }}</label>
                            <div class="info-value">{{ $request->requestType->name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="info-item">
                            <label class="font-weight-bold text-muted mb-2">{{ __('المكلف') }}</label>
                            <div class="info-value">{{ $request->mokalaf->name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="info-item">
                            <label class="font-weight-bold text-muted mb-2">{{ __('تاريخ الإنشاء') }}</label>
                            <div class="info-value" style="direction: ltr;">{{ $request->created_at->format('d-m-Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="info-item">
                            <label class="font-weight-bold text-muted mb-2">{{ __('أنشأه') }}</label>
                            <div class="info-value">{{ $request->creator->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Card -->
        <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
            <div class="card-header border-0 pt-7 pb-0">
                <h3 class="card-title font-weight-bolder text-dark">
                    <i class="fas fa-stream text-primary mr-2"></i>
                    {{ __('سجل الحركات الزمني') }}
                </h3>
            </div>
            <div class="card-body pt-6">
                <div class="timeline timeline-3">
                    @php
                        // دمج جميع الحركات في مصفوفة واحدة
                        $allEvents = collect();

                        // إضافة إنشاء الطلب
                        $allEvents->push([
                            'type' => 'created',
                            'date' => $request->created_at,
                            'user' => $request->creator->name ?? 'النظام',
                            'description' => 'تم إنشاء الطلب',
                            'details' => null,
                        ]);

                        // إضافة التعديلات من Activity Log
                        foreach ($activities as $activity) {
                            if ($activity->event !== 'created') {
                                $allEvents->push([
                                    'type' => $activity->event,
                                    'date' => $activity->created_at,
                                    'user' => $activity->causer->name ?? 'النظام',
                                    'description' => $activity->description,
                                    'details' => $activity->properties,
                                ]);
                            }
                        }

                        // إضافة الردود
                        foreach ($responses as $response) {
                            $allEvents->push([
                                'type' => 'response',
                                'date' => $response->responsed_at ?? $response->created_at,
                                'user' => $response->responsedBy->name ?? $response->createdBy->name ?? 'النظام',
                                'description' => 'رد على الطلب: ' . ($response->responseType->name ?? '-'),
                                'details' => [
                                    'section' => $response->section->name ?? '-',
                                    'department' => $response->department->name ?? '-',
                                    'notes' => $response->notes,
                                ],
                            ]);
                        }

                        // إضافة المدفوعات
                        foreach ($payments as $payment) {
                            $allEvents->push([
                                'type' => 'payment',
                                'date' => $payment->payment_date . ' ' . ($payment->created_at->format('H:i') ?? ''),
                                'user' => $payment->user->name ?? 'النظام',
                                'description' => 'دفعة: ' . number_format($payment->amount, 2) . ' - ' . ($payment->payment_method ?? ''),
                                'details' => [
                                    'receipt_number' => $payment->receipt_number,
                                    'notes' => $payment->notes,
                                ],
                            ]);
                        }

                        // ترتيب الأحداث حسب التاريخ
                        $allEvents = $allEvents->sortByDesc('date')->values();
                    @endphp

                    @forelse($allEvents as $event)
                        <div class="timeline-item">
                            <div class="timeline-label font-weight-bolder text-muted">
                                {{ $event['date']->format('d-m-Y H:i') ?? '-' }}
                            </div>
                            <div class="timeline-badge">
                                @if($event['type'] === 'created')
                                    <i class="fas fa-plus-circle text-success"></i>
                                @elseif($event['type'] === 'updated')
                                    <i class="fas fa-edit text-warning"></i>
                                @elseif($event['type'] === 'deleted')
                                    <i class="fas fa-trash text-danger"></i>
                                @elseif($event['type'] === 'response')
                                    <i class="fas fa-reply text-info"></i>
                                @elseif($event['type'] === 'payment')
                                    <i class="fas fa-money-bill-wave text-success"></i>
                                @else
                                    <i class="fas fa-circle text-secondary"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <div class="font-weight-bolder text-dark mb-1">
                                    {{ $event['description'] }}
                                </div>
                                <div class="text-muted font-size-sm mb-2">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $event['user'] }}
                                </div>
                                @if($event['details'])
                                    <div class="bg-light p-3 rounded">
                                        @if(is_array($event['details']))
                                            @foreach($event['details'] as $key => $value)
                                                @if($value && $value !== '-')
                                                    <div class="mb-1">
                                                        <strong>{{ $key }}:</strong> {{ $value }}
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div>{{ $event['details'] }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <i class="fas fa-history fa-3x text-muted mb-4"></i>
                            <p class="text-muted font-size-lg">{{ __('لا توجد حركات مسجلة لهذا الطلب') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Responses Card -->
        @if($responses->count() > 0)
            <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                <div class="card-header border-0 pt-7 pb-0">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fas fa-reply text-primary mr-2"></i>
                        {{ __('الردود على الطلب') }} ({{ $responses->count() }})
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="15%">{{ __('الجهة') }}</th>
                                    <th width="15%">{{ __('الموظف') }}</th>
                                    <th width="15%">{{ __('تاريخ الرد') }}</th>
                                    <th width="15%">{{ __('الحالة') }}</th>
                                    <th width="35%">{{ __('الملاحظات') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($responses as $response)
                                    @php
                                        $responseTypeId = $response->response_type;
                                        $badgeClass = match($responseTypeId) {
                                            1 => 'success',
                                            2 => 'warning',
                                            3 => 'danger',
                                            4 => 'info',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="font-weight-bolder text-dark">
                                                {{ $response->unit_name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($response->responsedBy)
                                                <span class="font-weight-bold text-primary">
                                                    {{ $response->responsedBy->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="direction: ltr;" class="text-right">
                                            @if($response->responsed_at)
                                                {{ \Carbon\Carbon::parse($response->responsed_at)->format('d-m-Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-lg badge-{{ $badgeClass }} font-weight-bold">
                                                {{ $response->responseType->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($response->notes && $response->notes != '-')
                                                <div class="text-dark-75 font-size-sm">
                                                    {{ $response->notes }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payments Card -->
        @if($payments->count() > 0)
            <div class="card card-custom gutter-b shadow-sm border-0">
                <div class="card-header border-0 pt-7 pb-0">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fas fa-money-bill-wave text-primary mr-2"></i>
                        {{ __('المدفوعات') }} ({{ $payments->count() }})
                    </h3>
                </div>
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="15%">{{ __('المستخدم') }}</th>
                                    <th width="15%">{{ __('تاريخ الدفع') }}</th>
                                    <th width="15%">{{ __('المبلغ') }}</th>
                                    <th width="15%">{{ __('طريقة الدفع') }}</th>
                                    <th width="15%">{{ __('رقم الإيصال') }}</th>
                                    <th width="20%">{{ __('ملاحظات') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            @if($payment->user)
                                                <span class="font-weight-bold text-primary">
                                                    {{ $payment->user->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="direction: ltr;" class="text-right">
                                            {{ $payment->payment_date->format('d-m-Y') }}
                                        </td>
                                        <td style="direction: ltr;" class="text-right font-weight-bolder">
                                            {{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td>{{ $payment->payment_method ?? '-' }}</td>
                                        <td>{{ $payment->receipt_number ?? '-' }}</td>
                                        <td>
                                            @if($payment->notes)
                                                <div class="text-dark-75 font-size-sm">{{ $payment->notes }}</div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
