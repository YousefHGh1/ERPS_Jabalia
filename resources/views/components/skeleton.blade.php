@props([
    'type' => 'text',
    'lines' => 3,
    'width' => '100%',
])

@php
    $typeClasses = match($type) {
        'text' => 'skeleton-text',
        'avatar' => 'skeleton-avatar',
        'card' => 'skeleton-card',
        'table' => 'skeleton-table',
        default => 'skeleton-text',
    };
@endphp

<div class="skeleton {{ $typeClasses }}" style="width: {{ $width }}">
    @if($type === 'text')
        @for($i = 0; $i < $lines; $i++)
            <div class="skeleton-line" style="width: {{ $i === 0 ? '100%' : ($i === $lines - 1 ? '60%' : '80%') }}"></div>
        @endfor
    @elseif($type === 'avatar')
        <div class="skeleton-avatar-inner"></div>
    @elseif($type === 'card')
        <div class="skeleton-card-header"></div>
        <div class="skeleton-card-body">
            <div class="skeleton-line" style="width: 100%"></div>
            <div class="skeleton-line" style="width: 80%"></div>
            <div class="skeleton-line" style="width: 60%"></div>
        </div>
    @elseif($type === 'table')
        <div class="skeleton-table-header"></div>
        @for($i = 0; $i < 5; $i++)
            <div class="skeleton-table-row">
                <div class="skeleton-line" style="width: 30%"></div>
                <div class="skeleton-line" style="width: 40%"></div>
                <div class="skeleton-line" style="width: 20%"></div>
            </div>
        @endfor
    @endif
</div>
