@props([
    'title' => '',
    'value' => 0,
    'icon' => 'fa-chart-line',
    'color' => 'primary', // primary, success, warning, danger, info, dark
    'link' => '#',
    'linkText' => 'View Details',
    'border' => true,
])

@php
    $gradientClasses = [
        'primary' => 'bg-primary-grad',
        'success' => 'bg-success-grad',
        'warning' => 'bg-warning-grad',
        'danger' => 'bg-danger-grad',
        'info' => 'bg-info-grad',
        'dark' => 'bg-dark-grad',
    ];

    $borderClasses = [
        'primary' => 'border-primary',
        'success' => 'border-success',
        'warning' => 'border-warning',
        'danger' => 'border-danger',
        'info' => 'border-info',
        'dark' => 'border-dark',
    ];

    $textClasses = [
        'primary' => 'text-primary',
        'success' => 'text-success',
        'warning' => 'text-warning',
        'danger' => 'text-danger',
        'info' => 'text-info',
        'dark' => 'text-dark',
    ];

    $btnClasses = [
        'primary' => 'btn-light-primary',
        'success' => 'btn-light-success',
        'warning' => 'btn-light-warning',
        'danger' => 'btn-light-danger',
        'info' => 'btn-light-info',
        'dark' => 'btn-light-dark',
    ];

    $gradientClass = $gradientClasses[$color] ?? 'bg-primary-grad';
    $borderClass = $borderClasses[$color] ?? 'border-primary';
    $textClass = $textClasses[$color] ?? 'text-primary';
    $btnClass = $btnClasses[$color] ?? 'btn-light-primary';
    $borderClassSuffix = $border ? 'border-4' : '';
@endphp

<div class="card card-custom admin-stat-card shadow-sm h-100 border-top {{ $borderClass }} {{ $borderClassSuffix }}">
    <div class="card-body p-7">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted font-weight-bold font-size-sm">{{ $title }}</span>
                <h2 class="font-weight-bolder mt-2 mb-0 {{ $textClass }}">{{ number_format($value) }}</h2>
            </div>
            <div class="icon-box {{ $gradientClass }}">
                <i class="fa {{ $icon }} text-white icon-lg"></i>
            </div>
        </div>
        @if($link && $linkText)
        <div class="mt-6">
            <a href="{{ $link }}" class="btn btn-block btn-sm {{ $btnClass }} font-weight-bolder py-3 px-6">{{ $linkText }}</a>
        </div>
        @endif
    </div>
</div>