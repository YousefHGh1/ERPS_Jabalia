<table class="table table-head-custom table-vertical-center table-hover">
    <thead>
        <tr class="text-left text-muted font-weight-bolder text-uppercase">
            <th class="pl-0" style="width: 15px">#</th>
            <th style="min-width: 250px">{{ __('employee') }}</th>
            <th style="min-width: 200px">{{ __('department') }}</th>
            <th style="min-width: 200px">{{ __('job') }}</th>
            <th style="min-width: 200px">{{ __('current_job') }}</th>
            {{-- <th style="min-width: 200px">{{ __('address') }}</th>
            <th style="min-width: 200px">{{ __('birthplace') }}</th> --}}
            <th class="text-right" style="min-width: 120px">{{ __('action') }}</th>
        </tr>
    </thead>
    <tbody>
  
        @forelse($employees as $employee)
            <tr>
                <td class="pl-0 py-5">
                    <span class="text-muted font-weight-bold">{{ $employee->id }}</span>
                </td>
                <td class="pl-0">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50 symbol-light-primary mr-4">
                            <span class="symbol-label">
                                <span
                                    class="font-size-h5 font-weight-boldest text-primary">{{ mb_substr($employee->name, 0, 1) }}</span>
                            </span>
                        </div>
                        <div>
                            @can('index employees')
                                <a href="{{ route('employees.show', $employee) }}"
                                    class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $employee->name }}</a>
                            @else
                                <span class="text-dark-75 font-weight-bolder mb-1 font-size-lg">{{ $employee->name }}</span>
                            @endcan
                            <span class="text-muted font-weight-bold d-block">ID: {{ $employee->czn_id }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span
                        class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $employee->department?->name ?? '---' }}</span>
                    <span class="text-muted font-weight-bold">{{ $employee->section?->name }}</span>
                </td>
                <td>
                    <span
                        class="label label-lg label-light-info label-inline font-weight-bold">{{ $employee->job?->name ?? '---' }}</span>
                </td>
                <td>
                    <span
                        class="label label-lg label-light-success label-inline font-weight-bold">{{ $employee->EmployeeJobInfo?->current_job ?? '---' }}</span>
                </td>
                {{-- <td>
                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $employee->address ?? '---' }}</span>
                </td>
                <td>
                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $employee->birthplace ?? '---' }}</span>
                </td> --}}
                <td class="text-right pr-0">
                    @can('update employees')
                        <a href="javascript:;" class="btn btn-icon btn-light-primary btn-sm mx-1" data-toggle="modal"
                            data-target="#editModal{{ $employee->id }}" title="{{ __('Edit') }}">
                            <i class="fas fa-pen"></i>
                        </a>
                    @endcan
                    @can('delete employees')
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="post"
                            class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-icon btn-light-danger btn-sm mx-1"
                                onclick="return confirm('{{ __('confirm_message') }}')" title="{{ __('Delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-10">
                    <div class="empty-state">
                        <div class="symbol symbol-100 symbol-light-primary mb-5">
                            <span class="symbol-label">
                                <i class="fas fa-box-open icon-4x text-primary"></i>
                            </span>
                        </div>
                        <p class="empty-state-text">{{ __('no_records') }}</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-5">
    {!! $employees->withQueryString()->links('pagination::bootstrap-5') !!}
</div>
