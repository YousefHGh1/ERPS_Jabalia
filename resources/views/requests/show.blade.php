@extends('layout.master')

@section('title')
    {{ __('تفاصيل الطلب') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الطلبات') }} / {{ __('تفاصيل الطلب') }}
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Card with Stats -->
        <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
            <div class="card-body py-8">
                <div class="d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap">
                    <div class="d-flex align-items-center mr-5">
                        <div class="symbol symbol-60 symbol-light-primary mr-5">
                            <span class="symbol-label">
                                <i class="fas fa-file-alt text-primary font-size-h3"></i>
                            </span>
                        </div>
                        <div>
                            <h2 class="font-weight-bolder text-dark font-size-h2 mb-2">
                                {{ __('تفاصيل الطلب') }} #{{ $request->id }}
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
                        <a href="{{ route('requests.print', $request) }}" class="btn btn-light-primary font-weight-bolder mr-2"
                            target="_blank">
                            <i class="fas fa-print"></i> {{ __('طباعة') }}
                        </a>
                        <a href="{{ route('requests.index') }}" class="btn btn-light-danger font-weight-bolder mr-2">
                            <i class="fas fa-arrow-right"></i> {{ __('عودة') }}
                        </a>
                        @can('update requests')
                            <a href="{{ route('requests.edit', $request) }}" class="btn btn-primary font-weight-bolder">
                                <i class="fas fa-pen"></i> {{ __('تعديل') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-8">
            <div class="col-xl-3 col-lg-6">
                <div class="card card-custom bg-light-primary border-0 mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50 symbol-light-primary mr-5">
                                <span class="symbol-label">
                                    <i class="fas fa-calendar text-primary font-size-h3"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-weight-bolder text-dark font-size-h5 mb-1">{{ __('تاريخ الإنشاء') }}</div>
                                <div class="text-muted font-weight-bold font-size-sm">
                                    {{ $request->created_at->format('d-m-Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card card-custom bg-light-success border-0 mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50 symbol-light-success mr-5">
                                <span class="symbol-label">
                                    <i class="fas fa-tag text-success font-size-h3"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-weight-bolder text-dark font-size-h5 mb-1">{{ __('نوع الطلب') }}</div>
                                <div class="text-muted font-weight-bold font-size-sm">
                                    {{ $request->requestType->name ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card card-custom bg-light-warning border-0 mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50 symbol-light-warning mr-5">
                                <span class="symbol-label">
                                    <i class="fas fa-user text-warning font-size-h3"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-weight-bolder text-dark font-size-h5 mb-1">{{ __('المكلف') }}</div>
                                <div class="text-muted font-weight-bold font-size-sm">
                                    {{ $request->mokalaf->name ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card card-custom bg-light-danger border-0 mb-8">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50 symbol-light-danger mr-5">
                                <span class="symbol-label">
                                    <i class="fas fa-clock text-danger font-size-h3"></i>
                                </span>
                            </div>
                            <div>
                                <div class="font-weight-bolder text-dark font-size-h5 mb-1">{{ __('الموعد النهائي') }}</div>
                                <div class="text-muted font-weight-bold font-size-sm">
                                    {{ $request->deadline ? $request->deadline->format('d-m-Y') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Main Information -->
            <div class="col-xl-8">
                <!-- Request Text Card -->
                <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                    <div class="card-header border-0 pt-7 pb-0">
                        <h3 class="card-title font-weight-bolder text-dark">
                            <i class="fas fa-file-alt text-primary mr-2"></i>
                            {{ __('نص الطلب') }}
                        </h3>
                    </div>
                    <div class="card-body pt-6">
                        <div class="bg-light-primary p-6 rounded border-left-primary border-left-3">
                            <p class="font-size-lg text-dark-75 mb-0" style="line-height: 1.8;">
                                {!! nl2br(e($request->text)) !!}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Detailed Address Card -->
                @if (
                    $request->jebaia_no ||
                    $request->neighborhood ||
                    $request->widget ||
                    $request->voucher ||
                    $request->divider ||
                    $request->building ||
                    $request->floor ||
                    $request->apartment ||
                    $request->owner ||
                    $request->tenant)
                    <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                        <div class="card-header border-0 pt-7 pb-0">
                            <h3 class="card-title font-weight-bolder text-dark">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                {{ __('العنوان التفصيلي') }}
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            <div class="row">
                                @if ($request->neighborhood)
                                    <div class="col-md-4 mb-4">
                                        <div class="info-item">
                                            <label class="font-weight-bold text-muted mb-2">{{ __('الحي') }}</label>
                                            <div class="info-value">{{ $request->neighborhood }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if ($request->widget)
                                    <div class="col-md-4 mb-4">
                                        <div class="info-item">
                                            <label class="font-weight-bold text-muted mb-2">{{ __('القطعة') }}</label>
                                            <div class="info-value">{{ $request->widget }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if ($request->voucher)
                                    <div class="col-md-4 mb-4">
                                        <div class="info-item">
                                            <label class="font-weight-bold text-muted mb-2">{{ __('القسيمة') }}</label>
                                            <div class="info-value">{{ $request->voucher }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if ($request->divider)
                                    <div class="col-md-4 mb-4">
                                        <div class="info-item">
                                            <label class="font-weight-bold text-muted mb-2">{{ __('المقسم') }}</label>
                                            <div class="info-value">{{ $request->divider }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if ($request->building)
                                    <div class="col-md-4 mb-4">
                                        <div class="info-item">
                                            <label class="font-weight-bold text-muted mb-2">{{ __('البناية') }}</label>
                                            <div class="info-value">{{ $request->building }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if ($request->floor)
                                    <div class="col-md-4 mb-4">
                                        <div class="info-item">
                                            <label class="font-weight-bold text-muted mb-2">{{ __('الطابق') }}</label>
                                            <div class="info-value">{{ $request->floor }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if ($request->apartment)
                                    <div class="col-md-4 mb-4">
                                        <div class="info-item">
                                            <label class="font-weight-bold text-muted mb-2">{{ __('الشقة') }}</label>
                                            <div class="info-value">{{ $request->apartment }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if ($request->owner)
                                    <div class="col-md-4 mb-4">
                                        <div class="info-item">
                                            <label class="font-weight-bold text-muted mb-2">{{ __('المالك') }}</label>
                                            <div class="info-value">{{ $request->owner }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if ($request->tenant)
                                    <div class="col-md-4 mb-4">
                                        <div class="info-item">
                                            <label class="font-weight-bold text-muted mb-2">{{ __('المستأجر') }}</label>
                                            <div class="info-value">{{ $request->tenant }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Mokalaf Card -->
                @if ($request->mokalaf)
                    <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                        <div class="card-header border-0 pt-7 pb-0">
                            <h3 class="card-title font-weight-bolder text-dark">
                                <i class="fas fa-user text-primary mr-2"></i>
                                {{ __('معلومات المكلف') }}
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="info-item">
                                        <label class="font-weight-bold text-muted mb-2">{{ __('اسم المكلف') }}</label>
                                        <div class="info-value">
                                            @can('view', $request->mokalaf)
                                                <a href="{{ route('mokalafs.show', $request->mokalaf) }}"
                                                    class="text-primary font-weight-bolder">
                                                    {{ $request->mokalaf->name }}
                                                </a>
                                            @else
                                                {{ $request->mokalaf->name }}
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="info-item">
                                        <label class="font-weight-bold text-muted mb-2">{{ __('رقم الهوية') }}</label>
                                        <div class="info-value">{{ $request->mokalaf->identity_number ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="info-item">
                                        <label class="font-weight-bold text-muted mb-2">{{ __('رقم الموبايل') }}</label>
                                        <div class="info-value">{{ $request->mobile ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="info-item">
                                        <label class="font-weight-bold text-muted mb-2">{{ __('العنوان') }}</label>
                                        <div class="info-value">{{ $request->address ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Request Responses Card -->
                @if ($request->responses && $request->responses->count() > 0)
                    <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                        <div class="card-header border-0 pt-7 pb-0">
                            <h3 class="card-title font-weight-bolder text-dark">
                                <i class="fas fa-check text-primary mr-2"></i>
                                {{ __('مسارات وردود الطلب') }}
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="20%">{{ __('الجهة (قسم/دائرة)') }}</th>
                                            <th width="15%">{{ __('الموظف') }}</th>
                                            <th width="15%">{{ __('تاريخ الوصول') }}</th>
                                            <th width="12%">{{ __('حالة المسار') }}</th>
                                            <th width="15%">{{ __('تاريخ الرد') }}</th>
                                            <th width="18%">{{ __('الرد / سبب الرفض') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($request->responses as $response)
                                            @php
                                                $responseTypeId = $response->response_type;
                                                $responseTypeName = $response->responseType->name ?? '-';
                                                $isRejected = $responseTypeId == 3; // مرفوض
                                                $isApproved = $responseTypeId == 1; // مقبول
                                                $isPending = $responseTypeId == 2; // معلق
                                                $isWaiting = $responseTypeId == 4; // قيد الانتظار

                                                // تحديد لون الحالة
                                                $badgeClass = 'secondary';
                                                if ($isApproved) {
                                                    $badgeClass = 'success';
                                                } elseif ($isRejected) {
                                                    $badgeClass = 'danger';
                                                } elseif ($isPending) {
                                                    $badgeClass = 'warning';
                                                } elseif ($isWaiting) {
                                                    $badgeClass = 'info';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="font-weight-bolder text-dark">
                                                        {{ $response->unit_name }}
                                                    </span>
                                                    @if ($response->unit_type === 'department')
                                                        <span class="badge badge-light-info font-size-xs px-2 py-1">دائرة</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($response->responsedBy)
                                                        <span class="font-weight-bold text-primary">
                                                            {{ $response->responsedBy->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td style="direction: ltr;" class="text-right">
                                                    {{ $request->created_at->format('d-m-Y H:i') }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-lg badge-{{ $badgeClass }} font-weight-bold">
                                                        {{ $responseTypeName }}
                                                    </span>
                                                </td>
                                                <td style="direction: ltr;" class="text-right">
                                                    @if ($response->responsed_at)
                                                        {{ \Carbon\Carbon::parse($response->responsed_at)->format('d-m-Y H:i') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($isRejected && $response->notes && $response->notes != '-')
                                                        <div
                                                            class="bg-light-danger p-3 rounded border-left-danger border-left-3">
                                                            <div class="font-weight-bolder text-danger mb-1">
                                                                <i class="fas fa-times-circle text-danger"></i>
                                                                {{ __('سبب الرفض') }}:
                                                            </div>
                                                            <div class="text-dark-75 font-size-sm">
                                                                {{ $response->notes }}
                                                            </div>
                                                        </div>
                                                    @elseif($isApproved && $response->notes && $response->notes != '-')
                                                        <div
                                                            class="bg-light-success p-3 rounded border-left-success border-left-3">
                                                            <div class="text-dark-75 font-size-sm">
                                                                {{ $response->notes }}
                                                            </div>
                                                        </div>
                                                    @elseif($isPending && $response->notes && $response->notes != '-')
                                                        <div
                                                            class="bg-light-warning p-3 rounded border-left-warning border-left-3">
                                                            <div class="text-dark-75 font-size-sm">
                                                                {{ $response->notes }}
                                                            </div>
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
            </div>

            <!-- Right Column - Quick Info -->
            <div class="col-xl-4">
                <!-- Status Card -->
                <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                    <div class="card-header border-0 pt-7 pb-0">
                        <h3 class="card-title font-weight-bolder text-dark">
                            <i class="fas fa-info-circle text-primary mr-2"></i>
                            {{ __('حالة الطلب') }}
                        </h3>
                    </div>
                    <div class="card-body pt-6">
                        <div class="text-center mb-6">
                            <span class="badge badge-lg badge-{{ $request->status->color ?? 'secondary' }} font-weight-bold font-size-h4 p-4">
                                {{ $request->status->name ?? '-' }}
                            </span>
                        </div>
                        @if ($request->is_urgent)
                            <div class="alert alert-danger border-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-bell mr-3 font-size-h3"></i>
                                    <div>
                                        <div class="font-weight-bolder">{{ __('طلب عاجل') }}</div>
                                        <div class="text-muted font-size-sm">{{ __('يتطلب معالجة فورية') }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($request->status_reason)
                            <div class="alert alert-light border-0">
                                <div class="font-weight-bolder text-muted mb-2">{{ __('سبب الحالة') }}</div>
                                <div class="text-dark">{{ $request->status_reason }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Attachments Card -->
                <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                    <div class="card-header border-0 pt-7 pb-0">
                        <h3 class="card-title font-weight-bolder text-dark">
                            <i class="fas fa-paperclip text-primary mr-2"></i>
                            {{ __('المرفقات') }}
                        </h3>
                    </div>
                    <div class="card-body pt-6">
                        @if ($request->attachments->count() > 0)
                            <div class="mt-2">
                                @foreach ($request->attachments as $attachment)
                                    <div
                                        class="d-flex align-items-center justify-content-between mb-3 p-4 bg-light rounded border">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <div class="symbol symbol-50 mr-3">
                                                <div class="symbol-label bg-light-primary">
                                                    <i class="fas fa-file text-primary font-size-h3"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span
                                                    class="text-dark font-weight-bolder font-size-sm">{{ $attachment->file_name }}</span>
                                                <small class="text-muted font-size-xs">
                                                    {{ $attachment->file_size ? formatBytes($attachment->file_size) : '' }}
                                                    @if ($attachment->created_at)
                                                        • {{ $attachment->created_at->format('d-m-Y') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <a href="{{ route('requests.download-attachment', ['request' => $request->id, 'attachment' => $attachment->id]) }}"
                                            class="btn btn-sm btn-light-primary font-weight-bolder" target="_blank"
                                            title="{{ __('تحميل') }}">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-8">
                                <i class="fas fa-paperclip text-muted" style="font-size: 4rem;"></i>
                                <p class="mt-4 font-weight-bold">{{ __('لا توجد مرفقات') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Activity Timeline Card -->
                <div class="card card-custom gutter-b shadow-sm border-0 mb-8">
                    <div class="card-header border-0 pt-7 pb-0">
                        <h3 class="card-title font-weight-bolder text-dark">
                            <i class="fas fa-clock text-primary mr-2"></i>
                            {{ __('سجل التتبع') }}
                        </h3>
                    </div>
                    <div class="card-body pt-6">
                        @forelse(($activities ?? collect()) as $activity)
                            <div class="d-flex align-items-start mb-5">
                                <div class="symbol symbol-35 symbol-light-primary mr-3">
                                    <span class="symbol-label">
                                        <i class="fas fa-check text-primary"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bolder text-dark">
                                        {{ $activity->description ?: __('تحديث على الطلب') }}
                                    </div>
                                    <div class="text-muted font-size-sm">
                                        {{ optional($activity->causer)->name ?? __('النظام') }}
                                        <span class="mx-1">-</span>
                                        {{ $activity->created_at ? $activity->created_at->format('d-m-Y H:i') : '-' }}
                                    </div>
                                    @php
                                        $changed = collect($activity->properties['attributes'] ?? [])
                                            ->keys()
                                            ->take(4);
                                    @endphp
                                    @if ($changed->isNotEmpty())
                                        <div class="mt-2">
                                            @foreach ($changed as $field)
                                                <span
                                                    class="badge badge-light-secondary mr-1 mb-1">{{ $field }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-8">
                                <i class="fas fa-clock text-muted" style="font-size: 4rem;"></i>
                                <p class="mt-4 font-weight-bold">{{ __('لا توجد عمليات مسجلة بعد') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card card-custom gutter-b shadow-sm border-0">
                    <div class="card-header border-0 pt-7 pb-0">
                        <h3 class="card-title font-weight-bolder text-dark">
                            <i class="fas fa-cog text-primary mr-2"></i>
                            {{ __('إجراءات سريعة') }}
                        </h3>
                    </div>
                    <div class="card-body pt-6">
                        <div class="d-flex flex-column">
                            <a href="{{ route('requests.print', $request) }}"
                                class="btn btn-light-primary font-weight-bolder mb-3" target="_blank">
                                <i class="fas fa-print"></i> {{ __('طباعة الطلب') }}
                            </a>
                            @can('update requests')
                                <a href="{{ route('requests.edit', $request) }}"
                                    class="btn btn-primary font-weight-bolder mb-3">
                                    <i class="fas fa-pen"></i> {{ __('تعديل الطلب') }}
                                </a>
                            @endcan
                            @if ($request->mokalaf)
                                @can('view', $request->mokalaf)
                                    <a href="{{ route('mokalafs.show', $request->mokalaf) }}"
                                        class="btn btn-light-success font-weight-bolder mb-3">
                                        <i class="fas fa-user"></i> {{ __('ملف المكلف') }}
                                    </a>
                                @endcan
                            @endif
                            <a href="{{ route('requests.index') }}"
                                class="btn btn-light-primary font-weight-bolder mb-3">
                                <i class="fas fa-list"></i> {{ __('عرض جميع الطلبات') }}
                            </a>
                            @can('delete requests')
                                <button type="button" class="btn btn-light-danger font-weight-bolder w-100"
                                    onclick="deleteRequest({{ $request->id }})">
                                    <i class="fas fa-trash"></i> {{ __('حذف الطلب') }}
                                </button>
                                <form id="delete-form-{{ $request->id }}"
                                    action="{{ route('requests.destroy', $request) }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @can('update requests')
            <!-- Modal: تعديل حالة الطلب -->
            <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog"
                aria-labelledby="updateStatusModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title text-white font-weight-bolder" id="updateStatusModalLabel">
                                <i class="fas fa-check mr-2"></i> {{ __('تعديل حالة الطلب') }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="updateStatusForm" method="POST"
                            action="{{ route('requests.update-status', $request) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">{{ __('الحالة') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="status_id" id="status_id" class="form-control form-control-solid" required>
                                        @foreach ($requestStatuses ?? [] as $status)
                                            <option value="{{ $status->id }}"
                                                {{ ($request->status_id ?? '') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark">{{ __('سبب الحالة') }}</label>
                                    <textarea name="status_reason" id="status_reason" class="form-control form-control-solid" rows="3"
                                        placeholder="{{ __('اختياري - توضيح سبب تغيير الحالة') }}">{{ old('status_reason', $request->status_reason ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning font-weight-bolder"
                                    data-dismiss="modal">{{ __('إلغاء') }}</button>
                                <button type="submit" class="btn btn-success font-weight-bolder" id="updateStatusSubmitBtn">
                                    <i class="fas fa-check"></i> {{ __('حفظ الحالة') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    @push('styles')
    <style>
        .info-item {
            background: #f5f8fa;
            padding: 16px;
            border-radius: 8px;
            border-right: 3px solid #3699ff;
            transition: all 0.2s ease;
        }

        .info-item:hover {
            background: #edf2f7;
            transform: translateX(-4px);
        }

        .info-item label {
            display: block;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 700;
            color: #3f4254;
        }

        .bg-light-primary {
            background-color: #f3f6f9 !important;
        }

        .bg-light-success {
            background-color: #e8fff3 !important;
        }

        .bg-light-warning {
            background-color: #fff8dd !important;
        }

        .bg-light-danger {
            background-color: #ffe2e5 !important;
        }
    </style>
    @endpush
@endsection

@section('scripts')
    <script>
        @can('update requests')
            $(function() {
                $('#updateStatusForm').on('submit', function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var $btn = $('#updateStatusSubmitBtn');
                    var $badge = $('#request-status-badge');
                    $btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');
                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    }).done(function(res) {
                        if (res.success && res.status) {
                            $badge.removeClass().addClass('badge badge-lg badge-' + (res.status
                                .color || 'secondary') + ' font-weight-bold p-3').text(res
                                .status.name);
                            var $reasonWrap = $('#request-status-reason-wrap');
                            var $reason = $('#request-status-reason');
                            if (res.status_reason) {
                                $reason.text(res.status_reason);
                                $reasonWrap.show();
                            } else {
                                $reason.text('');
                                $reasonWrap.hide();
                            }
                            $('#updateStatusModal').modal('hide');
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: res.message || 'تم تحديث الحالة',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                alert(res.message || 'تم تحديث الحالة');
                            }
                        }
                    }).fail(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON
                            .message : 'حدث خطأ أثناء تحديث الحالة';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: msg
                            });
                        } else {
                            alert(msg);
                        }
                    }).always(function() {
                        $btn.prop('disabled', false).html(
                            '<i class="fas fa-check"></i> {{ __('حفظ الحالة') }}');
                    });
                    return false;
                });
            });
        @endcan

        function deleteRequest(requestId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'سيتم حذف الطلب بشكل نهائي ولا يمكن التراجع عن هذا الإجراء!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'جاري الحذف...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        document.getElementById('delete-form-' + requestId).submit();
                    }
                });
            } else {
                if (confirm('هل أنت متأكد من حذف هذا الطلب؟')) {
                    document.getElementById('delete-form-' + requestId).submit();
                }
            }
        }
    </script>
@endsection
