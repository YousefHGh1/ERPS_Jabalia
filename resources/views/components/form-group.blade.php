@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'loading' => false,
    'error' => null,
    'hint' => null,
    'icon' => null,
    'floating' => false,
])

@php
    $inputId = $id ?? $name;
    $hasError = $error !== null;
    $inputClass = 'form-control form-control-solid';
    if ($hasError) {
        $inputClass .= ' is-invalid';
    }
    if ($loading) {
        $inputClass .= ' loading-state';
    }
@endphp

<div class="form-group mb-3 position-relative">
    @if($label)
        <label for="{{ $inputId }}" class="form-label font-weight-bold">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="input-group position-relative">
        @if($icon)
            <span class="input-group-text bg-light">
                <i class="fas {{ $icon }} text-muted"></i>
            </span>
        @endif

        @if($type === 'textarea')
            <textarea
                id="{{ $inputId }}"
                name="{{ $name }}"
                class="{{ $inputClass }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $attributes }}
            >{{ old($name, $value) }}</textarea>
        @elseif($type === 'select')
            <select
                id="{{ $inputId }}"
                name="{{ $name }}"
                class="form-select form-control-solid {{ $hasError ? 'is-invalid' : '' }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes }}
            >
                <option value="">{{ $placeholder ?? __('Select') }}</option>
                {{ $slot }}
            </select>
        @else
            <input
                type="{{ $type }}"
                id="{{ $inputId }}"
                name="{{ $name }}"
                class="{{ $inputClass }}"
                placeholder="{{ $placeholder }}"
                value="{{ old($name, $value) }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $attributes }}
            >
        @endif

        @if($loading)
            <div class="position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); z-index: 5;">
                <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
            </div>
        @endif
    </div>

    @if($hasError)
        <div class="invalid-feedback d-block font-size-sm mt-1">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ $error }}
        </div>
    @endif

    @if($hint && !$hasError)
        <div class="form-text text-muted font-size-sm">
            <i class="fas fa-info-circle mr-1"></i>
            {{ $hint }}
        </div>
    @endif
</div>
