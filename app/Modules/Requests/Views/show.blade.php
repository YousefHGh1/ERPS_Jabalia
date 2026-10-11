@extends('layouts.app')

@section('title')
{{ __('Request Details') }} #{{ $request->id }}

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('requests.index') }}">{{ __('Requests') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('Details') }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            @if (Auth::user()->can('update requests'))
                <a href="{{ route('requests.edit', $request->id) }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-edit"></i>
                    {{ __('Edit') }}
                </a>
            @endif
            <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <!-- Request Details -->
    <div class="row">
        <div class="col-md-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Request Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">{{ __('Sequence Number') }}</th>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $request->sequence_number }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Request Type') }}</th>
                                    <td>{{ $request->requestType->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Priority') }}</th>
                                    <td>
                                        <span class="badge bg-{{ $this->getPriorityColor($request->priority) }}">
                                            {{ ucfirst($request->priority) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}</th>
                                    <td>
                                        <span class="badge bg-{{ $this->getStatusColor($request->status) }}">
                                            {{ $this->getStatusName($request->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Created Date') }}</th>
                                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Deadline') }}</th>
                                    <td>
                                        @if($request->deadline)
                                            {{ $request->deadline->format('Y-m-d') }}
                                        @else
                                            <span class="text-muted">{{ __('No deadline') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">{{ __('Created By') }}</th>
                                    <td>{{ $request->createdBy->name ?? 'System' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Updated By') }}</th>
                                    <td>{{ $request->updatedBy->name ?? 'System' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Updated At') }}</th>
                                    <td>{{ $request->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @if($request->deadline)
                                    <tr>
                                        <th>{{ __('Days Until Deadline') }}</th>
                                        <td>
                                            @if($request->deadline->isPast())
                                                <span class="text-danger">{{ __('Overdue') }}</span>
                                            @else
                                                {{ $request->deadline->diffInDays(now()) }} {{ __('days') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Request Description') }}</h5>
                </div>
                <div class="card-body">
                    <p class="lead">{{ $request->text }}</p>
                    @if($request->notes)
                        <div class="mt-3">
                            <h6>{{ __('Additional Notes:') }}</h6>
                            <p>{{ $request->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Citizen Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Citizen Information') }}</h5>
                </div>
                <div class="card-body">
                    @if($request->mokalaf)
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="30%">{{ __('Name') }}</th>
                                        <td>{{ $request->mokalaf->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Identity Number') }}</th>
                                        <td>{{ $request->mokalaf->identity_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('License Number') }}</th>
                                        <td>{{ $request->mokalaf->license_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Mobile') }}</th>
                                        <td>{{ $request->mokalaf->mobile }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Address') }}</th>
                                        <td>{{ $request->mokalaf->address }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h6>{{ __('No Citizen Assigned') }}</h6>
                            <p>{{ __('This request is not assigned to any citizen.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status History -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Status History') }}</h5>
                </div>
                <div class="card-body">
                    @if($history->isNotEmpty())
                        <div class="timeline">
                            @foreach ($history as $item)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $this->getStatusColor($item->action) }}"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $this->getActionName($item->action) }}</h6>
                                                <p class="text-muted mb-0">{{ $item->description }}</p>
                                                @if($item->user_name)
                                                    <small class="text-muted">
                                                        <i class="fas fa-user"></i> {{ $item->user_name }}
                                                    </small>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $item->created_at->format('Y-m-d H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('No status history available') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attachments -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Attachments') }}</h5>
                    @if (Auth::user()->can('update requests'))
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#attachmentModal">
                            <i class="fas fa-plus"></i>
                            {{ __('Add Attachment') }}
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if ($attachments->isNotEmpty())
                        <div class="list-group">
                            @foreach ($attachments as $attachment)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-alt text-primary me-2"></i>
                                        <div>
                                            <div class="fw-bold">{{ $attachment->file_name }}</div>
                                            <small class="text-muted">
                                                {{ $attachment->file_size_formatted ?? $attachment->file_size }} bytes
                                                {{ __('uploaded') }} {{ $attachment->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank"
                                           title="{{ __('Download') }}">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if (Auth::user()->can('update requests'))
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirm('{{ __('Are you sure you want to delete this attachment?') }}')"
                                                    title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('No attachments available') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    @if (Auth::user()->can('update requests'))
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
                                <i class="fas fa-edit"></i>
                                {{ __('Update Status') }}
                            </button>
                            <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#attachmentModal">
                                <i class="fas fa-paperclip"></i>
                                {{ __('Add Attachment') }}
                            </button>
                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#notesModal">
                                <i class="fas fa-sticky-note"></i>
                                {{ __('Add Notes') }}
                            </button>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('requests.print', $request->id) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                            <i class="fas fa-print"></i>
                            {{ __('Print') }}
                        </a>
                        <a href="{{ route('requests.export', $request->id) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-export"></i>
                            {{ __('Export') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Status Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <span class="badge bg-{{ $this->getStatusColor($request->status) }} fs-6">
                            {{ $this->getStatusName($request->status) }}
                        </span>
                    </div>
                    <div class="mt-3">
                        @if ($request->status === 'pending')
                            <div class="alert alert-warning">
                                <h6>{{ __('Pending Review') }}</h6>
                                <p>{{ __('This request is currently pending review by the relevant department.') }}</p>
                            </div>
                        @elseif ($request->status === 'approved')
                            <div class="alert alert-success">
                                <h6>{{ __('Approved') }}</h6>
                                <p>{{ __('This request has been approved and is being processed.') }}</p>
                            </div>
                        @elseif ($request->status === 'rejected')
                            <div class="alert alert-danger">
                                <h6>{{ __('Rejected') }}</h6>
                                <p>{{ __('This request has been rejected.') }}</p>
                            </div>
                        @elseif ($request->status === 'completed')
                            <div class="alert alert-info">
                                <h6>{{ __('Completed') }}</h6>
                                <p>{{ __('This request has been completed successfully.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Information -->
            @if ($request->mokalaf)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Related Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <h6>{{ __('Citizen History') }}</h6>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('mokalafs.show', $request->mokalaf->id) }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-user me-2"></i>
                                {{ __('View Citizen Profile') }}
                            </a>
                            <a href="{{ route('mokalafs.requests', $request->mokalaf->id) }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-list me-2"></i>
                                {{ __('View Citizen Requests') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Modal -->
@if (Auth::user()->can('update requests'))
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">{{ __('Update Status') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('requests.status.update', $request->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="status_id" class="form-label">{{ __('New Status') }}</label>
                        <select name="status_id" id="status_id" class="form-select" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ $request->status_id == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status_notes" class="form-label">{{ __('Notes') }}</label>
                        <textarea name="notes" id="status_notes" class="form-control" rows="3"
                                  placeholder="{{ __('Add notes about this status change...') }}"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Status') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Attachment Modal -->
@if (Auth::user()->can('update requests'))
<div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachmentModalLabel">{{ __('Add Attachment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('requests.attachment.add', $request->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">{{ __('Select File') }}</label>
                        <input type="file" name="file" id="file" class="form-control" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        @error('file')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" id="description" class="form-control" rows="3"
                                  placeholder="{{ __('Describe the attachment...') }}"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Upload') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Notes Modal -->
@if (Auth::user()->can('update requests'))
<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalLabel">{{ __('Add Notes') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('requests.notes.update', $request->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('Notes') }}</label>
                        <textarea name="notes" id="notes" class="form-control" rows="5"
                                  placeholder="{{ __('Add notes about this request...') }}">{{ $request->notes ?? '' }}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Notes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
// Helper functions
function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'approved': 'success',
        'rejected': 'danger',
        'completed': 'info',
        'cancelled': 'secondary'
    };
    return colors[status] || 'secondary';
}

function getStatusName(status) {
    const names = {
        'pending': 'Pending',
        'approved': 'Approved',
        'rejected': 'Rejected',
        'completed': 'completed',
        'cancelled': 'Cancelled'
    };
    return names[status] || status;
}

function getPriorityColor(priority) {
    const colors = {
        'normal': 'secondary',
        'high': 'warning',
        'urgent' => 'danger'
    };
    return colors[priority] || 'secondary';
}

function getActionName(action) {
    const names = {
        'created' => 'Created',
        'updated' => 'Updated',
        'status_changed' => 'Status Changed',
        'attachment_added' => 'Attachment Added',
        'notes_updated' => 'Notes Updated'
    };
    return names[action] || action;
}
</script>
@endpush

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -15px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.timeline-content {
    margin-left: 25px;
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #fff;
}

.timeline-item::before {
    top: 30px;
}

.badge {
    font-size: 0.875rem;
}

.list-group-item {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 5px;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.modal-content {
    border: none;
    border-radius: 12px;
}

.alert {
    border: none;
    border-radius: 8px;
}
</style>
@endpush
