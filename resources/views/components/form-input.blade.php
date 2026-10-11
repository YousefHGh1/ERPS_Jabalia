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
    'error' => null,
    'hint' => null,
    'icon' => null,
])

@php
    $inputId = $id ?? $name;
    $hasError = $error !== null;
    $inputClass = $hasError ? 'is-invalid' : '';
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="input-group">
        @if($icon)
            <span class="input-group-text">
                <i class="fas {{ $icon }}"></i>
            </span>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            class="form-control {{ $inputClass }}"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $attributes }}
        >
    </div>

    @if($hasError)
        <div class="invalid-feedback d-block">
            {{ $error }}
        </div>
    @endif

    @if($hint && !$hasError)
        <div class="form-text">
            {{ $hint }}
        </div>
    @endif
</div>
