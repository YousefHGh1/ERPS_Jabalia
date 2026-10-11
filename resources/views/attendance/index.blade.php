@extends('layout.master')

@section('title')
{{ __('attendance') }}
@endsection

@section('page_title')
{{ __('controll_panel') }}
@endsection
@section('sub_main')
{{ __('home') }}
@endsection
@section('sub_title')
{{ __('attendance') }}
@endsection

@section('content')
<div class="container-fluid">
        {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
        <x-page-header
            title="{{ __('attendance') }}"
            subtitle="{{ __('Real-time log of employee fingerprint activity') }}"
        >
            <x-slot name="actions">
                <div class="dropdown dropdown-inline mr-2">
                                    <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-download"></i>{{ __('Export') }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        <ul class="nav nav-hover flex-column">
                                            <li class="nav-header font-weight-bolder text-uppercase text-primary pb-2">{{ __('Choose Plan') }}:</li>
                                            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon la la-file-pdf-o"></i><span class="nav-text">PDF</span></a></li>
                                            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon la la-file-excel-o"></i><span class="nav-text">Excel</span></a></li>
                                        </ul>
                                    </div>
                                </div>
            </x-slot>
        </x-page-header>
    <!-- Main Table Card -->
    <div class="card card-custom shadow-sm border-0">

        <div class="card-body pt-7">
            @can('index bonus') {{-- Assuming this permission is used for attendance too as per original code --}}
            <div class="table-responsive">
                <table class="table table-head-custom table-vertical-center">
                    <thead>
                        <tr class="text-left text-uppercase">
                            <th class="pl-0" style="min-width: 200px">{{ __('employee') }}</th>
                            <th style="min-width: 150px">{{ __('arrival_time') }}</th>
                            <th style="min-width: 150px">{{ __('Type') }}</th>
                            <th style="min-width: 100px">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="pl-0">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 symbol-light-success mr-3">
                                        <span class="symbol-label font-size-h5 font-weight-bold text-success">{{ substr($log->employee->employee->name ?? '?', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $log->employee->employee->name ?? 'N/A' }}</span>
                                        <span class="text-muted font-weight-bold">ID: {{ $log->employee->employee->employee_number ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark-75 font-weight-bold d-block">{{ \Carbon\Carbon::parse($log->arrival_time)->format('Y-m-d') }}</span>
                                <span class="text-muted font-weight-bold">{{ \Carbon\Carbon::parse($log->arrival_time)->format('h:i A') }}</span>
                            </td>
                            <td>
                                @php
                                    $types = [
                                        0 => ['text' => 'دخول', 'color' => 'success'],
                                        1 => ['text' => 'خروج', 'color' => 'danger'],
                                        2 => ['text' => 'اذن خروج', 'color' => 'warning'],
                                        3 => ['text' => 'عودة اذن خروج', 'color' => 'info'],
                                        4 => ['text' => 'بداية وقت إضافي', 'color' => 'primary'],
                                        5 => ['text' => 'نهاية وقت إضافي', 'color' => 'dark']
                                    ];
                                    $type = $types[$log->type] ?? ['text' => 'غير معروف', 'color' => 'secondary'];
                                @endphp
                                <span class="label label-lg label-light-{{ $type['color'] }} label-inline font-weight-bold">{{ $type['text'] }}</span>
                            </td>
                            <td>
                                <span class="text-success font-weight-bold"><i class="fa fa-check-circle text-success mr-1"></i> {{ __('Verified') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-10">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-list display-4 text-muted"></i>
                                    <span class="text-muted mt-3 font-weight-bold">{{ __('no_records') }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap mt-5">
                @if ($logs->links())
                    {!! $logs->withQueryString()->links('pagination::bootstrap-5') !!}
                @endif
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!--begin::Page Scripts(used by this page)-->
<script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
<!--end::Page Scripts-->
@endsection