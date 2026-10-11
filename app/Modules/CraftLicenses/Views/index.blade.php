@extends('layouts.app')

@section('title')
{{ __('Craft Licenses Management') }}

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">{{ __('Craft Licenses') }}</h1>
            <p class="text-muted">{{ __('Manage all craft licenses and payments') }}</p>
        </div>
        <div class="col-md-6 text-end">
            @if (Auth::user()->can('create craft licenses'))
                <a href="{{ route('craftlicenses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('New License') }}
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
                            <p class="card-text">{{ __('Total Licenses') }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-certificate fa-2x"></i>
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
                            <h4 class="card-title mb-0">{{ $statistics['active'] ?? 0 }}</h4>
                            <p class="card-text">{{ __('Active') }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="card-title mb-0">{{ $statistics['expired'] ?? 0 }}</h4>
                            <p class="card-text">{{ __('Expired') }}</p>
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
                            <h4 class="card-title mb-0">{{ $statistics['revenue'] ?? 0 }}</h4>
                            <p class="card-text">{{ __('Revenue') }}</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('craftlicenses.index') }}">
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
                        <label for="craft_type" class="form-label">{{ __('Craft Type') }}</label>
                        <select name="craft_type" id="craft_type" class="form-select">
                            <option value="">{{ __('All Types') }}</option>
                            @foreach ($craftTypes as $type => $label)
                                <option value="{{ $type }}" {{ $filters['craft_type'] == $type ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="owner_name" class="form-label">{{ __('Owner Name') }}</label>
                        <input type="text" name="owner_name" id="owner_name" class="form-control" 
                               value="{{ $filters['owner_name'] ?? '' }}" 
                               placeholder="{{ __('Search by owner name...') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ $filters['search'] ?? '' }}" 
                               placeholder="{{ __('Search licenses...') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                            {{ __('Search') }}
                        </button>
                        <a href="{{ route('craftlicenses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                            {{ __('Clear') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Licenses Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('File Number') }}</th>
                            <th>{{ __('Owner') }}</th>
                            <th>{{ __('Craft Type') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Fee') }}</th>
                            <th>{{ __('Paid') }}</th>
                            <th>{{ __('Remaining') }}</th>
                            <th>{{ __('Registration Date') }}</th>
                            <th>{{ __('Expiry Date') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($licenses as $license)
                            <tr>
                                <td>{{ $license->id }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $license->file_number }}</span>
                                </td>
                                <td>
                                    @if($license->mokalaf)
                                        {{ $license->mokalaf->name }}
                                        <small class="text-muted d-block">{{ $license->mokalaf->identity_number }}</small>
                                    @else
                                        {{ $license->owner_name }}
                                    @endif
                                </td>
                                <td>{{ $license->craft_type }}</td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusColor($license->status) }}">
                                        {{ $this->getStatusName($license->status) }}
                                    </span>
                                </td>
                                <td>{{ number_format($license->fee_amount, 2) }}</td>
                                <td>{{ number_format($license->paid_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $license->remaining > 0 ? 'danger' : 'success' }}">
                                        {{ number_format($license->remaining, 2) }}
                                        </span>
                                </td>
                                <td>{{ $license->registration_date->format('Y-m-d') }}</td>
                                <td>
                                    @if($license->expiry_date)
                                        @if($license->expiry_date->isPast())
                                            <span class="text-danger">{{ $license->expiry_date->format('Y-m-d') }}</span>
                                        @else
                                            <span>{{ $license->expiry_date->format('Y-m-d') }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">{{ __('No expiry date') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('craftlicenses.show', $license->id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (Auth::user()->can('update craft licenses'))
                                            <a href="{{ route('craftlicenses.edit', $license->id) }}" 
                                                   class="btn btn-sm btn-outline-secondary"
                                                   title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('craftlicenses.renew', $license->id) }}" 
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="{{ __('Renew') }}">
                                                <i class="fas fa-sync-alt"></i>
                                            </a>
                                            @if ($license->status === 'suspended')
                                                <a href="{{ route('craftlicenses.reactivate', $license->id) }}" 
                                                       class="btn btn-sm btn-outline-success"
                                                       title="{{ __('Reactivate') }}">
                                                    <i class="fas fa-check-circle"></i>
                                                </a>
                                            @endif
                                        @endif
                                        @if (Auth::user()->can('delete craft licenses'))
                                            <form action="{{ route('craftlicenses.destroy', $license->id) }}" method="POST">
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
                                <td colspan="13" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <h5>{{ __('No licenses found') }}</h5>
                                        <p>{{ __('Try adjusting your search or filters') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if ($licenses->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $links->links() }}
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

.text-danger {
    color: #dc3545;
}
</style>
@endpush

@push('scripts')
<script>
// Helper functions
function getStatusColor(status) {
    const colors = {
        'draft' => 'secondary',
        'active' => 'success',
        'expired' => 'danger',
        'suspended' => 'warning'
    };
    return colors[status] || 'secondary';
}

function getStatusName(status) {
    const names = {
        'draft' => 'Draft',
        'active' => 'Active',
        'expired' => 'Expired',
        'suspended' => 'Suspended',
    };
    return names[status] || status;
}

function number_format(number, decimals = 2) {
    return parseFloat(number).toFixed(decimals);
}
</script>
@endpush
