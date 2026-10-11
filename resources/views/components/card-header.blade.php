@props([
    'title' => null,
    'subtitle' => null,
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

<div class="card-header card-header-create" style="{{ $moduleVars }}">
    <div class="card-title">
        @if($title)
            <h3 class="card-label font-weight-bolder">{{ $title }}</h3>
        @endif
        @if($subtitle)
            <span class="text-muted pt-1 font-size-sm font-weight-normal">{{ $subtitle }}</span>
        @endif
    </div>
    @if($slot->exists('actions'))
        <div class="card-toolbar">
            {{ $actions }}
        </div>
    @endif
</div>