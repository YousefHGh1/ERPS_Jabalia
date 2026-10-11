@extends('layout.master')

@section('title')
    {{ __('Shift') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('Shift') }}
@endsection

@section('content')
<div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('Shift') }}"
            subtitle="{{ __('Configure work shifts and attendance policies') }}"
        >
            <x-slot name="actions">
                <a href="javascript:;" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#createModalShift">
                                    <span class="svg-icon svg-icon-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1" />
                                            <rect fill="#000000" opacity="0.3" transform="rotate(-90 12 12)" x="4" y="11" width="16" height="2" rx="1" />
                                        </svg>
                                    </span>
                                    {{ __('Create Shift') }}
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
                            <th style="min-width: 150px">{{ __('name') }}</th>
                            <th style="min-width: 120px">{{ __('Timing') }}</th>
                            <th style="min-width: 200px">{{ __('Work Days') }}</th>
                            <th style="min-width: 100px">{{ __('Lateness') }}</th>
                            <th style="min-width: 150px">{{ __('Validity') }}</th>
                            <th class="text-right pr-0" style="min-width: 120px">{{ __('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($Shifts as $Shift)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 symbol-light-success mr-3">
                                        <span class="symbol-label">
                                            <i class="fas fa-clock text-success"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $Shift->name }}</a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark-75 font-weight-bold">{{ \Carbon\Carbon::parse($Shift->start_time)->format('h:i A') }}</span>
                                    <span class="text-muted font-size-sm">{{ \Carbon\Carbon::parse($Shift->end_time)->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $days = is_array($Shift->days) ? $Shift->days : json_decode($Shift->days, true);
                                    if(!is_array($days)) $days = [];
                                @endphp
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($days as $day)
                                        <span class="label label-sm label-light-primary label-inline font-weight-bold mb-1 mr-1">{{ __($day) }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-warning font-weight-bold">{{ $Shift->start_late }} {{ __('min') }} (In)</span>
                                    <span class="text-danger font-weight-bold">{{ $Shift->end_late }} {{ __('min') }} (Out)</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-muted font-size-sm italic">{{ $Shift->start_shift }}</span>
                                    <span class="text-muted font-size-sm italic">{{ $Shift->end_shift }}</span>
                                </div>
                            </td>
                            <td class="text-right pr-0">
                                <a href="javascript:;" class="btn btn-icon btn-light btn-hover-primary btn-sm mx-1" data-toggle="modal" data-target="#editModalShift{{ $Shift->id }}">
                                    <i class="fa fa-edit text-primary"></i>
                                </a>
                                <form action="{{ route('Shift.destroy', $Shift->id) }}" method="POST" class="d-inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-light btn-hover-danger btn-sm mx-1" onclick="return confirm('{{ __('delete_message') }}')">
                                        <i class="fa fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModalShift{{ $Shift->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title font-weight-bolder">{{ __('update Shift') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i aria-hidden="true" class="fas fa-times"></i></button>
                                    </div>
                                    <form action="{{ route('Shift.update', $Shift->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body py-10">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="font-weight-bold">{{ __('name') }}</label>
                                                        <input type="text" name="name" class="form-control form-control-solid" value="{{ old('name', $Shift->name) }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-5">
                                                        <label class="font-weight-bold">{{ __('start_time') }}</label>
                                                        <input type="time" name="start_time" class="form-control form-control-solid" value="{{ old('start_time', $Shift->start_time) }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-5">
                                                        <label class="font-weight-bold">{{ __('end_time') }}</label>
                                                        <input type="time" name="end_time" class="form-control form-control-solid" value="{{ old('end_time', $Shift->end_time) }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-5">
                                                        <label class="font-weight-bold d-block">{{ __('days') }}</label>
                                                        <div class="checkbox-inline mt-3">
                                                            @php $days = $Shift->days; @endphp
                                                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                                                <label class="checkbox checkbox-outline checkbox-success mr-5 mb-2">
                                                                    <input type="checkbox" name="days[]" value="{{ $day }}" {{ in_array($day, $days) ? 'checked' : '' }}>
                                                                    <span></span> {{ __($day) }}
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-5">
                                                        <label class="font-weight-bold">{{ __('start_late') }} ({{ __('min') }})</label>
                                                        <input type="number" name="start_late" class="form-control form-control-solid" value="{{ old('start_late', $Shift->start_late) }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-5">
                                                        <label class="font-weight-bold">{{ __('end_late') }} ({{ __('min') }})</label>
                                                        <input type="number" name="end_late" class="form-control form-control-solid" value="{{ old('end_late', $Shift->end_late) }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __('start_shift') }}</label>
                                                        <input type="date" name="start_shift" class="form-control form-control-solid" value="{{ old('start_shift', $Shift->start_shift) }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">{{ __('end_shift') }}</label>
                                                        <input type="date" name="end_shift" class="form-control form-control-solid" value="{{ old('end_shift', $Shift->end_shift) }}" required>
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
                        <tr><td colspan="6" class="text-center py-10 text-muted">{{ __('no_records') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-5">
                {!! $Shifts->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModalShift" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bolder">{{ __('Create Shift') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i aria-hidden="true" class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('Shift.store') }}" method="POST">
                @csrf
                <div class="modal-body py-10">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-5">
                                <label class="font-weight-bold">{{ __('name') }}</label>
                                <input type="text" name="name" class="form-control form-control-solid" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-5">
                                <label class="font-weight-bold">{{ __('start_time') }}</label>
                                <input type="time" name="start_time" class="form-control form-control-solid" value="{{ old('start_time') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-5">
                                <label class="font-weight-bold">{{ __('end_time') }}</label>
                                <input type="time" name="end_time" class="form-control form-control-solid" value="{{ old('end_time') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-5">
                                <label class="font-weight-bold">{{ __('start_shift') }}</label>
                                <input type="date" name="start_shift" class="form-control form-control-solid" value="{{ old('start_shift') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-5">
                                <label class="font-weight-bold">{{ __('end_shift') }}</label>
                                <input type="date" name="end_shift" class="form-control form-control-solid" value="{{ old('end_shift') }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-5">
                                <label class="font-weight-bold d-block">{{ __('days') }}</label>
                                <div class="checkbox-inline mt-3">
                                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <label class="checkbox checkbox-outline checkbox-success mr-5 mb-2">
                                            <input type="checkbox" name="days[]" value="{{ $day }}">
                                            <span></span> {{ __($day) }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-5">
                                <label class="font-weight-bold">{{ __('start_late') }} ({{ __('min') }})</label>
                                <input type="number" name="start_late" class="form-control form-control-solid" value="{{ old('start_late') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-5">
                                <label class="font-weight-bold">{{ __('end_late') }} ({{ __('min') }})</label>
                                <input type="number" name="end_late" class="form-control form-control-solid" value="{{ old('end_late') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('start_shift') }}</label>
                                <input type="date" name="start_shift" class="form-control form-control-solid" value="{{ old('start_shift') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('end_shift') }}</label>
                                <input type="date" name="end_shift" class="form-control form-control-solid" value="{{ old('end_shift') }}" required>
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