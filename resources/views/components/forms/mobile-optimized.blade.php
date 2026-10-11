@props(['title', 'fields', 'submitText', 'submitUrl', 'method' => 'POST', 'showProgress' => true])

<div class="mobile-form-container" x-data="mobileForm()">
    <!-- Mobile Header -->
    <div class="mobile-form-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="form-title mb-0">{{ $title }}</h5>
            @if($showProgress)
                <div class="progress-indicator">
                    <span class="progress-text" x-text="progressText"></span>
                </div>
            @endif
        </div>
        
        @if($showProgress)
            <!-- Progress Bar -->
            <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-primary" 
                     role="progressbar" 
                     :style="{ width: progressPercentage + '%' }"
                     :aria-valuenow="progressPercentage"
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
            </div>
        @endif
    </div>

    <!-- Mobile Form -->
    <form method="{{ $method }}" 
          action="{{ $submitUrl }}" 
          class="mobile-form"
          @submit="handleSubmit($event)"
          x-ref="mobileForm">
        
        @csrf
        
        <!-- Auto-save Indicator -->
        <div class="auto-save-status" 
             x-show="autoSaving" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="alert alert-info py-2">
                <small><i class="fas fa-save"></i> {{ __('Auto-saving...') }}</small>
            </div>
        </div>

        <!-- Form Fields -->
        <div class="form-fields">
            @foreach($fields as $index => $field)
                <div class="mobile-field-group" 
                     :class="{ 'field-focused': focusedField === '{{ $field['name'] }}' }"
                     x-data="field('{{ $field['name'] }}', {{ json_encode($field) }})">
                    
                    <!-- Field Label -->
                    @if(isset($field['label']))
                        <label class="form-label mobile-label" :for="'field-{{ $field['name'] }}'">
                            {{ $field['label'] }}
                            @if(isset($field['required']) && $field['required'])
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                    @endif

                    <!-- Field Help Text -->
                    @if(isset($field['help']))
                        <small class="form-text text-muted field-help">{{ $field['help'] }}</small>
                    @endif

                    <!-- Field Input -->
                    <div class="mobile-field-input">
                        @switch($field['type'] ?? 'text')
                            @case('text')
                                <input type="text" 
                                       class="form-control mobile-input" 
                                       id="field-{{ $field['name'] }}"
                                       name="{{ $field['name'] }}"
                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                       value="{{ $field['value'] ?? '' }}"
                                       {{ $field['required'] ?? false ? 'required' : '' }}
                                       @focus="focusedField = '{{ $field['name'] }}'"
                                       @blur="focusedField = null"
                                       @input="handleInput($event)">
                                @break

                            @case('email')
                                <input type="email" 
                                       class="form-control mobile-input" 
                                       id="field-{{ $field['name'] }}"
                                       name="{{ $field['name'] }}"
                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                       value="{{ $field['value'] ?? '' }}"
                                       {{ $field['required'] ?? false ? 'required' : '' }}
                                       @focus="focusedField = '{{ $field['name'] }}'"
                                       @blur="focusedField = null"
                                       @input="handleInput($event)">
                                @break

                            @case('tel')
                                <input type="tel" 
                                       class="form-control mobile-input" 
                                       id="field-{{ $field['name'] }}"
                                       name="{{ $field['name'] }}"
                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                       value="{{ $field['value'] ?? '' }}"
                                       {{ $field['required'] ?? false ? 'required' : '' }}
                                       @focus="focusedField = '{{ $field['name'] }}'"
                                       @blur="focusedField = null"
                                       @input="handleInput($event)">
                                @break

                            @case('number')
                                <input type="number" 
                                       class="form-control mobile-input" 
                                       id="field-{{ $field['name'] }}"
                                       name="{{ $field['name'] }}"
                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                       value="{{ $field['value'] ?? '' }}"
                                       {{ $field['required'] ?? false ? 'required' : '' }}
                                       {{ $field['min'] ?? '' ? 'min="' . $field['min'] . '"' : '' }}
                                       {{ $field['max'] ?? '' ? 'max="' . $field['max'] . '"' : '' }}
                                       @focus="focusedField = '{{ $field['name'] }}'"
                                       @blur="focusedField = null"
                                       @input="handleInput($event)">
                                @break

                            @case('textarea')
                                <textarea class="form-control mobile-input mobile-textarea" 
                                          id="field-{{ $field['name'] }}"
                                          name="{{ $field['name'] }}"
                                          placeholder="{{ $field['placeholder'] ?? '' }}"
                                          rows="{{ $field['rows'] ?? 4 }}"
                                          {{ $field['required'] ?? false ? 'required' : '' }}
                                          @focus="focusedField = '{{ $field['name'] }}'"
                                          @blur="focusedField = null"
                                          @input="handleInput($event)">{{ $field['value'] ?? '' }}</textarea>
                                @break

                            @case('select')
                                <select class="form-select mobile-input" 
                                        id="field-{{ $field['name'] }}"
                                        name="{{ $field['name'] }}"
                                        {{ $field['required'] ?? false ? 'required' : '' }}
                                        @focus="focusedField = '{{ $field['name'] }}'"
                                        @blur="focusedField = null"
                                        @change="handleInput($event)">
                                    <option value="">{{ $field['placeholder'] ?? 'Select...' }}</option>
                                    @foreach($field['options'] ?? [] as $value => $label)
                                        <option value="{{ $value }}" 
                                                {{ ($field['value'] ?? '') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @break

                            @case('checkbox')
                                <div class="form-check mobile-checkbox">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="field-{{ $field['name'] }}"
                                           name="{{ $field['name'] }}"
                                           value="1"
                                           {{ ($field['value'] ?? false) ? 'checked' : '' }}
                                           @change="handleInput($event)">
                                    <label class="form-check-label" for="field-{{ $field['name'] }}">
                                        {{ $field['label'] }}
                                    </label>
                                </div>
                                @break

                            @case('radio')
                                <div class="mobile-radio-group">
                                    @foreach($field['options'] ?? [] as $value => $label)
                                        <div class="form-check mobile-radio">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   id="field-{{ $field['name'] }}-{{ $value }}"
                                                   name="{{ $field['name'] }}"
                                                   value="{{ $value }}"
                                                   {{ ($field['value'] ?? '') == $value ? 'checked' : '' }}
                                                   @change="handleInput($event)">
                                            <label class="form-check-label" for="field-{{ $field['name'] }}-{{ $value }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @break

                            @case('date')
                                <input type="date" 
                                       class="form-control mobile-input" 
                                       id="field-{{ $field['name'] }}"
                                       name="{{ $field['name'] }}"
                                       value="{{ $field['value'] ?? '' }}"
                                       {{ $field['required'] ?? false ? 'required' : '' }}
                                       @focus="focusedField = '{{ $field['name'] }}'"
                                       @blur="focusedField = null"
                                       @input="handleInput($event)">
                                @break

                            @case('file')
                                <div class="mobile-file-upload">
                                    <input type="file" 
                                           class="form-control mobile-input" 
                                           id="field-{{ $field['name'] }}"
                                           name="{{ $field['name'] }}"
                                           accept="{{ $field['accept'] ?? '*' }}"
                                           {{ $field['required'] ?? false ? 'required' : '' }}
                                           @change="handleFileInput($event)">
                                    @if(isset($field['help']))
                                        <small class="form-text text-muted">{{ $field['help'] }}</small>
                                    @endif
                                </div>
                                @break

                            @default
                                <input type="text" 
                                       class="form-control mobile-input" 
                                       id="field-{{ $field['name'] }}"
                                       name="{{ $field['name'] }}"
                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                       value="{{ $field['value'] ?? '' }}"
                                       {{ $field['required'] ?? false ? 'required' : '' }}
                                       @focus="focusedField = '{{ $field['name'] }}'"
                                       @blur="focusedField = null"
                                       @input="handleInput($event)">
                        @endswitch
                    </div>

                    <!-- Field Error -->
                    <div class="field-error" x-show="errors['{{ $field['name'] }}']">
                        <small class="text-danger" x-text="errors['{{ $field['name'] }}']"></small>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Form Actions -->
        <div class="mobile-form-actions">
            <div class="d-grid gap-2">
                <!-- Submit Button -->
                <button type="submit" 
                        class="btn btn-primary btn-lg mobile-submit-btn"
                        :disabled="submitting"
                        @touchstart="$el.classList.add('btn-touch')"
                        @touchend="$el.classList.remove('btn-touch')">
                    <span x-show="!submitting">
                        <i class="fas fa-save"></i>
                        {{ $submitText }}
                    </span>
                    <span x-show="submitting">
                        <i class="fas fa-spinner fa-spin"></i>
                        {{ __('Processing...') }}
                    </span>
                </button>

                <!-- Cancel Button -->
                @if(isset($cancelUrl))
                    <a href="{{ $cancelUrl }}" 
                       class="btn btn-outline-secondary btn-lg mobile-cancel-btn">
                        <i class="fas fa-times"></i>
                        {{ __('Cancel') }}
                    </a>
                @endif
            </div>
        </div>

        <!-- Draft Saved Indicator -->
        <div class="draft-saved" 
             x-show="draftSaved" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="alert alert-success py-2">
                <small><i class="fas fa-check"></i> {{ __('Draft saved') }}</small>
            </div>
        </div>
    </form>
</div>

<script>
function mobileForm() {
    return {
        focusedField: null,
        submitting: false,
        autoSaving: false,
        draftSaved: false,
        errors: {},
        autoSaveTimer: null,
        formData: {},

        get progressPercentage() {
            const totalFields = {{ count($fields) }};
            const filledFields = Object.values(this.formData).filter(value => 
                value !== null && value !== ''
            ).length;
            return Math.round((filledFields / totalFields) * 100);
        },

        get progressText() {
            return `${this.progressPercentage}% {{ __('Complete') }}`;
        },

        handleInput(event) {
            const field = event.target;
            this.formData[field.name] = field.type === 'checkbox' ? field.checked : field.value;
            
            // Clear field error when user starts typing
            if (this.errors[field.name]) {
                delete this.errors[field.name];
            }
            
            // Trigger auto-save
            this.autoSave();
        },

        handleFileInput(event) {
            const file = event.target.files[0];
            if (file) {
                this.formData[event.target.name] = file.name;
                this.autoSave();
            }
        },

        autoSave() {
            clearTimeout(this.autoSaveTimer);
            this.autoSaving = true;

            this.autoSaveTimer = setTimeout(() => {
                // Save to localStorage
                localStorage.setItem('mobile_form_draft_' + window.location.pathname, 
                                  JSON.stringify(this.formData));
                
                this.autoSaving = false;
                this.draftSaved = true;
                
                setTimeout(() => {
                    this.draftSaved = false;
                }, 2000);
            }, 2000);
        },

        loadDraft() {
            const draft = localStorage.getItem('mobile_form_draft_' + window.location.pathname);
            if (draft) {
                this.formData = JSON.parse(draft);
                this.restoreFormData();
            }
        },

        restoreFormData() {
            const form = this.$refs.mobileForm;
            
            Object.keys(this.formData).forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    if (field.type === 'checkbox') {
                        field.checked = this.formData[fieldName];
                    } else {
                        field.value = this.formData[fieldName];
                    }
                }
            });
        },

        validateForm() {
            this.errors = {};
            let isValid = true;

            const form = this.$refs.mobileForm;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    this.errors[field.name] = 'This field is required';
                    isValid = false;
                }
            });

            return isValid;
        },

        handleSubmit(event) {
            if (this.submitting) {
                event.preventDefault();
                return;
            }

            if (!this.validateForm()) {
                event.preventDefault();
                
                // Scroll to first error
                const firstErrorField = document.querySelector('.field-error');
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                return;
            }

            this.submitting = true;
            
            // Clear draft on successful submission
            setTimeout(() => {
                localStorage.removeItem('mobile_form_draft_' + window.location.pathname);
            }, 2000);
        },

        init() {
            this.loadDraft();
            
            // Add touch feedback for mobile
            const buttons = document.querySelectorAll('.mobile-submit-btn, .mobile-cancel-btn');
            buttons.forEach(button => {
                button.addEventListener('touchstart', function() {
                    this.classList.add('btn-touch');
                });
                button.addEventListener('touchend', function() {
                    this.classList.remove('btn-touch');
                });
            });
        }
    }
}

