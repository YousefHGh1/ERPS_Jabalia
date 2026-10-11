@props([
    'route' => null,
    'url' => null,
    'label',
    'icon',
    'active' => [],
    'activeClass' => 'menu-item-active',
    'liveIndicator' => false,
    'submenuClass' => false,
    'iconSvg' => false,
    'target' => '_self',
    'module' => null,
    'accent' => null,
])

@php
    $isActive = sidebar_is_active($active);
    $liClass = 'menu-item';
    if ($submenuClass) {
        $liClass .= ' menu-item-submenu';
    }
    if ($isActive) {
        $liClass .= ' ' . $activeClass;
    }
    $href = $url ?? route($route);
    // لون العنصر: accent الصريح (أولوية) أو لون القسم
    $accentVars = module_theme_accent_vars($accent, $module);
@endphp
<li class="{{ $liClass }}" aria-haspopup="true"
    data-sidebar-key="{{ $route ?? $url }}"
    @if ($module) data-module="{{ $module }}" @endif
    @if ($accentVars) style="{{ $accentVars }}" @endif>
    <a href="{{ $href }}" class="menu-link"
        target="{{ $target }}"
        @if ($isActive) aria-current="page" @endif>
        <span class="svg-icon menu-icon">
            <i class="{{ $icon }}"></i>
        </span>
        <span class="menu-text">{{ __($label) }}</span>
        @if ($liveIndicator)
            <span class="live-indicator ml-2"></span>
        @endif
        <span class="menu-fav-toggle" title="{{ __('تثبيت في المفضلة') }}">
            <i class="fa-regular fa-star"></i>
        </span>
    </a>
</li>
