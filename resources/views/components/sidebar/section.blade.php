@props(['label', 'module' => null, 'accent' => null])

@php
    // لون رأس القسم: accent الصريح (أولوية) أو لون القسم
    $accentVars = module_theme_accent_vars($accent, $module);
@endphp

<li class="menu-section" @if ($module) data-module="{{ $module }}" @endif
    @if ($accentVars) style="{{ $accentVars }}" @endif>
    <h4 class="menu-text">{{ __($label) }}</h4>
</li>
