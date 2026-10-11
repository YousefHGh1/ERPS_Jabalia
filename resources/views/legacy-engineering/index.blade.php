@extends('layout.master')

@section('title')
    {{ __('Legacy Engineering Files') }}
@endsection

@section('page_title')
    {{ __('Legacy Engineering Files') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('Search legacy engineering and organization files') }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ __('Legacy Engineering Files') }}"
                subtitle="{{ __('Search legacy engineering and organization files') }}"
            >
                <x-slot name="actions">
                    <a href="#" class="btn btn-light-success font-weight-bolder" data-toggle="collapse"
                                            data-target="#collapseLegacySearch">
                                            <i class="fas fa-search"></i> {{ __('Search') }}
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body pt-7">
                <div id="collapseLegacySearch" class="collapse show mb-7">
                    <div class="card card-body bg-light-o-50 border-0 shadow-none p-8">
                        <form action="{{ route('legacy-engineering.index') }}" method="GET" class="form">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label
                                        class="font-weight-bold">{{ __('Search by name, address or file number') }}</label>
                                    <input name="search" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('Search here...') }}" value="{{ request('search') }}" />
                                </div>
                                <div class="col-md-2">
                                    <label class="font-weight-bold">{{ __('File Number') }}</label>
                                    <input name="file_number" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('File Number') }}" value="{{ request('file_number') }}" />
                                </div>
                                <div class="col-md-3">
                                    <label class="font-weight-bold">{{ __('Owner Name') }}</label>
                                    <input name="owner_name" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('Owner Name') }}" value="{{ request('owner_name') }}" />
                                </div>
                                {{-- <div class="col-md-2">
                                    <label class="font-weight-bold">{{ __('Status') }}</label>
                                    <input name="status" type="text" class="form-control form-control-solid"
                                        placeholder="{{ __('e.g. خالص') }}" value="{{ request('status') }}" />
                                </div> --}}
                                {{-- <div class="col-md-2">
                                    <label class="font-weight-bold">{{ __('Source') }}</label>
                                    <select name="type" class="form-control form-control-solid">
                                        <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>
                                            {{ __('All historical files') }}</option>
                                        <option value="crafts" {{ request('type') === 'crafts' ? 'selected' : '' }}>
                                            {{ __('Crafts') }}</option>
                                        <option value="engineering"
                                            {{ request('type') === 'engineering' ? 'selected' : '' }}>
                                            {{ __('Engineering and organization') }}</option>
                                    </select>
                                </div> --}}
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary font-weight-bolder px-8">
                                        <i class="fas fa-search"></i> {{ __('Search') }}
                                    </button>
                                    <a href="{{ route('legacy-engineering.index') }}"
                                        class="btn btn-light-primary font-weight-bolder px-8 ml-2">
                                        <i class="fas fa-sync-alt"></i> {{ __('Reset') }}
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
                                <th>{{ __('Owner') }}</th>
                                <th>{{ __('Address') }}</th>
                                <th>{{ __('Piece / Area') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Total Amount') }}</th>
                                <th class="text-right" style="width: 100px;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($files as $file)
                                <tr>
                                    <td class="font-weight-bolder" data-label="{{ __('File Number') }}">
                                        {{ $file->FILE_NO ?? '—' }}
                                        @if ($file->FILE_YEAR)
                                            <span class="text-muted">/ {{ $file->FILE_YEAR }}</span>
                                        @endif
                                    </td>
                                    <td data-label="{{ __('Owner') }}">
                                        <div class="d-flex flex-column">
                                            <span
                                                class="text-dark-75 font-weight-bolder">{{ $file->full_name_display }}</span>
                                            @if ($file->ID_NO)
                                                <span class="text-muted font-size-sm">{{ $file->ID_NO }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="{{ __('Address') }}">
                                        <span
                                            class="text-dark-75">{{ Str::limit($file->ADDRESS ?? ($file->STREET ?? '—'), 40) }}</span>
                                    </td>
                                    <td data-label="{{ __('Piece / Area') }}">
                                        @if ($file->PIECE || $file->AREA)
                                            قسيمة: {{ $file->PIECE ?? '—' }} / مساحة: {{ $file->AREA ?? '—' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td data-label="{{ __('Status') }}">
                                        <span
                                            class="label label-inline label-light-info font-weight-bold">{{ $file->FILE_STATUSE ?? '—' }}</span>
                                    </td>
                                    <td data-label="{{ __('Total Amount') }}">
                                        @if ($file->TOTAL_AMT !== null)
                                            {{ number_format((float) $file->TOTAL_AMT, 2) }} ₪
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-right pr-0" data-label="{{ __('Actions') }}">
                                        <a href="{{ route('legacy-engineering.show', $file->ID) }}"
                                            class="btn btn-icon btn-light-info btn-sm" title="{{ __('View') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-box-open icon-4x text-muted mb-5"></i>
                                            <span
                                                class="text-muted font-weight-bold font-size-h5">{{ __('no_records') }}</span>
                                            <span
                                                class="text-muted font-size-sm mt-2">{{ __('Legacy data is imported from master2026_mysql.sql into table MASTER') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {!! $files->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
@endsection
