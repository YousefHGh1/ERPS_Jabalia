<div class="row" id="employees-cards">
    @forelse($employees as $employee)
        <div class="col-lg-6 col-xl-4">
            <div class="card card-custom gutter-b shadow-sm border-0 mb-6">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-5">
                        <div>
                            <h4 class="card-title font-weight-bolder mb-1">
                                @can('index employees')
                                    <a href="{{ route('employees.show', $employee) }}"
                                        class="text-dark text-hover-primary text-decoration-none">
                                        {{ $employee->name }}
                                    </a>
                                @else
                                    <span class="text-dark text-decoration-none">
                                        {{ $employee->name }}
                                    </span>
                                @endcan
                            </h4>
                            <p class="text-muted mb-2">
                                {{ $employee->department?->name ?? '---' }}
                                @if ($employee->section?->name)
                                    - {{ $employee->section?->name }}
                                @endif
                            </p>
                        </div>

                        <span class="badge badge-secondary badge-pill font-weight-bold">#{{ $employee->id }}</span>
                    </div>

                    <div class="mb-4">
                        <div class="font-weight-bold text-dark mb-2">{{ __('job') }}</div>
                        <span class="label label-lg label-light-info label-inline font-weight-bold">
                            {{ $employee->job?->name ?? '---' }}
                        </span>
                    </div>

                    <div class="mb-2">
                        <span class="label label-lg label-light-success label-inline font-weight-bold">
                            {{ $employee->EmployeeJobInfo?->current_job ?? '---' }}
                        </span>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center border-0 pt-0">
                    <div>
                        @can('update employees')
                            <a href="javascript:;" class="btn btn-light-info btn-sm font-weight-bold mr-2"
                                data-toggle="modal" data-target="#editModal{{ $employee->id }}"
                                title="{{ __('Edit') }}">
                                <i class="fas fa-pen"></i> {{ __('Edit') }}
                            </a>
                        @endcan

                        @can('delete employees')
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light-danger btn-sm font-weight-bold delete-confirm"
                                    onclick="return confirm('{{ __('confirm_message') }}')" title="{{ __('Delete') }}">
                                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="d-flex flex-column align-items-center py-20">
                <div class="symbol symbol-100 symbol-light-primary mb-5">
                    <span class="symbol-label">
                        <i class="fas fa-box-open icon-4x text-primary"></i>
                    </span>
                </div>
                <p class="text-muted font-size-h5 font-weight-bold">{{ __('no_records') }}</p>
            </div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
    {!! $employees->withQueryString()->links('pagination::bootstrap-5') !!}
</div>
