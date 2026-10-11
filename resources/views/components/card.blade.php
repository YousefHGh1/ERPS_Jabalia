@props([
    'title' => null,
    'icon' => null,
    'headerClass' => null,
    'bodyClass' => null,
    'footer' => null,
    'color' => 'primary',
])

@php
    $headerColorClass = $headerClass ?: 'bg-' . $color;
@endphp

<div {{ $attributes->merge(['class' => 'card shadow-sm']) }}>
    @if($title || isset($header))
        <div class="card-header {{ $headerColorClass }} text-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                @if($icon)
                    <i class="fas {{ $icon }} mr-2"></i>
                @endif
                @if($title)
                    <h5 class="card-title mb-0 text-white">{{ $title }}</h5>
                @endif
            </div>
            @isset($header)
                <div class="ml-auto">
                    {{ $header }}
                </div>
            @endisset
        </div>
    @endif

    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
