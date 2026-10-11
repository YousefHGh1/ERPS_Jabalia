@props([
    'label' => __('Save'),
    'loading' => false,
    'loadingText' => __('Saving...'),
    'type' => 'primary',
    'icon' => 'fa-save',
    'size' => 'md',
    'disabled' => false,
])

@php
    $typeClasses = $type === 'primary' ? 'btn-primary' : 
                   ($type === 'success' ? 'btn-success' : 
                   ($type === 'danger' ? 'btn-danger' : 
                   ($type === 'warning' ? 'btn-warning' : 
                   ($type === 'info' ? 'btn-info' : 
                   ($type === 'secondary' ? 'btn-secondary' : 'btn-primary')))));

    $sizeClasses = $size === 'sm' ? 'btn-sm' : 
                   ($size === 'lg' ? 'btn-lg' : '');
@endphp

<button type="submit" 
        class="btn {{ $typeClasses }} {{ $sizeClasses }} font-weight-bolder px-8 py-3"
        {{ $disabled || $loading ? 'disabled' : '' }}>
    @if($loading)
        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
        <span>{{ $loadingText }}</span>
    @else
        @if($icon)
            <i class="fas {{ $icon }} mr-2"></i>
        @endif
        <span>{{ $label }}</span>
    @endif
</button>
