@extends('layout.master')

@section('title')
    {{ __('Engineering Files') }}
@endsection

@section('page_title')
    {{ __('Engineering Files') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('List Engineering Files') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('Engineering Files') }}"
                subtitle="{{ __('Manage engineering files and fees') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder mr-2" data-toggle="collapse"
                                            data-target="#collapseSearch">
                                            <i class="fas fa-search"></i> {{ __('Search') }}
                                        </a>
                                        <a href="{{ route('engineering.create') }}" class="btn btn-primary font-weight-bolder">
                                            <i class="fas fa-plus"></i> {{ __('Create') }}
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body pt-7">
                <div id="collapseSearch" class="collapse mb-7">
                    <div class="card card-body bg-light-o-50 border-0 shadow-none p-8">
                        <form action="{{ route('engineering.index') }}" method="GET" class="form">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="font-weight-bold">{{ __('Search by name or file number') }}</label>
                                    <input name="search" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('Search here...') }}" value="{{ request('search') }}" />
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-bold">{{ __('File Number') }}</label>
                                    <input name="file_number" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('File Number') }}" value="{{ request('file_number') }}" />
                                </div>
                                <div class="col-md-5">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                                        <i class="fas fa-search"></i> {{ __('Search') }}
                                    </button>
                                    <a href="{{ route('engineering.index') }}"
                                        class="btn btn-light-primary font-weight-bolder px-8 ml-2">
                                        <i class="fas fa-sync-alt"></i> {{ __('Reset') }}
                                    </a>
                                    <a href="{{ route('engineering.export', request()->all()) }}"
                                        class="btn btn-success font-weight-bolder px-8 ml-2">
                                        <i class="fa fa-file-csv"></i> {{ __('Export Data') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive" data-responsive="true">
                    <table class="table table-head-custom table-vertical-center table-borderless">
                        <thead>
                            <tr class="text-left text-muted font-weight-bolder text-uppercase">
                                <th>{{ __('File Number') }}</th>
                                <th>{{ __('Owner / ID') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Building') }}</th>
                                <th>{{ __('Financial (GIS/License/Total)') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th class="text-danger">{{ __('Remaining') }}</th>
                                <th class="text-right" style="width: 150px;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($engineerings as $engineering)
                                <tr>
                                    <td class="font-weight-bolder" data-label="{{ __('File Number') }}">{{ $engineering->file_number }} /
                                        {{ $engineering->file_year }}</td>
                                    <td data-label="{{ __('Owner / ID') }}">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark-75 font-weight-bolder font-size-lg">
                                                {{ $engineering->first_name }} {{ $engineering->father_name }}
                                                {{ $engineering->family_name }}
                                            </span>
                                            <span
                                                class="text-muted font-weight-bold">{{ $engineering->national_id }}</span>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('Location') }}">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark-75 font-weight-bold">قسيمة:
                                                {{ $engineering->plot_number }} / قطعة:
                                                {{ $engineering->parcel_number }}</span>
                                            <span class="text-muted font-size-sm">{{ $engineering->neighborhood }}</span>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('Building') }}">
                                        <div class="d-flex flex-column">
                                            <span
                                                class="text-dark-75 font-weight-bold">{{ $engineering->building_type }}</span>
                                            <span class="text-muted font-size-sm">طوابق: {{ is_array($engineering->floors_count) ? implode(', ', $engineering->floors_count) : $engineering->floors_count }}
                                               </span>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('Financial (GIS/License/Total)') }}">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark-75 font-weight-bold">رسوم GIS:
                                                {{ number_format($engineering->gis_fees, 2) }}</span>
                                            <span class="text-muted font-size-sm">رسوم الترخيص:
                                                {{ number_format($engineering->license_fees, 2) }} | الإجمالي:
                                                {{ number_format($engineering->total_amount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-success font-weight-bolder" data-label="{{ __('Paid') }}">
                                        {{ number_format($engineering->paid_amount, 2) }} ₪</td>
                                    <td class="text-danger font-weight-bolder" data-label="{{ __('Remaining') }}">
                                        {{ number_format($engineering->remaining_amount, 2) }}
                                        ₪</td>
                                    <td class="text-right pr-0 table-actions" data-label="{{ __('Actions') }}">
                                        <a href="{{ route('engineering.edit', $engineering->id) }}"
                                            class="btn btn-icon btn-light-primary btn-sm mx-1" title="{{ __('Edit') }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="{{ route('engineering.print', $engineering->id) }}" target="_blank"
                                            class="btn btn-icon btn-light-success btn-sm mx-1" title="{{ __('Print') }}">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <form action="{{ route('engineering.destroy', $engineering->id) }}" method="POST"
                                            class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-light-danger btn-sm mx-1"
                                                title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                            <span
                                                class="text-muted font-weight-bold font-size-h5">{{ __('no_records') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {!! $engineerings->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('form.d-inline-block').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this record?')) {
                    this.submit();
                }
            });
        });
    </script>
@endsection
