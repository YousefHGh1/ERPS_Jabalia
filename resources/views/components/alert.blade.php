@props([
    'type' => 'info',
    'dismissible' => false,
])

@php
    $typeClasses = $type === 'success' ? 'alert-success' : 
                   ($type === 'danger' ? 'alert-danger' : 
                   ($type === 'warning' ? 'alert-warning' : 
                   ($type === 'info' ? 'alert-info' : 'alert-info')));

    $icon = $type === 'success' ? 'fa-check-circle' : 
            ($type === 'danger' ? 'fa-times-circle' : 
            ($type === 'warning' ? 'fa-exclamation-triangle' : 
            ($type === 'info' ? 'fa-info-circle' : 'fa-info-circle')));
@endphp

<div class="alert {{ $typeClasses }} fade show d-flex align-items-center" role="alert" style="border-radius:14px;">
    @if($icon)
        <i class="fas {{ $icon }} mr-2"></i>
    @endif

    <div class="flex-grow-1">{{ $slot }}</div>

    @if($dismissible)
        <button type="button" class="close ml-2" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif
</div>
