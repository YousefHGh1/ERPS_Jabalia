{{-- بطاقة موظف - للقوائم والمديرين --}}
@props([
    'employee' => null,
    'subtitle' => null,
    'showDepartment' => true,
    'showJob' => true,
    'editModalTarget' => null,
    'destroyRoute' => null,
    'showLink' => true,
])

@if($employee)
<div class="card admin-structure-card admin-employee-card h-100">
    <div class="card-body position-relative">
        <div class="d-flex align-items-start">
            <div class="symbol symbol-50 symbol-light-primary flex-shrink-0 ml-3">
                <span class="symbol-label font-weight-bolder font-size-h6 text-primary">{{ mb_substr($employee->name ?? '', 0, 1) }}</span>
            </div>
            <div class="flex-grow-1 min-w-0">
                <h5 class="card-title font-weight-bolder text-dark mb-1">
                    @if($showLink && $employee->id)
                    <a href="{{ route('employees.show', $employee) }}" class="text-dark text-hover-primary">{{ $employee->name }}</a>
                    @else
                    <span>{{ $employee->name }}</span>
                    @endif
                </h5>
                @if($subtitle)
                <p class="text-muted font-size-sm mb-1">{{ $subtitle }}</p>
                @endif
                @if($showDepartment && isset($employee->department))
                <p class="text-muted font-size-sm mb-0">
                    <i class="fas fa-building ml-1"></i> {{ $employee->department?->name ?? '---' }}
                    @if(isset($employee->section) && $employee->section)
                    <span class="text-muted"> / {{ $employee->section->name }}</span>
                    @endif
                </p>
                @endif
                @if($showJob && isset($employee->job))
                <p class="text-muted font-size-sm mb-0">
                    <i class="fas fa-briefcase ml-1"></i> {{ $employee->job?->name ?? '---' }}
                </p>
                @endif
            </div>
            @if($editModalTarget || $destroyRoute)
            <div class="admin-card-actions flex-shrink-0">
                @if($editModalTarget)
                <button type="button" class="btn btn-icon btn-sm btn-light-primary" data-toggle="modal" data-target="#{{ $editModalTarget }}" title="{{ __('Edit') }}">
                    <i class="fas fa-edit"></i>
                </button>
                @endif
                @if($destroyRoute)
                <form action="{{ $destroyRoute }}" method="POST" class="d-inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-icon btn-sm btn-light-danger" onclick="return confirm('{{ __('confirm_message') }}')" title="{{ __('Delete') }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endif
