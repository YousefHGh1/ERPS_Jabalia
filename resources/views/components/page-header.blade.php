@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'actions' => null,
    'module' => null,
    'showModule' => true,
])

@php
    // هوية القسم (config/module_themes.php): تُستخدم لتلوين الترويسة ومتغيرات CSS داخلها
    $theme = module_theme($module);
    $icon = $icon ?: $theme['icon'];
    $moduleBadge = $showModule && $theme['key'] !== 'dashboard' && $theme['label'] !== '';
    $themeVars = '--module-accent: ' . $theme['color'] . ';'
        . ' --module-accent-rgb: ' . $theme['rgb'] . ';'
        . ' --module-accent-soft: ' . $theme['soft'] . ';'
        . ' --module-accent-strong: ' . $theme['strong'] . ';';
@endphp

<div class="view-page-header" data-module="{{ $theme['key'] }}" style="{{ $themeVars }}">
    <div class="d-flex align-items-center">
        @if($icon)
            <div class="title-icon">
                <i class="fas {{ $icon }}"></i>
            </div>
        @endif
        <div>
            @if($moduleBadge)
                <span class="view-module-chip">
                    <i class="fas {{ $theme['icon'] }}"></i>
                    {{ $theme['label'] }}
                </span>
            @endif
            @if($title)
                <h1 class="h3 mb-1 text-dark font-weight-bolder">{{ $title }}</h1>
            @endif
            @if($subtitle)
                <span class="text-muted font-weight-bold font-size-sm">{{ $subtitle }}</span>
            @endif
        </div>
    </div>
    @if($actions)
        <div class="d-flex flex-wrap" style="gap:8px;">
            {{ $actions }}
        </div>
    @endif
</div>