function field(fieldName, fieldConfig) {
    return {
        fieldName: fieldName,
        fieldConfig: fieldConfig,
        
        handleInput(event) {
            // Field-specific validation can be added here
        }
    }
}
</script>

<style>
.mobile-form-container {
    max-width: 100%;
    margin: 0 auto;
}

.mobile-form-header {
    position: sticky;
    top: 0;
    background: white;
    z-index: 100;
    padding: 1rem 0;
    border-bottom: 1px solid #dee2e6;
}

.mobile-form-title {
    font-size: 1.25rem;
    font-weight: 600;
}

.progress-indicator {
    font-size: 0.875rem;
    color: #6c757d;
}

.mobile-field-group {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-radius: 12px;
    background: #f8f9fa;
    transition: all 0.2s ease;
}

.mobile-field-group.field-focused {
    background: #e3f2fd;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.mobile-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.field-help {
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
}

.mobile-input {
    font-size: 16px; /* Prevents zoom on iOS */
    padding: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.mobile-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.mobile-textarea {
    resize: vertical;
    min-height: 100px;
}

.mobile-checkbox, .mobile-radio {
    padding: 0.5rem 0;
}

.mobile-checkbox .form-check-input,
.mobile-radio .form-check-input {
    width: 20px;
    height: 20px;
    margin-right: 0.5rem;
}

.mobile-file-upload input[type="file"] {
    font-size: 16px;
    padding: 0.75rem;
}

.field-error {
    margin-top: 0.25rem;
    min-height: 1.2rem;
}

.mobile-form-actions {
    position: sticky;
    bottom: 0;
    background: white;
    padding: 1rem 0;
    margin-top: 2rem;
    border-top: 1px solid #dee2e6;
}

.mobile-submit-btn, .mobile-cancel-btn {
    font-size: 1.1rem;
    padding: 1rem;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.2s ease;
}

.mobile-submit-btn:disabled {
    opacity: 0.7;
}

.btn-touch {
    transform: scale(0.95);
    opacity: 0.8;
}

.auto-save-status, .draft-saved {
    position: fixed;
    top: 20px;
    right: 20px;
    left: 20px;
    z-index: 1000;
}

.auto-save-status .alert,
.draft-saved .alert {
    margin-bottom: 0;
    border-radius: 8px;
}

/* Mobile-specific optimizations */
@media (max-width: 768px) {
    .mobile-form-container {
        padding: 0 1rem;
    }
    
    .mobile-field-group {
        padding: 0.75rem;
    }
    
    .mobile-input {
        font-size: 16px; /* Important for iOS */
    }
    
    .mobile-submit-btn, .mobile-cancel-btn {
        padding: 0.875rem;
        font-size: 1rem;
    }
    
    .auto-save-status, .draft-saved {
        left: 1rem;
        right: 1rem;
    }
}

/* Large mobile and tablet */
@media (min-width: 769px) and (max-width: 1024px) {
    .mobile-form-container {
        max-width: 768px;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .mobile-field-group:hover {
        background: #f8f9fa;
    }
    
    .mobile-field-group.field-focused:hover {
        background: #e3f2fd;
    }
    
    .mobile-submit-btn:hover,
    .mobile-cancel-btn:hover {
        transform: none;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .mobile-form-header {
        background: #1a1a1a;
        border-bottom-color: #333;
    }
    
    .mobile-field-group {
        background: #2d2d2d;
    }
    
    .mobile-field-group.field-focused {
        background: #1e3a5f;
    }
    
    .mobile-input {
        background: #3d3d3d;
        border-color: #555;
        color: #fff;
    }
    
    .mobile-form-actions {
        background: #1a1a1a;
        border-top-color: #333;
    }
}
</style>
