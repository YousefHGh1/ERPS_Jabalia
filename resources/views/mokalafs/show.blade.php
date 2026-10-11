@extends('layout.master')

@section('title')
    {{ __('ملف المكلف') }} - {{ $mokalaf->name }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('ملف المكلف') }}
@endsection

@section('content')
    @php
        $mokalafQuery = array_filter(
            [
                'identity_number' => $mokalaf->identity_number ?? null,
                'name' => $mokalaf->name ?? null,
                'address' => $mokalaf->address ?? null,
                'mobile' => $mokalaf->mobile ?? null,
            ],
            fn($v) => $v !== null && $v !== '',
        );
        $mokalafQueryWithId = array_merge($mokalafQuery, ['mokalaf_id' => $mokalaf->id]);
    @endphp
    @php
        $overallRemaining = (float) ($overallRemainingTotal ?? 0);
        $requestsRemaining = (float) ($sectionRemainingTotals['requests'] ?? 0);
        $craftRemaining = (float) ($sectionRemainingTotals['craft_licenses'] ?? 0);
        $engineeringRemaining = (float) ($sectionRemainingTotals['engineerings'] ?? 0);
        $externalRemaining = (float) ($sectionRemainingTotals['external_collections'] ?? 0);

        $overallBadgeClass = $overallRemaining > 0 ? 'badge-light-danger' : 'badge-light-success';
        $requestsBadgeClass = $requestsRemaining > 0 ? 'badge-light-warning' : 'badge-light-success';
        $craftBadgeClass = $craftRemaining > 0 ? 'badge-light-warning' : 'badge-light-success';
        $engineeringBadgeClass = $engineeringRemaining > 0 ? 'badge-light-warning' : 'badge-light-success';
        $externalBadgeClass = $externalRemaining > 0 ? 'badge-light-warning' : 'badge-light-success';
    @endphp
    <div class="container-fluid">
        {{-- رأس الصفحة: بيانات المكلف --}}
        <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
            <div class="card-body py-6">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-60 symbol-light-primary mr-5">
                            <span
                                class="symbol-label font-size-h2 font-weight-boldest">{{ mb_substr($mokalaf->name ?? '?', 0, 2) }}</span>
                        </div>
                        <div>
                            <h1 class="font-weight-bolder text-dark mb-1">
                                {{ $mokalaf->name ?? '---' }}
                                <span class="badge {{ $overallBadgeClass }} font-size-sm ml-2">
                                    {{ __('إجمالي المتبقي') }}:
                                    {{ number_format($overallRemaining, 2) }}
                                    {{ __('NIS') }}
                                </span>
                            </h1>
                            <span
                                class="badge badge-{{ $mokalaf->identity_number ? 'info' : 'warning' }} font-size-sm">{{ $mokalaf->identifier }}</span>
                            <span class="text-muted font-size-sm mr-2">({{ $mokalaf->identifier_type }})</span>
                            @if ($mokalaf->address)
                                <div class="text-muted font-size-sm mt-1">{{ $mokalaf->address }}</div>
                            @endif
                            @if ($mokalaf->mobile)
                                <div class="text-muted font-size-sm">{{ $mokalaf->mobile }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap">
                        @can('update mokalafs')
                            <a href="{{ route('mokalafs.edit', $mokalaf) }}" class="btn btn-success font-weight-bolder mr-2"
                                title="{{ __('تعديل') }}">
                                <i class="fas fa-pen mr-1" aria-hidden="true"></i>
                                {{ __('تعديل') }}
                            </a>
                        @endcan
                        <a href="{{ route('mokalafs.index') }}" class="btn btn-light-primary font-weight-bolder"
                            title="{{ __('عودة لقائمة المكلفين') }}">
                            <i class="fas fa-arrow-right mr-1" aria-hidden="true"></i>
                            {{ __('عودة لقائمة المكلفين') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- الطلبات --}}
        @can('index requests')
            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fa fa-clipboard-list text-primary mr-2"></i>
                        {{ __('الطلبات') }}
                        <span class="badge badge-primary ml-2">{{ $requests->count() }}</span>
                        <span class="badge {{ $requestsBadgeClass }} ml-2">
                            {{ __('المتبقي') }}:
                            {{ number_format($requestsRemaining, 2) }}
                            {{ __('NIS') }}
                        </span>
                    </h3>
                    <div class="card-toolbar">
                        {{-- نفس شرط سياسة الطلبات RequestPolicy::create (create requests أو store requests) --}}
                        @canany(['create requests', 'store requests'])
                            <a href="{{ route('requests.create', $mokalafQueryWithId) }}" class="btn btn-sm btn-success mr-2"><i
                                    class="fas fa-plus"></i> {{ __('إضافة جديد') }}</a>
                        @endcanany
                        <a href="{{ route('requests.index') }}" class="btn btn-sm btn-light-primary">{{ __('عرض الكل') }}</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if ($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('الطلب') }}</th>
                                        <th>{{ __('النوع') }}</th>
                                        <th>{{ __('الحالة') }}</th>
                                        <th>{{ __('التاريخ') }}</th>
                                        <th>{{ __('إجراء') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests as $req)
                                        <tr>
                                            <td>{{ \Illuminate\Support\Str::limit($req->text ?? '---', 50) }}</td>
                                            <td>{{ $req->requestType->name ?? '---' }}</td>
                                            <td><span
                                                    class="label label-inline label-light-warning">{{ $req->status->name ?? '---' }}</span>
                                            </td>
                                            <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                            <td><a href="{{ route('requests.show', $req->id) }}"
                                                    class="btn btn-sm btn-light-primary">{{ __('عرض') }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('لا توجد طلبات مرتبطة بهذا المكلف') }}</p>
                    @endif
                </div>
            </div>
        @endcan

        {{-- تراخيص الحرف --}}
        @can('index craft_licenses')
            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fas fa-tools text-success mr-2"></i>
                        {{ __('تراخيص الحرف') }}
                        <span class="badge badge-success ml-2">{{ $craftLicenses->count() }}</span>
                        <span class="badge {{ $craftBadgeClass }} ml-2">
                            {{ __('المتبقي') }}:
                            {{ number_format($craftRemaining, 2) }}
                            {{ __('NIS') }}
                        </span>
                    </h3>
                    <div class="card-toolbar">
                        @can('create craft_licenses')
                            <a href="{{ route('craft_licenses.create', $mokalafQuery) }}" class="btn btn-sm btn-success mr-2"><i
                                    class="fas fa-plus"></i> {{ __('إضافة جديد') }}</a>
                        @endcan
                        <a href="{{ route('craft_licenses.index') }}"
                            class="btn btn-sm btn-light-success">{{ __('عرض الكل') }}</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if ($craftLicenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('رقم الملف') }}</th>
                                        <th>{{ __('الاسم التجاري') }}</th>
                                        <th>{{ __('الحالة') }}</th>
                                        <th>{{ __('التاريخ') }}</th>
                                        <th>{{ __('إجراء') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($craftLicenses as $lic)
                                        <tr>
                                            <td>{{ $lic->file_number ?? '---' }}</td>
                                            <td>{{ $lic->commercial_name ?? ($lic->owner_name ?? '---') }}</td>
                                            <td><span
                                                    class="label label-inline label-light-{{ $lic->status == 'active' ? 'success' : 'warning' }}">{{ $lic->status ?? '---' }}</span>
                                            </td>
                                            <td>{{ $lic->created_at ? $lic->created_at->format('Y-m-d') : '---' }}</td>
                                            <td><a href="{{ route('craft_licenses.show', $lic->id) }}"
                                                    class="btn btn-sm btn-light-success">{{ __('عرض') }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('لا توجد تراخيص حرف مرتبطة برقم الهوية') }}</p>
                    @endif
                </div>
            </div>
        @endcan

        {{-- ملفات الهندسة والتنظيم --}}
        @can('index engineerings')
            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fas fa-drafting-compass text-info mr-2"></i>
                        {{ __('ملفات الهندسة والتنظيم') }}
                        <span class="badge badge-info ml-2">{{ $engineerings->count() }}</span>
                        <span class="badge {{ $engineeringBadgeClass }} ml-2">
                            {{ __('المتبقي') }}:
                            {{ number_format($engineeringRemaining, 2) }}
                            {{ __('NIS') }}
                        </span>
                    </h3>
                    <div class="card-toolbar">
                        @can('create engineerings')
                            <a href="{{ route('engineering.create', $mokalafQuery) }}" class="btn btn-sm btn-success mr-2"><i
                                    class="fas fa-plus"></i> {{ __('إضافة جديد') }}</a>
                        @endcan
                        <a href="{{ route('engineering.index') }}" class="btn btn-sm btn-light-info">{{ __('عرض الكل') }}</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if ($engineerings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('رقم الملف') }}</th>
                                        <th>{{ __('اسم المالك') }}</th>
                                        <th>{{ __('الحالة') }}</th>
                                        <th>{{ __('التاريخ') }}</th>
                                        <th>{{ __('إجراء') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($engineerings as $eng)
                                        <tr>
                                            <td>{{ $eng->file_number ?? '---' }}</td>
                                            <td>{{ trim(($eng->first_name ?? '') . ' ' . ($eng->father_name ?? '') . ' ' . ($eng->family_name ?? '')) ?: '---' }}
                                            </td>
                                            <td>
                                                @if (($eng->remaining_amount ?? 0) > 0)
                                                    <span
                                                        class="label label-inline label-light-warning">{{ __('متبقي') }}</span>
                                                @else
                                                    <span
                                                        class="label label-inline label-light-success">{{ __('مسدد') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $eng->created_at ? $eng->created_at->format('Y-m-d') : '---' }}</td>
                                            <td><a href="{{ route('engineering.show', $eng->id) }}"
                                                    class="btn btn-sm btn-light-info">{{ __('عرض') }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('لا توجد ملفات هندسة مرتبطة برقم الهوية') }}</p>
                    @endif
                </div>
            </div>
        @endcan

        {{-- التحصيلات الخارجية --}}
        {{-- التحصيلات الخارجية: نفس الصلاحية المفروضة في ExternalCollectionController (view external_collections) --}}
        @can('view external_collections')
            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fas fa-tag text-primary mr-2"></i>
                        {{ __('التحصيلات الخارجية') }}
                        <span class="badge badge-primary ml-2">{{ $externalCollections->count() }}</span>
                        <span class="badge {{ $externalBadgeClass }} ml-2">
                            {{ __('المتبقي') }}:
                            {{ number_format($externalRemaining, 2) }}
                            {{ __('NIS') }}
                        </span>
                    </h3>
                    <div class="card-toolbar">
                        @can('create external_collections')
                            <a href="{{ route('external-collections.create', $mokalafQuery) }}"
                                class="btn btn-sm btn-success mr-2"><i class="fas fa-plus"></i> {{ __('إضافة جديد') }}</a>
                        @endcan
                        <a href="{{ route('external-collections.index') }}"
                            class="btn btn-sm btn-light-primary">{{ __('عرض الكل') }}</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if ($externalCollections->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('القسم') }}</th>
                                        <th>{{ __('المبلغ') }}</th>
                                        <th>{{ __('المدفوع') }}</th>
                                        <th>{{ __('المتبقي') }}</th>
                                        <th>{{ __('الحالة') }}</th>
                                        <th>{{ __('إجراء') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($externalCollections as $ext)
                                        <tr>
                                            <td>{{ $ext->section->name ?? '---' }}</td>
                                            <td>{{ number_format($ext->amount, 2) }}</td>
                                            <td>{{ number_format($ext->paid_amount, 2) }}</td>
                                            <td>{{ number_format($ext->remaining_amount, 2) }}</td>
                                            <td>{!! $ext->payment_status_badge !!}</td>
                                            <td><a href="{{ route('external-collections.show', $ext->id) }}"
                                                    class="btn btn-sm btn-light-primary">{{ __('عرض') }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('لا توجد تحصيلات خارجية مرتبطة') }}</p>
                    @endif
                </div>
            </div>
        @endcan

        {{-- أوامر الحبس --}}
        @can('index imprisonment_orders')
            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fa fa-gavel text-danger mr-2"></i>
                        {{ __('أوامر الحبس') }}
                        <span class="badge badge-danger ml-2">{{ $imprisonmentOrders->count() }}</span>
                    </h3>
                    <div class="card-toolbar">
                        @can('store imprisonment_orders')
                            <a href="{{ route('imprisonment_orders.create', $mokalafQuery) }}"
                                class="btn btn-sm btn-success mr-2"><i class="fas fa-plus"></i> {{ __('إضافة جديد') }}</a>
                        @endcan
                        <a href="{{ route('imprisonment_orders.index') }}"
                            class="btn btn-sm btn-light-danger">{{ __('عرض الكل') }}</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if ($imprisonmentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('رقم الأمر') }}</th>
                                        <th>{{ __('المتهم') }}</th>
                                        <th>{{ __('الحالة') }}</th>
                                        <th>{{ __('التاريخ') }}</th>
                                        <th>{{ __('إجراء') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($imprisonmentOrders as $order)
                                        <tr>
                                            <td>{{ $order->order_number ?? '---' }}</td>
                                            <td>{{ $order->defendant_name ?? '---' }} <small
                                                    class="text-muted">({{ $order->defendant_identity ?? '' }})</small></td>
                                            <td><span
                                                    class="label label-inline label-light-warning">{{ $order->status ?? '---' }}</span>
                                            </td>
                                            <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : '---' }}
                                            </td>
                                            <td><a href="{{ route('imprisonment_orders.show', $order->id) }}"
                                                    class="btn btn-sm btn-light-danger">{{ __('عرض') }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('لا توجد أوامر حبس مرتبطة برقم الهوية') }}</p>
                    @endif
                </div>
            </div>
        @endcan

        {{-- الحجوزات --}}
        @can('index reservations')
            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fa fa-calendar-alt text-primary mr-2"></i>
                        {{ __('الحجوزات') }}
                        <span class="badge badge-primary ml-2">{{ $reservations->count() }}</span>
                    </h3>
                    <div class="card-toolbar">
                        @can('store reservations')
                            <a href="{{ route('reservations.create', $mokalafQuery) }}" class="btn btn-sm btn-success mr-2"><i
                                    class="fas fa-plus"></i> {{ __('إضافة جديد') }}</a>
                        @endcan
                        <a href="{{ route('reservations.index') }}"
                            class="btn btn-sm btn-light-primary">{{ __('عرض الكل') }}</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if ($reservations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('رقم الحجز') }}</th>
                                        <th>{{ __('الموضوع') }}</th>
                                        <th>{{ __('التاريخ') }}</th>
                                        <th>{{ __('الحالة') }}</th>
                                        <th>{{ __('إجراء') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservations as $res)
                                        <tr>
                                            <td>{{ $res->reservation_number ?? ($res->reservation_no ?? '---') }}</td>
                                            <td>{{ $res->subject ?? ($res->description ?? '---') }}</td>
                                            <td>{{ $res->reservation_date ? $res->reservation_date->format('Y-m-d') : '---' }}
                                            </td>
                                            <td><span
                                                    class="label label-inline label-light-warning">{{ $res->status ?? '---' }}</span>
                                            </td>
                                            <td><a href="{{ route('reservations.show', $res->id) }}"
                                                    class="btn btn-sm btn-light-primary">{{ __('عرض') }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('لا توجد حجوزات مرتبطة برقم الهوية') }}</p>
                    @endif
                </div>
            </div>
        @endcan

        {{-- الإخطارات --}}
        @can('index notices')
            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                <div class="card-header border-0 py-5">
                    <h3 class="card-title font-weight-bolder text-dark">
                        <i class="fa fa-bell text-warning mr-2"></i>
                        {{ __('الإخطارات') }}
                        <span class="badge badge-warning ml-2">{{ $notices->count() }}</span>
                    </h3>
                    <div class="card-toolbar">
                        @can('store notices')
                            <a href="{{ route('notices.create', $mokalafQuery) }}" class="btn btn-sm btn-success mr-2"><i
                                    class="fas fa-plus"></i> {{ __('إضافة جديد') }}</a>
                        @endcan
                        <a href="{{ route('notices.index') }}"
                            class="btn btn-sm btn-light-warning">{{ __('عرض الكل') }}</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if ($notices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-head-custom table-vertical-center">
                                <thead>
                                    <tr>
                                        <th>{{ __('رقم الإخطار') }}</th>
                                        <th>{{ __('التاريخ') }}</th>
                                        <th>{{ __('الحالة') }}</th>
                                        <th>{{ __('إجراء') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notices as $notice)
                                        <tr>
                                            <td>{{ $notice->notice_number ?? '---' }}</td>
                                            <td>{{ $notice->notice_date ? $notice->notice_date->format('Y-m-d') : '---' }}
                                            </td>
                                            <td><span
                                                    class="label label-inline label-light-info">{{ $notice->status ?? '---' }}</span>
                                            </td>
                                            <td><a href="{{ route('notices.show', $notice->id) }}"
                                                    class="btn btn-sm btn-light-warning">{{ __('عرض') }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('لا توجد إخطارات مرتبطة برقم الهوية') }}</p>
                    @endif
                </div>
            </div>
        @endcan
    </div>
@endsection
