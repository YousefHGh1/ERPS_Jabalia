@props([
    'label',
    'icon',
    'active' => [],
    'children' => [],
    'iconSvg' => false,
    'module' => null,
    'accent' => null,
])

@php
    $isActive = sidebar_is_active($active);
    $liClass = 'menu-item menu-item-submenu';
    // عند تفعيل أحد الروابط الداخلية، نفتح القائمة فقط بدون تلوين رأس القائمة بالأزرق
    if ($isActive) {
        $liClass .= ' menu-item-open';
    }
    // لون العنصر: accent الصريح (أولوية) أو لون القسم
    $accentVars = module_theme_accent_vars($accent, $module);
@endphp
<li class="{{ $liClass }}" aria-haspopup="true" aria-expanded="{{ $isActive ? 'true' : 'false' }}"
    @if ($module) data-module="{{ $module }}" @endif
    @if ($accentVars) style="{{ $accentVars }}" @endif
    data-menu-toggle="hover">
    <a href="javascript:;" class="menu-link menu-toggle">
        <span class="svg-icon menu-icon">
            @if ($iconSvg && $icon === 'bi bi-calendar-day')
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-day" viewBox="0 0 16 16">
                    <path d="M4.684 11.523v-2.3h2.261v-.61H4.684V6.801h2.464v-.61H4v5.332h.684zm3.296 0h.676V8.98c0-.554.227-1.007.953-1.007.125 0 .258.004.329.015v-.613a1.806 1.806 0 0 0-.254-.02c-.582 0-.891.32-1.012.567h-.02v-.504H7.98v4.105zm2.805-5.093c0 .238.192.425.43.425a.428.428 0 1 0 0-.855.426.426 0 0 0-.43.43zm.094 5.093h.672V7.418h-.672v4.105z" />
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                </svg>
            @elseif ($iconSvg && $icon === 'bi bi-diagram-3')
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-diagram-3" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H14a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 2 7h5.5V6A1.5 1.5 0 0 1 6 4.5v-1zM8.5 5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1zM0 11.5A1.5 1.5 0 0 1 1.5 10h1A1.5 1.5 0 0 1 4 11.5v1A1.5 1.5 0 0 1 2.5 14h-1A1.5 1.5 0 0 1 0 12.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm4.5.5A1.5 1.5 0 0 1 7.5 10h1a1.5 1.5 0 0 1 1.5 1.5v1A1.5 1.5 0 0 1 8.5 14h-1A1.5 1.5 0 0 1 6 12.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm4.5.5a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z" />
                </svg>
            @else
                <i class="{{ $icon }}"></i>
            @endif
        </span>
        <span class="menu-text">{{ __($label) }}</span>
        <i class="menu-arrow"></i>
    </a>
    <div class="menu-submenu" kt-hidden-height="80" style="display: {{ $isActive ? 'block' : 'none' }}; overflow: hidden;">
        <i class="menu-arrow"></i>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent" aria-haspopup="true">
                <span class="menu-link"><span class="menu-text">{{ __($label) }}</span></span>
            </li>
            @php
                // تحديد العنصر النشط داخل هذه القائمة الفرعية.
                // القاعدة: نطابق 'active' الخاص بالطفل، أو 'active' الخاص بأي من أبناء child's
                // (القوائم الفرعية المتداخلة) — وإلا بقيت القائمة الأم مغلقة عند فتح
                // صفحة ينتمي إليها أحد أبنائها.
                $activeChildIndex = null;

                $childMatches = function (array $node) use (&$childMatches): bool {
                    if (sidebar_is_active($node['active'] ?? [])) {
                        return true;
                    }

                    foreach (($node['children'] ?? []) as $grandChild) {
                        if ($childMatches($grandChild)) {
                            return true;
                        }
                    }

                    return false;
                };

                foreach ($children as $idx => $childCandidate) {
                    if ($childMatches($childCandidate)) {
                        $activeChildIndex = $idx;
                        break;
                    }
                }
            @endphp
            @foreach ($children as $index => $child)
                @php
                    $childIsActive = $activeChildIndex === $index;
                    $childActiveClass = $child['active_class'] ?? 'menu-item-active';
                    $childLiClass = 'menu-item ' . ($child['li_class'] ?? '');
                    if ($childIsActive) {
                        $childLiClass .= ' ' . $childActiveClass;
                    }
                    $childRoute = $child['route'] ?? null;
                    $childType = $child['type'] ?? 'link';
                    // لون القسم ينتقل للعناصر الفرعية (ما لم يُحدَّد module خاص بالعنصر)
                    $childModule = $child['module'] ?? $module;
                    // لون العنصر المخصص ينتقل للعناصر الفرعية أيضاً
                    $childAccent = $child['accent'] ?? $accent;
                    $childAccentVars = module_theme_accent_vars($childAccent, $childModule);
                @endphp
                
                @if($childType === 'submenu')
                    <!-- Nested submenu -->
                    <x-sidebar.submenu 
                        :label="$child['label']" 
                        :icon="$child['icon'] ?? 'fas fa-circle text-primary'"
                        :active="$child['active'] ?? []"
                        :children="$child['children'] ?? []"
                        :iconSvg="$child['icon_svg'] ?? false"
                        :module="$childModule"
                        :accent="$childAccent"
                    />
                @else
                    <!-- Regular link -->
                    <li class="{{ $childLiClass }}" @if($childRoute) data-sidebar-key="{{ $childRoute }}" @endif
                        @if($childModule) data-module="{{ $childModule }}" @endif
                        @if($childAccentVars) style="{{ $childAccentVars }}" @endif>
                        <a href="{{ $childRoute ? route($childRoute) : 'javascript:;' }}" class="menu-link" @if(!$childRoute) onclick="event.preventDefault();" @endif>
                            <span class="svg-icon menu-icon {{ isset($child['icon_svg']) && $child['icon_svg'] && ($child['icon'] ?? '') === 'bi bi-diagram-3' ? '' : 'mr-2' }}">
                                @if (!empty($child['icon_svg']) && ($child['icon'] ?? '') === 'bi bi-diagram-3')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-diagram-3" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H14a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 2 7h5.5V6A1.5 1.5 0 0 1 6 4.5v-1zM8.5 5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1zM0 11.5A1.5 1.5 0 0 1 1.5 10h1A1.5 1.5 0 0 1 4 11.5v1A1.5 1.5 0 0 1 2.5 14h-1A1.5 1.5 0 0 1 0 12.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm4.5.5A1.5 1.5 0 0 1 7.5 10h1a1.5 1.5 0 0 1 1.5 1.5v1A1.5 1.5 0 0 1 8.5 14h-1A1.5 1.5 0 0 1 6 12.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm4.5.5a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z" />
                                </svg>
                            @else
                                <i class="{{ $child['icon'] ?? 'fas fa-circle text-primary' }}"></i>
                            @endif
                        </span>
                        <span class="menu-text">{{ __($child['label'] ?? '') }}</span>
                        @if (!empty($childRoute))
                            <span class="menu-fav-toggle" title="{{ __('t_thbit_fy_almdhylh') }}">
                                <i class="fa-regular fa-star"></i>
                            </span>
                        @endif
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
</li>
