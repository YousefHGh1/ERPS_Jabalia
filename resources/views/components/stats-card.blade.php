@props([
    'title' => null,
    'value' => 0,
    'icon' => 'fa-chart-line',
    'color' => 'primary',
    'trend' => null,
    'trendValue' => null,
    'href' => null,
])

@php
    $grad = [
        'primary' => 'linear-gradient(135deg,#3699FF,#1BC5BD)',
        'info' => 'linear-gradient(135deg,#8950FC,#3699FF)',
        'success' => 'linear-gradient(135deg,#0f9b8e,#38d996)',
        'warning' => 'linear-gradient(135deg,#f2994a,#FFA800)',
        'danger' => 'linear-gradient(135deg,#eb3349,#F64E60)',
        'dark' => 'linear-gradient(135deg,#3F4254,#181C32)',
        'light' => 'linear-gradient(135deg,#E4E6EF,#F3F6F9)',
    ][$color] ?? 'linear-gradient(135deg,#3699FF,#1BC5BD)';
@endphp

<div class="col-md-3 mb-4">
    @if($href)
        <a href="{{ $href }}" class="text-decoration-none">
    @endif
    <div class="card shadow-sm h-100 border-0">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted font-weight-bold font-size-sm mb-2">{{ $title }}</div>
                    <div class="h2 mb-0 font-weight-bolder text-dark">{{ number_format($value) }}</div>
                    @if($trend && $trendValue)
                        <div class="font-size-sm {{ $trend === 'up' ? 'text-success' : 'text-danger' }} mt-2">
                            <i class="fas fa-arrow-{{ $trend }} mr-1"></i>
                            {{ $trendValue }}
                        </div>
                    @endif
                </div>
                <div class="d-flex align-items-center justify-content-center text-white"
                     style="width:54px;height:54px;border-radius:15px;background:{{ $grad }};box-shadow:0 8px 20px rgba(0,0,0,.10);">
                    <i class="fas {{ $icon }}"></i>
                </div>
            </div>
        </div>
    </div>
    @if($href)
        </a>
    @endif
</div>
