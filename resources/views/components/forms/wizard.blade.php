@props(['steps', 'currentStep', 'title', 'submitText', 'submitUrl', 'method' => 'POST'])

<div class="wizard-container" x-data="wizard({{ $currentStep }})">
    <!-- Wizard Header -->
    <div class="wizard-header mb-4">
        <h3 class="wizard-title">{{ $title }}</h3>
        
        <!-- Progress Steps -->
        <div class="wizard-steps">
            <ul class="nav nav-pills nav-justified">
                @foreach($steps as $index => $step)
                    <li class="nav-item">
                        <a href="#" 
                           class="nav-link step-link {{ $index <= $currentStep ? 'active' : '' }} {{ $index < $currentStep ? 'completed' : '' }}"
                           :class="{ 'active': currentStep === {{ $index }}, 'completed': currentStep > {{ $index }} }"
                           @click="goToStep({{ $index }})"
                           {{ $index > $currentStep ? 'disabled' : '' }}>
                            <span class="step-number">{{ $index + 1 }}</span>
                            <span class="step-title">{{ $step['title'] }}</span>
                            <span class="step-icon" x-show="currentStep > {{ $index }}">
                                <i class="fas fa-check"></i>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress mb-4" style="height: 6px;">
        <div class="progress-bar bg-success" 
             role="progressbar" 
             :style="{ width: progressPercentage + '%' }"
             :aria-valuenow="progressPercentage"
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
    </div>

    <!-- Wizard Content -->
    <div class="wizard-content">
        <form method="{{ $method }}" action="{{ $submitUrl }}" 
              @submit="handleFormSubmit($event)"
              x-ref="wizardForm">
            
            <!-- CSRF Token -->
            @csrf
            
            <!-- Step Content -->
            @foreach($steps as $index => $step)
                <div class="step-content" 
                     x-show="currentStep === {{ $index }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-4"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform translate-x-4">
                    
                    <div class="step-header mb-3">
                        <h4 class="step-title">{{ $step['title'] }}</h4>
                        @if(isset($step['description']))
                            <p class="step-description text-muted">{{ $step['description'] }}</p>
                        @endif
                    </div>

                    <!-- Step Fields -->
                    <div class="step-fields">
                        {{ $step['content'] ?? '' }}
                    </div>

                    <!-- Step Validation Summary -->
                    <div class="step-validation" x-show="stepErrors.length > 0">
                        <div class="alert alert-danger">
                            <h6>Please correct the following errors:</h6>
                            <ul>
                                <template x-for="error in stepErrors" :key="error">
                                    <li x-text="error"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Navigation Buttons -->
            <div class="wizard-navigation mt-4">
                <div class="d-flex justify-content-between">
                    <!-- Previous Button -->
                    <button type="button" 
                            class="btn btn-secondary"
                            x-show="currentStep > 0"
                            @click="previousStep()"
                            :disabled="currentStep === 0">
                        <i class="fas fa-arrow-right"></i>
                        {{ __('Previous') }}
                    </button>

                    <!-- Spacer -->
                    <div class="flex-grow-1"></div>

                    <!-- Next/Submit Button -->
                    <button type="button" 
                            class="btn btn-primary"
                            x-show="currentStep < {{ count($steps) - 1 }}"
                            @click="nextStep()"
                            :disabled="!isCurrentStepValid()">
                        {{ __('Next') }}
                        <i class="fas fa-arrow-left"></i>
                    </button>

                    <button type="submit" 
                            class="btn btn-success"
                            x-show="currentStep === {{ count($steps) - 1 }}"
                            :disabled="submitting">
                        <span x-show="!submitting">
                            <i class="fas fa-save"></i>
                            {{ $submitText }}
                        </span>
                        <span x-show="submitting">
                            <i class="fas fa-spinner fa-spin"></i>
                            {{ __('Processing...') }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Auto-save Indicator -->
            <div class="auto-save-indicator mt-3" 
                 x-show="autoSaving" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100">
                <div class="alert alert-info">
                    <i class="fas fa-save"></i>
                    {{ __('Auto-saving...') }}
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function wizard(initialStep = 0) {
    return {
        currentStep: initialStep,
        totalSteps: {{ count($steps) }},
        stepData: {},
        stepErrors: [],
        submitting: false,
        autoSaving: false,
        autoSaveTimer: null,

        get progressPercentage() {
            return ((this.currentStep + 1) / this.totalSteps) * 100;
        },

        goToStep(step) {
            if (step < this.currentStep || this.isCurrentStepValid()) {
                this.currentStep = step;
                this.stepErrors = [];
                this.scrollToTop();
            }
        },

        nextStep() {
            if (this.isCurrentStepValid()) {
                this.saveCurrentStepData();
                this.currentStep++;
                this.stepErrors = [];
                this.scrollToTop();
            } else {
                this.validateCurrentStep();
            }
        },

        previousStep() {
            this.saveCurrentStepData();
            this.currentStep--;
            this.stepErrors = [];
            this.scrollToTop();
        },

        isCurrentStepValid() {
            const form = this.$refs.wizardForm;
            const currentStepElement = form.querySelector('.step-content[x-show*="' + this.currentStep + '"]');
            const requiredFields = currentStepElement.querySelectorAll('[required]');
            
            let isValid = true;
            this.stepErrors = [];

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    this.stepErrors.push(field.getAttribute('data-error-message') || 
                                      field.getAttribute('placeholder') + ' is required');
                }
            });

            return isValid;
        },

        validateCurrentStep() {
            this.isCurrentStepValid();
        },

        saveCurrentStepData() {
            const form = this.$refs.wizardForm;
            const currentStepElement = form.querySelector('.step-content[x-show*="' + this.currentStep + '"]');
            const inputs = currentStepElement.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    this.stepData[input.name] = input.checked;
                } else {
                    this.stepData[input.name] = input.value;
                }
            });

            // Trigger auto-save
            this.autoSave();
        },

        autoSave() {
            clearTimeout(this.autoSaveTimer);
            this.autoSaving = true;

            this.autoSaveTimer = setTimeout(() => {
                // Save to localStorage or server
                localStorage.setItem('wizard_draft_' + window.location.pathname, 
                                  JSON.stringify(this.stepData));
                this.autoSaving = false;
            }, 1000);
        },

        loadDraftData() {
            const draft = localStorage.getItem('wizard_draft_' + window.location.pathname);
            if (draft) {
                this.stepData = JSON.parse(draft);
                this.restoreFormData();
            }
        },

        restoreFormData() {
            const form = this.$refs.wizardForm;
            
            Object.keys(this.stepData).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        field.checked = this.stepData[key];
                    } else {
                        field.value = this.stepData[key];
                    }
                }
            });
        },

        handleFormSubmit(event) {
            if (this.currentStep < this.totalSteps - 1) {
                event.preventDefault();
                this.nextStep();
                return;
            }

            if (!this.isCurrentStepValid()) {
                event.preventDefault();
                this.validateCurrentStep();
                return;
            }

            this.submitting = true;
            
            // Clear draft on successful submission
            setTimeout(() => {
                localStorage.removeItem('wizard_draft_' + window.location.pathname);
            }, 2000);
        },

        scrollToTop() {
            const wizardElement = document.querySelector('.wizard-container');
            if (wizardElement) {
                wizardElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },

        init() {
            this.loadDraftData();
            
            // Set up auto-save on input changes
            const form = this.$refs.wizardForm;
            form.addEventListener('input', () => {
                clearTimeout(this.autoSaveTimer);
                this.autoSaveTimer = setTimeout(() => {
                    this.autoSave();
                }, 2000);
            });
        }
    }
}
</script>

<style>
.wizard-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 2rem;
}

.wizard-steps .nav-link {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0;
    position: relative;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    font-weight: bold;
}

.nav-link.active .step-number {
    background: #007bff;
    color: white;
}

.nav-link.completed .step-number {
    background: #28a745;
    color: white;
}

.step-title {
    font-size: 0.9rem;
}

.step-icon {
    margin-left: 8px;
    color: #28a745;
}

.step-content {
    min-height: 300px;
}

.wizard-navigation .btn {
    min-width: 120px;
}

.auto-save-indicator .alert {
    margin-bottom: 0;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .wizard-container {
        padding: 1rem;
    }
    
    .wizard-steps .nav-link {
        padding: 0.5rem;
        font-size: 0.8rem;
    }
    
    .step-title {
        display: none;
    }
    
    .wizard-navigation .btn {
        min-width: 100px;
    }
}
</style>
