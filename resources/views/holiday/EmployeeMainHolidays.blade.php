@extends('layout.master')

@section('title')
    {{ __('main_holiday') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('main_holiday') }}
@endsection

@section('content')
<div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('main_holiday') }}"
            subtitle="{{ __('Manage official and recurring municipality holidays') }}"
        >
            <x-slot name="actions">
                <a href="javascript:;" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#createModal">
                                    <span class="svg-icon svg-icon-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1" />
                                            <rect fill="#000000" opacity="0.3" transform="rotate(-90 12 12)" x="4" y="11" width="16" height="2" rx="1" />
                                        </svg>
                                    </span>
                                    {{ __('انشاء اجازات الموظف الرسمية ') }}
                                </a>
            </x-slot>
        </x-page-header>
    <!-- Main Table Card -->
    <div class="card card-custom shadow-sm border-0">

        <div class="card-body pt-7">
            <div class="table-responsive">
                <table class="table table-head-custom table-vertical-center">
                    <thead>
                        <tr class="text-left text-uppercase">
                            <th class="pl-0" style="width: 50px">#</th>
                            <th style="min-width: 250px">{{ __('holiday_name') }}</th>
                            <th style="min-width: 150px">{{ __('start_date') }}</th>
                            <th style="min-width: 150px">{{ __('end_date') }}</th>
                            <th class="text-right pr-0" style="min-width: 150px">{{ __('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidaies as $holiday)
                        <tr>
                            <td class="pl-0 font-weight-bolder text-muted">{{ $holiday->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 symbol-light-primary mr-3">
                                        <span class="symbol-label">
                                            <i class="fas fa-calendar text-primary"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $holiday->holiday_name }}</a>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $holiday->start_date }}</span></td>
                            <td><span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $holiday->end_date }}</span></td>
                            <td class="text-right pr-0">
                                <a href="javascript:;" class="btn btn-icon btn-light btn-hover-primary btn-sm mx-1" data-toggle="modal" data-target="#editModal{{ $holiday->id }}">
                                    <i class="fa fa-edit text-primary"></i>
                                </a>
                                <form action="{{ route('main_holiday.destroy', $holiday->id) }}" method="POST" class="d-inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-light btn-hover-danger btn-sm mx-1" onclick="return confirm('{{ __('delete_message') }}')">
                                        <i class="fa fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $holiday->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title font-weight-bolder">{{ __('update main_holiday') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <i aria-hidden="true" class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <form action="{{ route('main_holiday.update', $holiday->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body py-10">
                                            <div class="form-group mb-5">
                                                <label class="font-weight-bold">{{ __('holiday_name') }}</label>
                                                <input type="text" name="holiday_name" class="form-control form-control-solid @error('holiday_name') is-invalid @enderror" value="{{ old('holiday_name', $holiday->holiday_name) }}" required>
                                                @error('holiday_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __('start_date') }}</label>
                                                        <input type="date" name="start_date" class="form-control form-control-solid @error('start_date') is-invalid @enderror" value="{{ old('start_date', $holiday->start_date) }}" required>
                                                        @error('start_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __('end_date') }}</label>
                                                        <input type="date" name="end_date" class="form-control form-control-solid @error('end_date') is-invalid @enderror" value="{{ old('end_date', $holiday->end_date) }}" required>
                                                        @error('end_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                                            <button type="submit" class="btn btn-primary font-weight-bold">{{ __('Save Changes') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-calendar display-4 text-muted"></i>
                                    <span class="mt-3 font-weight-bold">{{ __('no_records') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-5">
                {!! $holidaies->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bolder">{{ __('Create main_holiday') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('main_holiday.store') }}" method="POST">
                @csrf
                <div class="modal-body py-10">
                    <div class="form-group mb-5">
                        <label class="font-weight-bold">{{ __('holiday_name') }}</label>
                        <input type="text" name="holiday_name" class="form-control form-control-solid @error('holiday_name') is-invalid @enderror" value="{{ old('holiday_name') }}" required>
                        @error('holiday_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('start_date') }}</label>
                                <input type="date" name="start_date" class="form-control form-control-solid @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                                @error('start_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('end_date') }}</label>
                                <input type="date" name="end_date" class="form-control form-control-solid @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                                @error('end_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
@endsection
