@extends('layout.master')

@section('title')
    {{ __('employee reports') }}
@endsection

@section('page_title')
    {{ __('employee reports') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('employee reports') }}
@endsection

@section('content')
    <div class="container">
        <div class="card card-custom gutter-b">
            <div class="card-header border-0 py-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label font-weight-bolder text-dark">{{ __('employee reports') }}</span>
                </h3>
            </div>

            <div class="card-body">
                <form class="form mb-15" action="{{ route('getEmployeeReport') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('employee_id') }}</label>
                                <select class="form-control selectpicker" data-live-search="true" name="employee_id"
                                    id="employee_id">
                                    <option value="all">كل الموظفين</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ isset($employee) && $employee->id == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('report type') }}</label>
                                <select class="form-control" name="report_type" id="report_type">
                                    <option value="late_report">{{ __('late report') }}</option>
                                    <option value="over_time_report">{{ __('over time report') }}</option>
                                    <option value="absence_report">{{ __('absence report') }}</option>
                                    <option value="dept_summary">خلاصة حضور الدوائر</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ __('start_date') }}</label>
                                <input class="form-control" type="date" name="start_date"
                                    value="{{ request('start_date', date('Y-m-01')) }}" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ __('end_date') }}</label>
                                <input class="form-control" type="date" name="end_date"
                                    value="{{ request('end_date', date('Y-m-d')) }}" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="submit"
                                class="btn btn-primary font-weight-bold btn-block">{{ __('Generate') }}</button>
                        </div>
                    </div>
                </form>

                @if (isset($late_employee) || isset($late_array))
                    <div class="separator separator-dashed my-10"></div>

                    <div class="d-flex justify-content-end mb-5">
                        <button onclick="window.print()" class="btn btn-light-primary font-weight-bold mr-2">
                            <i class="fa fa-print"></i> {{ __('Print') }}
                        </button>
                        <button id="exportExcel" class="btn btn-light-success font-weight-bold">
                            <i class="fa fa-file-excel"></i> {{ __('Export Excel') }}
                        </button>
                    </div>
                @endif

                @if (isset($dept_summary))
                    <div class="table-responsive">
                        <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                            <thead>
                                <tr class="text-uppercase">
                                    <th>{{ __('department') }}</th>
                                    <th>عدد الموظفين</th>
                                    <th>إجمالي ساعات التأخير</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dept_summary as $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['emp_count'] }}</td>
                                        <td class="text-danger font-weight-bold">{{ $item['total_late_hours'] }} ساعة</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if (isset($late_employee))
                <div class="table-responsive">
                    <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                        <thead>
                            <tr class="text-uppercase">
                                <th style="min-width: 250px">{{ __('name') }}</th>
                                <th style="min-width: 100px">{{ __('emp_number') }}</th>
                                <th style="min-width: 100px">{{ __('total_lates') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($late_employee as $late)
                                <tr>
                                    <td>{{ $late->name }}</td>
                                    <td>{{ $late->EmployeeJobInfo ? $late->EmployeeJobInfo->emp_number : '-' }}</td>
                                    <td>{{ $late->total_lates }}</td>

                                </tr>
                            @empty
                            @endforelse


                        </tbody>
                    </table>


                </div>
                @endif
                @if (isset($late_array) && $late_array != null)
                    <div class="table-responsive">
                        <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                            <thead>
                                <tr class="text-uppercase">
                                    <th style="min-width: 250px">{{ __('name') }}</th>
                                    <th style="min-width: 100px">{{ __('emp_number') }}</th>
                                    <th style="min-width: 100px">{{ __('arrival_time') }}</th>
                                    <th style="min-width: 100px">{{ __('late_minute') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($late_array as $late)
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->EmployeeJobInfo->emp_number }}</td>
                                        <td>{{ $late->arrival_time }}</td>
                                        <td>{{ $late->minutesDifference }}</td>
                                    </tr>
                                @empty
                                @endforelse

                            </tbody>
                        </table>


                    </div>
                @endif
                <!--end::Table-->
            </div>
            <!--end::Body-->
        </div>
    </div>
    <!--end::Advance Table Widget 3-->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#exportExcel').on('click', function() {
                let table = document.querySelector('table');
                let html = table.outerHTML;
                let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
                let link = document.createElement('a');
                link.download = 'report.xls';
                link.href = url;
                link.click();
            });
        });
    </script>
@endsection
