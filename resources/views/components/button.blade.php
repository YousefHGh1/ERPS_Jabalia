@props([
    'type' => 'primary',
    'size' => 'md',
    'icon' => null,
    'spinner' => false,
    'disabled' => false,
])

@php
    $typeClasses = $type === 'primary' ? 'btn-primary' : 
                   ($type === 'secondary' ? 'btn-secondary' : 
                   ($type === 'success' ? 'btn-success' : 
                   ($type === 'danger' ? 'btn-danger' : 
                   ($type === 'warning' ? 'btn-warning' : 
                   ($type === 'info' ? 'btn-info' : 
                   ($type === 'light' ? 'btn-light' : 
                   ($type === 'dark' ? 'btn-dark' : 
                   ($type === 'link' ? 'btn-link' : 
                   ($type === 'outline-primary' ? 'btn-outline-primary' : 
                   ($type === 'outline-secondary' ? 'btn-outline-secondary' : 
                   ($type === 'outline-success' ? 'btn-outline-success' : 
                   ($type === 'outline-danger' ? 'btn-outline-danger' : 'btn-primary'))))))))))));

    $sizeClasses = $size === 'sm' ? 'btn-sm' : 
                   ($size === 'lg' ? 'btn-lg' : '');
@endphp

<button {{ $attributes->merge(['class' => "btn {$typeClasses} {$sizeClasses}", 'disabled' => $disabled || $spinner]) }}>
    @if($spinner)
        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
    @endif

    @if($icon && !$spinner)
        <i class="fas {{ $icon }} mr-2"></i>
    @endif

    {{ $slot }}
</button>
