@extends('layouts.app')

@section('title')
{{ __('Create New Request') }}

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0">{{ __('Create New Request') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('requests.index') }}">{{ __('Requests') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('Create') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('requests.store') }}">
                        @csrf
                        
                        <!-- Request Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">{{ __('Request Information') }}</h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="request_type_id" class="form-label">{{ __('Request Type') }} <span class="text-danger">*</span></label>
                                <select name="request_type_id" id="request_type_id" class="form-select" required>
                                    <option value="">{{ __('Select Request Type') }}</option>
                                    @foreach ($requestTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('request_type_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label">{{ __('Priority') }} <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="normal">{{ __('Normal') }}</option>
                                    <option value="high">{{ __('High') }}</option>
                                    <option value="urgent">{{ __('Urgent') }}</option>
                                </select>
                                @error('priority')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="text" class="form-label">{{ __('Request Details') }} <span class="text-danger">*</span></label>
                                <textarea name="text" id="text" class="form-control" rows="4" required
                                          placeholder="{{ __('Describe your request in detail...') }}"></textarea>
                                @error('text')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="deadline" class="form-label">{{ __('Deadline (Optional)') }}</label>
                                <input type="date" name="deadline" id="deadline" class="form-control">
                                @error('deadline')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="notes" class="form-label">{{ __('Additional Notes (Optional)') }}</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2"
                                          placeholder="{{ __('Any additional information...') }}"></textarea>
                                @error('notes')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Citizen Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">{{ __('Citizen Information') }}</h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="has_mokalaf" name="has_mokalaf" value="1" checked>
                                    <label class="form-check-label" for="has_mokalaf">
                                        {{ __('Request for existing citizen') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="mokalaf_fields">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="mokalaf_id" class="form-label">{{ __('Select Citizen') }}</label>
                                    <select name="mokalaf_id" id="mokalaf_id" class="form-select">
                                        <option value="">{{ __('Select Citizen') }}</option>
                                        @foreach ($mokalafs as $mokalaf)
                                            <option value="{{ $mokalaf->id }}">
                                                {{ $mokalaf->name }} ({{ $mokalaf->identity_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('mokalaf_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="identity_number" class="form-label">{{ __('Identity Number') }}</label>
                                    <input type="text" name="identity_number" id="identity_number" class="form-control"
                                           placeholder="{{ __('Enter identity number...') }}">
                                    @error('identity_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="license_number" class="form-label">{{ __('License Number') }}</label>
                                    <input type="text" name="license_number" id="license_number" class="form-control"
                                           placeholder="{{ __('Enter license number...') }}">
                                    @error('license_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="address" class="form-label">{{ __('Address') }}</label>
                                    <textarea name="address" id="address" class="form-control" rows="2"
                                              placeholder="{{ __('Enter citizen address...') }}"></textarea>
                                    @error('address')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="new_mokalaf_fields" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="new_mokalaf_name" class="form-label">{{ __('Citizen Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="new_mokalaf_name" id="new_mokalaf_name" class="form-control"
                                           placeholder="{{ __('Enter citizen name...') }}">
                                    @error('new_mokalaf_name')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="new_mokalaf_identity" class="form-label">{{ __('Identity Number') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="new_mokalaf_identity" id="new_mokalaf_identity" class="form-control"
                                           placeholder="{{ __('Enter identity number...') }}">
                                    @error('new_mokalaf_identity')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="new_mokalaf_license" class="form-label">{{ __('License Number') }}</label>
                                    <input type="text" name="new_mokalaf_license" id="new_mokalaf_license" class="form-control"
                                           placeholder="{{ __('Enter license number...') }}">
                                    @error('new_mokalaf_license')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="new_mokalaf_mobile" class="form-label">{{ __('Mobile Number') }}</label>
                                    <input type="tel" name="new_mokalaf_mobile" id="new_mokalaf_mobile" class="form-control"
                                           placeholder="{{ __('Enter mobile number...') }}">
                                    @error('new_mokalaf_mobile')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="new_mokalaf_address" class="form-label">{{ __('Address') }}</label>
                                    <textarea name="new_mokalaf_address" id="new_mokalaf_address" class="form-control" rows="2"
                                              placeholder="{{ __('Enter citizen address...') }}"></textarea>
                                    @error('new_mokalaf_address')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-right"></i>
                                        {{ __('Cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        {{ __('Create Request') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('Request Guidelines') }}</h5>
                <div class="alert alert-info">
                    <h6>{{ __('Important Notes:') }}</h6>
                    <ul class="mb-0">
                        <li>{{ __('All fields marked with * are required') }}</li>
                        <li>{{ __('Select the appropriate request type for your needs') }}</li>
                        <li>{{ __('Provide detailed description for better service') }}</li>
                        <li>{{ __('Set priority to urgent for time-sensitive requests') }}</li>
                    </ul>
                </div>

                <h6>{{ __('Request Types:') }}</h6>
                <ul class="list-unstyled">
                    @foreach ($requestTypes as $type)
                        <li class="mb-2">
                            <strong>{{ $type->name }}:</strong>
                            <p class="text-muted small">{{ $type->description ?? 'No description available' }}</p>
                        </li>
                    @endforeach
                </ul>

                <div>
                    <h6>{{ __('Need Help?') }}</h6>
                    <p class="text-muted small">
                        {{ __('Contact the help desk at extension 100') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasMokalafCheckbox = document.getElementById('has_mokalaf');
    const mokalafFields = document.getElementById('mokalaf_fields');
    const newMokalafFields = document.getElementById('new_mokalaf_fields');

    hasMokalafCheckbox.addEventListener('change', function() {
        if (this.checked) {
            mokalafFields.style.display = 'block';
            newMokalafFields.style.display = 'none';
            
            // Make mokalaf fields required
            document.getElementById('mokalaf_id').required = true;
            document.getElementById('identity_number').required = false;
            document.getElementById('license_number').required = false;
            document.getElementById('address').required = false;
        } else {
            mokalafFields.style.display = 'none';
            newMokalafFields.style.display = 'block';
            
            // Make new mokalaf fields required
            document.getElementById('mokalaf_id').required = false;
            document.getElementById('identity_number').required = false;
            document.getElementById('license_number').required = false;
            document.getElementById('address').required = false;
            document.getElementById('new_mokalaf_name').required = true;
            document.getElementById('new_mokalaf_identity').required = true;
        }
    });

    // Initialize based on checkbox state
    if (hasMokalafCheckbox.checked) {
        mokalafFields.style.display = 'block';
        newMokalafFields.style.display = 'none';
        document.getElementById('mokalaf_id').required = true;
    } else {
        newMokalafFields.style.display = 'block';
        mokalafFields.style.display = 'none';
        document.getElementById('new_mokalaf_name').required = true;
        document.getElementById('new_mokalaf_identity').required = true;
    }
});
</script>
@endpush

@push('styles')
<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    border-radius: 8px;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.form-switch {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    padding: 0 0.5rem;
    color: #6c757d;
}

.alert {
    border: none;
    border-radius: 8px;
}

.btn {
    border-radius: 6px;
    padding: 0.5rem 1rem;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}
</style>
@endpush
