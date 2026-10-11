@extends('layout.master')

@section('title')
    {{ __('Central Treasury') }}
@endsection

@section('page_title')
    {{ __('Central Treasury') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('Central Treasury Daily Batch') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('Central Treasury Daily Batch') }}"
                subtitle="{{ __('View and manage today central revenue batch status and payments') }}"
            >
                <x-slot name="actions">
                    @can('central_treasury.manage')
                                            @if($unifiedBatch && $unifiedBatch->isOpen() && $sectionBatches->where('status', 'open')->isEmpty())
                                                <form action="{{ route('central_treasury.close_unified_today') }}" method="POST" class="d-inline mr-2 mb-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-light-danger font-weight-bolder"
                                                            onclick="return confirm('{{ __('هل أنت متأكد من إغلاق الرزمة الموحدة؟ تأكد من إغلاق جميع رزم الأقسام أولاً (أو عدم وجود أقسام مفتوحة).') }}')">
                                                        <i class="fas fa-lock"></i> {{ __('Close Unified Batch') }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body pt-7">
                @if(session('success'))
                    <div class="alert alert-success mt-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger mt-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="alert alert-light-info mt-4">
                    {{ __('Unified batch opens automatically on the first payment of the day; each department gets its own sub-batch.') }}
                </div>

                @if(!$unifiedBatch)
                    <div class="alert alert-secondary mt-4">
                        {{ __('No unified batch for today yet. It will be created when the first payment is recorded.') }}
                    </div>
                @else
                    <div class="row mb-5 mt-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-custom stat-card shadow-sm h-100 border-top border-primary border-4">
                                <div class="card-body p-5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted font-weight-bold font-size-sm">{{ __('Batch Date') }}</span>
                                            <h3 class="font-weight-bolder mt-2 mb-0 text-dark">
                                                {{ $unifiedBatch->date->format('Y-m-d') }}
                                            </h3>
                                        </div>
                                        <div class="icon-box bg-primary-grad">
                                            <i class="fas fa-calendar text-white icon-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-custom stat-card shadow-sm h-100 border-top border-secondary border-4">
                                <div class="card-body p-5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted font-weight-bold font-size-sm">{{ __('Batch Number') }}</span>
                                            <h3 class="font-weight-bolder mt-2 mb-0 text-dark">
                                                {{ $unifiedBatch->sequence_number ?? '—' }}
                                            </h3>
                                        </div>
                                        <div class="icon-box bg-secondary-grad">
                                            <i class="fas fa-copy text-white icon-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-custom stat-card shadow-sm h-100 border-top border-info border-4">
                                <div class="card-body p-5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted font-weight-bold font-size-sm">{{ __('status') }}</span>
                                            <h3 class="font-weight-bolder mt-2 mb-0">
                                                <span class="label label-lg {{ $unifiedBatch->isOpen() ? 'label-light-success' : 'label-light-info' }} label-inline font-weight-bold">
                                                    {{ $unifiedBatch->isOpen() ? __('Open') : __('Closed') }}
                                                </span>
                                            </h3>
                                        </div>
                                        <div class="icon-box bg-info-grad">
                                            <i class="fas fa-info-circle text-white icon-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-custom stat-card shadow-sm h-100 border-top border-success border-4">
                                <div class="card-body p-5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted font-weight-bold font-size-sm">{{ __('Total Revenue') }}</span>
                                            <h3 class="font-weight-bolder mt-2 mb-0 text-success">
                                                {{ number_format((float) ($todayActiveTotal ?? 0), 2) }} {{ __('NIS') }}
                                            </h3>
                                        </div>
                                        <div class="icon-box bg-success-grad">
                                            <i class="fas fa-chart-line text-white icon-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card card-custom stat-card shadow-sm h-100 border-top border-warning border-4">
                                <div class="card-body p-5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted font-weight-bold font-size-sm">{{ __('Payments Count') }}</span>
                                            <h3 class="font-weight-bolder mt-2 mb-0 text-warning">
                                                {{ $unifiedBatch->payments_count }}
                                            </h3>
                                        </div>
                                        <div class="icon-box bg-warning-grad">
                                            <i class="fas fa-list text-white icon-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <div class="col-lg-12 mb-5">
                            <div class="row">
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card card-custom shadow-sm h-100 border-top border-primary border-4">
                                        <div class="card-body p-5">
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <div>
                                                    <div class="text-muted font-weight-bold font-size-sm">{{ __('دفعات طلبات قلم الجمهور') }}</div>
                                                    <h3 class="font-weight-bolder mt-2 mb-0 text-primary">
                                                        {{ number_format((float) ($requestsTotal ?? 0), 2) }} {{ __('NIS') }}
                                                    </h3>
                                                </div>
                                                @can('central_treasury.manage')
                                                    @php($requestBatch = $sectionBatchBySource['public_pen_requests'] ?? null)
                                                    @if($requestBatch && $requestBatch->isOpen())
                                                        <form action="{{ route('central_treasury.close_section_batch', $requestBatch) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-light-danger font-weight-bold"
                                                                    onclick="return confirm('{{ __('هل تريد إغلاق رزمة دفعات طلبات قلم الجمهور؟') }}')">
                                                                {{ __('إغلاق الرزمة الفرعية') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card card-custom shadow-sm h-100 border-top border-warning border-4">
                                        <div class="card-body p-5">
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <div>
                                                    <div class="text-muted font-weight-bold font-size-sm">{{ __('دفعات الهندسة والتنظيم') }}</div>
                                                    <h3 class="font-weight-bolder mt-2 mb-0 text-warning">
                                                        {{ number_format((float) ($engineeringTotal ?? 0), 2) }} {{ __('NIS') }}
                                                    </h3>
                                                </div>
                                                @can('central_treasury.manage')
                                                    @php($engineeringBatch = $sectionBatchBySource['engineering'] ?? null)
                                                    @if($engineeringBatch && $engineeringBatch->isOpen())
                                                        <form action="{{ route('central_treasury.close_section_batch', $engineeringBatch) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-light-danger font-weight-bold"
                                                                    onclick="return confirm('{{ __('هل تريد إغلاق رزمة دفعات الهندسة والتنظيم؟') }}')">
                                                                {{ __('إغلاق الرزمة الفرعية') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card card-custom shadow-sm h-100 border-top border-info border-4">
                                        <div class="card-body p-5">
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <div>
                                                    <div class="text-muted font-weight-bold font-size-sm">{{ __('دفعات رخص الحرف والصناعات') }}</div>
                                                    <h3 class="font-weight-bolder mt-2 mb-0 text-info">
                                                        {{ number_format((float) ($craftTotal ?? 0), 2) }} {{ __('NIS') }}
                                                    </h3>
                                                </div>
                                                @can('central_treasury.manage')
                                                    @php($craftBatch = $sectionBatchBySource['crafts'] ?? null)
                                                    @if($craftBatch && $craftBatch->isOpen())
                                                        <form action="{{ route('central_treasury.close_section_batch', $craftBatch) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-light-danger font-weight-bold"
                                                                    onclick="return confirm('{{ __('هل تريد إغلاق رزمة دفعات رخص الحرف والصناعات؟') }}')">
                                                                {{ __('إغلاق الرزمة الفرعية') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card card-custom shadow-sm h-100 border-top border-success border-4">
                                        <div class="card-body p-5">
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <div>
                                                    <div class="text-muted font-weight-bold font-size-sm">{{ __('دفعات التحصيل الخارجي') }}</div>
                                                    <h3 class="font-weight-bolder mt-2 mb-0 text-success">
                                                        {{ number_format((float) ($externalCollectionTotal ?? 0), 2) }} {{ __('NIS') }}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card card-custom shadow-sm gutter-b border-left border-primary border-3">
                                <div class="card-header border-0 pt-6">
                                    <h3 class="card-title font-weight-bolder text-dark mb-0">
                                        {{ __('Department revenue batches (today)') }}
                                    </h3>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-borderless">
                                            <thead>
                                                <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                                    <th>#</th>
                                                    <th>{{ __('Section') }}</th>
                                                    <th>{{ __('Batch Number') }}</th>
                                                    <th>{{ __('status') }}</th>
                                                    <th>{{ __('Total Revenue') }}</th>
                                                    <th>{{ __('Payments Count') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($sectionBatches as $sb)
                                                    <tr>
                                                        <td>{{ $sb->id }}</td>
                                                        <td>{{ $sb->section->name ?? ('#' . $sb->section_id) }}</td>
                                                        <td>{{ $sb->sequence_number ?? '—' }}</td>
                                                        <td>
                                                            <span class="label label-inline {{ $sb->isOpen() ? 'label-light-success' : 'label-light-info' }} font-weight-bold">
                                                                {{ $sb->isOpen() ? __('Open') : __('Closed') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ number_format((float) $sb->total_amount, 2) }} {{ __('NIS') }}</td>
                                                        <td>{{ $sb->payments_count }}</td>
                                                        <td>
                                                            @can('central_treasury.manage')
                                                                @if($sb->isOpen())
                                                                    <form action="{{ route('central_treasury.close_section_batch', $sb) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-light-danger font-weight-bold"
                                                                                onclick="return confirm('{{ __('Close this department batch?') }}')">
                                                                            {{ __('Close department batch') }}
                                                                        </button>
                                                                    </form>
                                                                @elseif($sb->date && $sb->date->isToday())
                                                                    <form action="{{ route('central_treasury.reopen_batch', $sb) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-light-success font-weight-bold"
                                                                                onclick="return confirm('{{ __('Reopen this department batch?') }}')">
                                                                            {{ __('Reopen') }}
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-8">
                                                            {{ __('No department batches yet today') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 mb-5">
                            <div class="card card-custom shadow-sm gutter-b">
                                <div class="card-header border-0 pt-7">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label font-weight-bolder text-dark">
                                            {{ __('External Collection Payments') }}
                                        </span>
                                        <span class="text-muted mt-3 font-weight-bold font-size-sm">
                                            {{ __('All external collection payments included in today batch') }}
                                        </span>
                                    </h3>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-borderless">
                                            <thead>
                                                <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                                    <th>#</th>
                                                    <th>{{ __('Payment Number') }}</th>
                                                    <th>{{ __('Type') }}</th>
                                                    <th>{{ __('Taxpayer') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Balance After') }}</th>
                                                    <th>{{ __('Payment Method') }}</th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Notes') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($externalCollectionPayments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>{{ $payment->payment_number ?? '—' }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-success font-weight-bold">
                                                                {{ __('External Collection') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($payment->externalCollection)->taxpayer_full_name ?? '-' }}</td>
                                                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                        <td>{{ number_format((float) ($payment->balance_after ?? 0), 2) }}</td>
                                                        <td>{{ $payment->payment_method }}</td>
                                                        <td>{{ optional($payment->user)->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                        <td>{{ $payment->notes }}</td>
                                                        <td>
                                                            <a href="{{ route('receipts.external-collection', $payment) }}" target="_blank"
                                                                class="btn btn-sm btn-light-primary font-weight-bold">
                                                                {{ __('طباعة إيصال') }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center py-10">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                                                <span class="text-muted font-weight-bold font-size-h5">
                                                                    {{ __('No external collection payments in this batch yet') }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mb-5">
                            <div class="card card-custom shadow-sm gutter-b">
                                <div class="card-header border-0 pt-7">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label font-weight-bolder text-dark">
                                            {{ __('Crafts and Industries Payments') }}
                                        </span>
                                        <span class="text-muted mt-3 font-weight-bold font-size-sm">
                                            {{ __('All craft license payments included in today batch') }}
                                        </span>
                                    </h3>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-borderless">
                                            <thead>
                                                <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                                    <th>#</th>
                                                    <th>{{ __('Payment Number') }}</th>
                                                    <th>{{ __('Type') }}</th>
                                                    <th>{{ __('File Number') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Balance After') }}</th>
                                                    <th>{{ __('Payment Method') }}</th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Notes') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($craftPayments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>{{ $payment->payment_number ?? '—' }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-info font-weight-bold">
                                                                {{ __('Crafts and Industries') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($payment->craftLicense)->file_number }}</td>
                                                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                        <td>{{ number_format((float) $payment->balance_after, 2) }}</td>
                                                        <td>{{ $payment->payment_method }}</td>
                                                        <td>{{ optional($payment->user)->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                        <td>{{ $payment->notes }}</td>
                                                        <td>
                                                            <a href="{{ route('receipts.payments.craft', $payment) }}" target="_blank"
                                                                class="btn btn-sm btn-light-primary font-weight-bold">
                                                                {{ __('طباعة إيصال') }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center py-10">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                                                <span class="text-muted font-weight-bold font-size-h5">
                                                                    {{ __('No craft payments in this batch yet') }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="card card-custom shadow-sm gutter-b">
                                <div class="card-header border-0 pt-7">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label font-weight-bolder text-dark">
                                            {{ __('Engineering and Organization Payments') }}
                                        </span>
                                        <span class="text-muted mt-3 font-weight-bold font-size-sm">
                                            {{ __('All engineering payments included in today batch') }}
                                        </span>
                                    </h3>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-borderless">
                                            <thead>
                                                <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                                    <th>#</th>
                                                    <th>{{ __('Payment Number') }}</th>
                                                    <th>{{ __('Type') }}</th>
                                                    <th>{{ __('File Number') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Balance After') }}</th>
                                                    <th>{{ __('Payment Method') }}</th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Notes') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($engineeringPayments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>{{ $payment->payment_number ?? '—' }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-warning font-weight-bold">
                                                                {{ __('Engineering and Organization') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($payment->engineering)->file_number }}</td>
                                                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                        <td>{{ number_format((float) $payment->balance_after, 2) }}</td>
                                                        <td>{{ $payment->payment_method }}</td>
                                                        <td>{{ optional($payment->user)->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                        <td>{{ $payment->notes }}</td>
                                                        <td>
                                                            <a href="{{ route('receipts.payments.engineering', $payment) }}" target="_blank"
                                                                class="btn btn-sm btn-light-primary font-weight-bold">
                                                                {{ __('طباعة إيصال') }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center py-10">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                                                <span class="text-muted font-weight-bold font-size-h5">
                                                                    {{ __('No engineering payments in this batch yet') }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="card card-custom shadow-sm gutter-b">
                                <div class="card-header border-0 pt-7">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label font-weight-bolder text-dark">
                                            {{ __('Public Pen Requests Payments') }}
                                        </span>
                                        <span class="text-muted mt-3 font-weight-bold font-size-sm">
                                            {{ __('All public pen request payments included in today batch') }}
                                        </span>
                                    </h3>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-borderless">
                                            <thead>
                                                <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                                    <th>#</th>
                                                    <th>{{ __('Payment Number') }}</th>
                                                    <th>{{ __('Type') }}</th>
                                                    <th>{{ __('Request ID') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Balance After') }}</th>
                                                    <th>{{ __('Payment Method') }}</th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Notes') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($requestPayments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>{{ $payment->payment_number ?? '—' }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-primary font-weight-bold">
                                                                {{ __('Public Pen Requests') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($payment->citizenRequest)->id }}</td>
                                                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                        <td>{{ number_format((float) ($payment->balance_after ?? 0), 2) }}</td>
                                                        <td>{{ $payment->payment_method }}</td>
                                                        <td>{{ optional($payment->user)->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                        <td>{{ $payment->notes }}</td>
                                                        <td>
                                                            <a href="{{ route('receipts.payments.request', $payment) }}" target="_blank"
                                                                class="btn btn-sm btn-light-primary font-weight-bold">
                                                                {{ __('طباعة إيصال') }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center py-10">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                                                <span class="text-muted font-weight-bold font-size-h5">
                                                                    {{ __('No public pen request payments in this batch yet') }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- جدول الدفعات الملغاة --}}
                    <div class="row mt-8">
                        <div class="col-lg-12 mb-5">
                            <div class="card card-custom shadow-sm gutter-b">
                                <div class="card-header border-0 pt-7">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label font-weight-bolder text-danger">
                                            {{ __('Cancelled Payments (All Sources)') }}
                                        </span>
                                        <span class="text-muted mt-3 font-weight-bold font-size-sm">
                                            {{ __('This table shows payments that were cancelled and are not counted in today totals') }}
                                        </span>
                                    </h3>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center table-borderless">
                                            <thead>
                                                <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                                    <th>#</th>
                                                    <th>{{ __('Type') }}</th>
                                                    <th>{{ __('File Number') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Balance After') }}</th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Cancellation Reason') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($cancelledCraftPayments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-info font-weight-bold">
                                                                {{ __('Crafts and Industries') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($payment->craftLicense)->file_number }}</td>
                                                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                        <td>{{ number_format((float) ($payment->balance_after ?? 0), 2) }}</td>
                                                        <td>{{ optional($payment->user)->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-danger font-weight-bold">
                                                                {{ __('Cancelled') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $payment->cancellation_reason }}</td>
                                                    </tr>
                                                @empty
                                                @endforelse

                                                @forelse($cancelledEngineeringPayments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-warning font-weight-bold">
                                                                {{ __('Engineering and Organization') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($payment->engineering)->file_number }}</td>
                                                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                        <td>{{ number_format((float) ($payment->balance_after ?? 0), 2) }}</td>
                                                        <td>{{ optional($payment->user)->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-danger font-weight-bold">
                                                                {{ __('Cancelled') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $payment->cancellation_reason }}</td>
                                                    </tr>
                                                @empty
                                                @endforelse

                                                @forelse($cancelledRequestPayments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-primary font-weight-bold">
                                                                {{ __('Public Pen Requests') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($payment->citizenRequest)->id }}</td>
                                                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                        <td>{{ number_format((float) ($payment->balance_after ?? 0), 2) }}</td>
                                                        <td>{{ optional($payment->user)->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-danger font-weight-bold">
                                                                {{ __('Cancelled') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $payment->cancellation_reason }}</td>
                                                    </tr>
                                                @empty
                                                @endforelse

                                                @forelse($cancelledExternalCollectionPayments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->id }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-success font-weight-bold">
                                                                {{ __('External Collection') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($payment->externalCollection)->taxpayer_full_name ?? '-' }}</td>
                                                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                        <td>{{ number_format((float) ($payment->balance_after ?? 0), 2) }}</td>
                                                        <td>{{ optional($payment->user)->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                        <td>
                                                            <span class="label label-inline label-light-danger font-weight-bold">
                                                                {{ __('Cancelled') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $payment->cancellation_reason }}</td>
                                                    </tr>
                                                @empty
                                                @endforelse

                                                @if($cancelledCraftPayments->isEmpty() && $cancelledEngineeringPayments->isEmpty() && $cancelledRequestPayments->isEmpty() && $cancelledExternalCollectionPayments->isEmpty())
                                                    <tr>
                                                        <td colspan="9" class="text-center py-10">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                                                <span class="text-muted font-weight-bold font-size-h5">
                                                                    {{ __('No cancelled payments in this batch') }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

