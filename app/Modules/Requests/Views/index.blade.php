@extends('layouts.app')

@section('title')
{{ __('Requests Management') }}

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">{{ __('Requests') }}</h1>
            <p class="text-muted">{{ __('Manage all citizen requests and workflows') }}</p>
        </div>
        <div class="col-md-6 text-end">
            @if (Auth::user()->can('create requests'))
                <a href="{{ route('requests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('New Request') }}
                </a>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">{{ $statistics['total'] ?? 0 }}</h4>
                            <p class="card-text">{{ __('Total Requests') }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">{{ $statistics['pending'] ?? 0 }}</h4>
                            <p class="card-text">{{ __('Pending') }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">{{ $statistics['urgent'] ?? 0 }}</h4>
                            <p class="card-text">{{ __('Urgent') }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">{{ $statistics['new_today'] ?? 0 }}</h4>
                            <p class="card-text">{{ __('New Today') }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('requests.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status" class="form-label">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">{{ __('All Statuses') }}</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $filters['status'] == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="request_type" class="form-label">{{ __('Request Type') }}</label>
                        <select name="request_type" id="request_type" class="form-select">
                            <option value="">{{ __('All Types') }}</option>
                            @foreach ($requestTypes as $type)
                                <option value="{{ $type->id }}" {{ $filters['request_type'] == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="priority" class="form-label">{{ __('Priority') }}</label>
                        <select name="priority" id="priority" class="form-select">
                            <option value="">{{ __('All Priorities') }}</option>
                            <option value="normal">{{ __('Normal') }}</option>
                            <option value="high">{{ __('High') }}</option>
                            <option value="urgent">{{ __('Urgent') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ $filters['search'] ?? '' }}" 
                               placeholder="{{ __('Search requests...') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                            {{ __('Search') }}
                        </button>
                        <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                            {{ __('Clear') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Sequence Number') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Citizen') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Priority') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $request->sequence_number }}</span>
                                </td>
                                <td>{{ $request->requestType->name ?? '-' }}</td>
                                <td>
                                    @if($request->mokalaf)
                                        {{ $request->mokalaf->name }}
                                        <small class="text-muted d-block">{{ $request->mokalaf->identity_number }}</small>
                                    @else
                                        {{ __('Not Assigned') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusColor($request->status) }}">
                                        {{ $this->getStatusName($request->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getPriorityColor($request->priority) }}">
                                        {{ ucfirst($request->priority) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('requests.show', $request->id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (Auth::user()->can('update requests'))
                                            <a href="{{ route('requests.edit', $request->id) }}" 
                                               class="btn btn-sm btn-outline-secondary"
                                               title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if (Auth::user()->can('delete requests'))
                                            <form action="{{ route('requests.destroy', $request->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="{{ __('Delete') }}"
                                                        onclick="return confirm('{{ __('Are you sure?') }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <h5>{{ __('No requests found') }}</h5>
                                        <p>{{ __('Try adjusting your search or filters') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if ($requests->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.table th {
    border-top: none;
    border-bottom: 2px solid #dee2e6;
}

.badge {
    font-size: 0.75rem;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
// Auto-refresh functionality
let refreshInterval;
let autoRefresh = false;

function toggleAutoRefresh() {
    autoRefresh = !autoRefresh;
    const button = document.getElementById('auto-refresh-btn');
    
    if (autoRefresh) {
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');
        button.innerHTML = '<i class="fas fa-pause"></i> ' + '{{ __('Pause Auto-Refresh') }}';
        startAutoRefresh();
    } else {
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
        button.innerHTML = '<i class="fas fa-sync"></i> ' + '{{ __('Auto-Refresh') }}';
        stopAutoRefresh();
    }
}

function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        window.location.reload();
    }, 30000); // Refresh every 30 seconds
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const autoRefreshBtn = document.getElementById('auto-refresh-btn');
    if (autoRefreshBtn) {
        autoRefreshBtn.addEventListener('click', toggleAutoRefresh);
    }
});
</script>
@endpush
