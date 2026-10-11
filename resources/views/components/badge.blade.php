@props([
    'type' => 'primary',
    'pill' => false,
])

@php
    $typeClasses = match($type) {
        'primary' => 'bg-primary',
        'secondary' => 'bg-secondary',
        'success' => 'bg-success',
        'danger' => 'bg-danger',
        'warning' => 'bg-warning text-dark',
        'info' => 'bg-info',
        'light' => 'bg-light text-dark',
        'dark' => 'bg-dark',
        default => 'bg-primary',
    };
@endphp

<span class="badge {{ $typeClasses }} {{ $pill ? 'rounded-pill' : '' }}" {{ $attributes }}>
    {{ $slot }}
</span>
