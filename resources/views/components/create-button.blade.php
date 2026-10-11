@props([
    'route' => null,
    'url' => null,
    'text' => 'Create',
    'icon' => 'fa-plus',
    'module' => null,
])

@php
    // هوية القسم للون التلقائي
    $theme = module_theme($module);
    $moduleVars = '--module-accent: ' . $theme['color'] . ';'
        . ' --module-accent-rgb: ' . $theme['rgb'] . ';'
        . ' --module-accent-soft: ' . $theme['soft'] . ';'
        . ' --module-accent-strong: ' . $theme['strong'] . ';';
@endphp

@if($route)
    <a href="{{ route($route) }}" class="btn btn-create-main font-weight-bolder" style="{{ $moduleVars }}">
        <i class="fas {{ $icon }}"></i> {{ $text }}
    </a>
@elseif($url)
    <a href="{{ $url }}" class="btn btn-create-main font-weight-bolder" style="{{ $moduleVars }}">
        <i class="fas {{ $icon }}"></i> {{ $text }}
    </a>
@endif