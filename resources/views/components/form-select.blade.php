@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
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

        <select
            id="{{ $inputId }}"
            name="{{ $name }}"
            class="form-select {{ $inputClass }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach($options as $value => $text)
                <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endforeach
        </select>
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
