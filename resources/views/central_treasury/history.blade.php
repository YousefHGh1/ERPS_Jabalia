@extends('layout.master')

@section('title')
    {{ __('Central Treasury') }} - {{ __('History') }}
@endsection

@section('page_title')
    {{ __('Central Treasury') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('Central Treasury History') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('Central Treasury Closed Batches') }}"
                subtitle="{{ __('View closed revenue batches by date (read-only)') }}"
            ></x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body pt-7">
                <div class="table-responsive">
                    <table class="table table-head-custom table-vertical-center table-borderless">
                        <thead>
                            <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                <th>#</th>
                                <th>{{ __('Batch Number') }}</th>
                                <th>{{ __('Batch Date') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('Total Revenue') }}</th>
                                <th>{{ __('Payments Count') }}</th>
                                <th>{{ __('Opened At') }}</th>
                                <th>{{ __('Closed At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                                @forelse($batches as $batch)
                                <tr>
                                    <td>{{ $batch->id }}</td>
                                    <td>{{ $batch->sequence_number ?? '—' }}</td>
                                    <td>{{ $batch->date?->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="label label-inline label-light-info font-weight-bold">
                                            {{ __('Closed') }}
                                        </span>
                                    </td>
                                    <td>{{ number_format((float) $batch->total_amount, 2) }} {{ __('NIS') }}</td>
                                    <td>{{ $batch->payments_count }}</td>
                                    <td>{{ optional($batch->opened_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ optional($batch->closed_at)->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('central_treasury.show_batch', $batch->id) }}"
                                           class="btn btn-sm btn-light-primary font-weight-bold">
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                            <span class="text-muted font-weight-bold font-size-h5">
                                                {{ __('No closed batches found') }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $batches->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

