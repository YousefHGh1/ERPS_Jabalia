@extends('layout.master')

@section('title')
    {{ __('Central Treasury') }} - {{ __('Batch Date') }} {{ $batch->date?->format('Y-m-d') }}
@endsection

@section('page_title')
    {{ __('Central Treasury') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('Central Treasury Daily Batch') }} - {{ $batch->date?->format('Y-m-d') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('Central Treasury Daily Batch') }} - {{ $batch->date?->format('Y-m-d') }}"
                subtitle="{{ __('View closed batch details (read-only)') }}"
            >
                <x-slot name="actions">
                    <a href="{{ route('central_treasury.history') }}"
                                           class="btn btn-light-primary font-weight-bolder">
                                            <i class="fas fa-arrow-right"></i> {{ __('Back') }}
                                        </a>

                                        @can('central_treasury.manage')
                                            @if($batch->isSectionBatch() && $batch->date && $batch->date->isToday() && ! $batch->isOpen())
                                                <form action="{{ route('central_treasury.reopen_batch', $batch->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-light-success font-weight-bolder"
                                                            onclick="return confirm('{{ __('هل أنت متأكد من إعادة فتح رزمة القسم؟ ستُعاد فتح الرزمة الموحدة إن كانت مغلقة.') }}')">
                                                        <i class="fas fa-unlock"></i> {{ __('Reopen department batch') }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body pt-7">
                <div class="row mb-5 mt-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-custom stat-card shadow-sm h-100 border-top border-primary border-4">
                            <div class="card-body p-5">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted font-weight-bold font-size-sm">{{ __('Batch Date') }}</span>
                                        <h3 class="font-weight-bolder mt-2 mb-0 text-dark">
                                            {{ $batch->date?->format('Y-m-d') }}
                                        </h3>
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
                                            {{ $batch->sequence_number ?? '—' }}
                                        </h3>
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
                                            @if($batch->isOpen())
                                                <span class="label label-lg label-light-success label-inline font-weight-bold">
                                                    {{ __('Open') }}
                                                </span>
                                            @else
                                                <span class="label label-lg label-light-info label-inline font-weight-bold">
                                                    {{ __('Closed') }}
                                                </span>
                                            @endif
                                        </h3>
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
                                            {{ number_format((float) $batch->total_amount, 2) }} {{ __('NIS') }}
                                        </h3>
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
                                            {{ $batch->payments_count }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($batch->isUnified() && $childSectionBatches->isNotEmpty())
                    <div class="row mb-6">
                        <div class="col-lg-12">
                            <div class="card card-custom shadow-sm gutter-b">
                                <div class="card-header border-0 pt-6">
                                    <h3 class="card-title font-weight-bolder text-dark">{{ __('Department revenue batches') }}</h3>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-vertical-center">
                                            <thead>
                                                <tr class="text-muted font-weight-bolder text-uppercase">
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
                                                @foreach($childSectionBatches as $sb)
                                                    <tr>
                                                        <td>{{ $sb->id }}</td>
                                                        <td>{{ $sb->section->name ?? ('#' . $sb->section_id) }}</td>
                                                        <td>{{ $sb->sequence_number ?? '—' }}</td>
                                                        <td>
                                                            <span class="label label-inline {{ $sb->isOpen() ? 'label-light-success' : 'label-light-info' }}">{{ $sb->isOpen() ? __('Open') : __('Closed') }}</span>
                                                        </td>
                                                        <td>{{ number_format((float) $sb->total_amount, 2) }} {{ __('NIS') }}</td>
                                                        <td>{{ $sb->payments_count }}</td>
                                                        <td>
                                                            @can('central_treasury.manage')
                                                                @if($sb->isSectionBatch() && $sb->date && $sb->date->isToday() && ! $sb->isOpen())
                                                                    <form action="{{ route('central_treasury.reopen_batch', $sb) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-light-success">{{ __('Reopen') }}</button>
                                                                    </form>
                                                                @endif
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-12 mb-5">
                        <div class="card card-custom shadow-sm gutter-b">
                            <div class="card-header border-0 pt-7">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder text-dark">
                                        {{ __('Crafts and Industries Payments') }}
                                    </span>
                                    <span class="text-muted mt-3 font-weight-bold font-size-sm">
                                        {{ __('All craft license payments included in this batch') }}
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
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Notes') }}</th>
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
                                                    <td>{{ number_format((float) ($payment->balance_after ?? 0), 2) }}</td>
                                                    <td>{{ $payment->payment_method }}</td>
                                                    <td>{{ optional($payment->user)->name }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                    <td>
                                                        @if($payment->status === 'active')
                                                            <span class="label label-inline label-light-success font-weight-bold">
                                                                {{ __('Active') }}
                                                            </span>
                                                        @else
                                                            <span class="label label-inline label-light-danger font-weight-bold">
                                                                {{ __('Cancelled') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $payment->notes }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center py-10">
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
                                        {{ __('All engineering payments included in this batch') }}
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
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Notes') }}</th>
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
                                                    <td>{{ number_format((float) ($payment->balance_after ?? 0), 2) }}</td>
                                                    <td>{{ $payment->payment_method }}</td>
                                                    <td>{{ optional($payment->user)->name }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                                    <td>
                                                        @if($payment->status === 'active')
                                                            <span class="label label-inline label-light-success font-weight-bold">
                                                                {{ __('Active') }}
                                                            </span>
                                                        @else
                                                            <span class="label label-inline label-light-danger font-weight-bold">
                                                                {{ __('Cancelled') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $payment->notes }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center py-10">
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
                                        {{ __('All public pen request payments included in this batch') }}
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
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Notes') }}</th>
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
                                                    <td>
                                                        @if($payment->status === 'active')
                                                            <span class="label label-inline label-light-success font-weight-bold">
                                                                {{ __('Active') }}
                                                            </span>
                                                        @else
                                                            <span class="label label-inline label-light-danger font-weight-bold">
                                                                {{ __('Cancelled') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $payment->notes }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center py-10">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                                            <span class="text-muted font-weight-bold font-size-h5">
                                                                {{ __('No public pen request payments in this batch') }}
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

            </div>
        </div>
    </div>
@endsection

