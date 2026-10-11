@extends('layout.master')

@section('title')
    {{ __('EmployeeHolidays') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('EmployeeHolidays') }}
@endsection

@section('content')
<div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('EmployeeHolidays') }}"
            subtitle="{{ __('Review and process employee holiday requests') }}"
        >
            <x-slot name="actions">
                <div class="dropdown dropdown-inline mr-2">
                                    <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-eye"></i>{{ __('Filter Status') }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        <ul class="nav nav-hover flex-column">
                                            <li class="nav-item"><a href="{{ URL::current() }}" class="nav-link"><span class="nav-text">{{ __('All') }}</span></a></li>
                                            <li class="nav-item"><a href="{{ URL::current() }}?status=pending" class="nav-link"><span class="nav-text text-warning">{{ __('Pending') }}</span></a></li>
                                            <li class="nav-item"><a href="{{ URL::current() }}?status=accepted" class="nav-link"><span class="nav-text text-success">{{ __('Accepted') }}</span></a></li>
                                            <li class="nav-item"><a href="{{ URL::current() }}?status=rejected" class="nav-link"><span class="nav-text text-danger">{{ __('Rejected') }}</span></a></li>
                                        </ul>
                                    </div>
                                </div>
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
                            <th style="min-width: 200px">{{ __('employee') }}</th>
                            <th style="min-width: 150px">{{ __('holiday_name') }}</th>
                            <th style="min-width: 120px">{{ __('start_date') }}</th>
                            <th style="min-width: 120px">{{ __('end_date') }}</th>
                            <th style="min-width: 120px">{{ __('status') }}</th>
                            <th class="text-right pr-0" style="min-width: 120px">{{ __('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeeholidays as $employeeholiday)
                        <tr>
                            <td class="pl-0 font-weight-bolder text-muted">{{ $employeeholiday->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 symbol-light-primary mr-3">
                                        <span class="symbol-label font-size-h5 font-weight-bold">{{ substr($employeeholiday->employee->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $employeeholiday->employee->name }}</a>
                                        <span class="text-muted font-weight-bold d-block">{{ $employeeholiday->employee->employee_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark-75 font-weight-bold d-block">{{ $employeeholiday->holiday->name }}</span>
                            </td>
                            <td>
                                <span class="text-muted font-weight-bold">{{ $employeeholiday->start_date }}</span>
                            </td>
                            <td>
                                <span class="text-muted font-weight-bold">{{ $employeeholiday->end_date }}</span>
                            </td>
                            <td>
                                @php
                                    $statusMap = [
                                        'pending' => ['color' => 'warning', 'icon' => 'fa-clock'],
                                        'accepted' => ['color' => 'success', 'icon' => 'fa-check'],
                                        'rejected' => ['color' => 'danger', 'icon' => 'fa-times']
                                    ];
                                    $status = $statusMap[$employeeholiday->status] ?? ['color' => 'secondary', 'icon' => 'fa-question'];
                                @endphp
                                <span class="label label-lg label-light-{{ $status['color'] }} label-inline font-weight-bold">
                                    <i class="fa {{ $status['icon'] }} mr-1 font-size-xs text-{{ $status['color'] }}"></i>
                                    {{ __($employeeholiday->status) }}
                                </span>
                            </td>
                            <td class="text-right pr-0">
                                @if ($employeeholiday->status == 'pending')
                                <div class="d-flex justify-content-end">
                                    <form action="{{ route('acceptHoliday', $employeeholiday->id) }}" method="POST" class="d-inline mx-1">
                                        @csrf
                                        <button type="submit" class="btn btn-icon btn-light-success btn-hover-success btn-sm" title="{{ __('Accept') }}" onclick="return confirm('{{ __('confirm_message') }}')">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('rejectHoliday', $employeeholiday->id) }}" method="POST" class="d-inline mx-1">
                                        @csrf
                                        <button type="submit" class="btn btn-icon btn-light-danger btn-hover-danger btn-sm" title="{{ __('Reject') }}" onclick="return confirm('{{ __('confirm_message') }}')">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span class="text-muted font-size-sm italic">{{ __('Processed') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-calendar display-4 text-muted"></i>
                                    <span class="text-muted mt-3 font-weight-bold">{{ __('no_records') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-5">
                {!! $employeeholidays->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
@endsection
