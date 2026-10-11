{{-- هوية السايدبار: public/css/sidebar-custom.css (متناسقة مع الداشبورد وصفحة الدخول) --}}

<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">

    {{-- Brand --}}
    <div class="brand flex-column-auto" id="kt_brand">
        <a href="{{ route('home.index') }}" class="brand-logo aside-brand">
            <img alt="{{ __('Municipality of Jabalia Al-Nazlah') }}" src="{{ asset('assets/bg/logo-light.png') }}" />
            <span class="aside-brand-copy">
                {{-- <strong>{{ __('Municipality of Jabalia Al-Nazlah') }}</strong> --}}
                <span>{{ __('Municipality_ERP_System') }}</span>
            </span>
        </a>

        <button class="px-0 brand-toggle btn btn-sm btn-icon" id="kt_aside_toggle" aria-label="طي القائمة الجانبية">
            <span class="svg-icon svg-icon-xl">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24" />
                        <path
                            d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z"
                            fill="currentColor" fill-rule="nonzero"
                            transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
                        <path
                            d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z"
                            fill="currentColor" fill-rule="nonzero" opacity="0.3"
                            transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
                    </g>
                </svg>
            </span>
        </button>
    </div>

    {{-- Aside Menu --}}
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
        <div id="kt_aside_menu" class="my-4 aside-menu" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
            <ul class="menu-nav">
                @foreach ($sidebarMenu ?? [] as $item)
                    @if (($item['type'] ?? '') === 'section')
                        <x-sidebar.section :label="$item['label'] ?? ''" :module="$item['module'] ?? null"
                            :accent="$item['accent'] ?? null" />
                    @elseif (($item['type'] ?? '') === 'link')
                        <x-sidebar.link
                            :route="$item['route'] ?? null"
                            :url="$item['url'] ?? null"
                            :label="$item['label'] ?? ''"
                            :icon="$item['icon'] ?? 'fas fa-circle'"
                            :active="$item['active'] ?? []"
                            :activeClass="$item['active_class'] ?? 'menu-item-active'"
                            :liveIndicator="$item['live_indicator'] ?? false"
                            :submenuClass="$item['submenu_class'] ?? false"
                            :iconSvg="$item['icon_svg'] ?? false"
                            :target="$item['target'] ?? '_self'"
                            :module="$item['module'] ?? null"
                            :accent="$item['accent'] ?? null"
                        />
                    @elseif (($item['type'] ?? '') === 'submenu')
                        <x-sidebar.submenu
                            :label="$item['label'] ?? ''"
                            :icon="$item['icon'] ?? 'fas fa-circle'"
                            :active="$item['active'] ?? []"
                            :children="$item['children'] ?? []"
                            :iconSvg="$item['icon_svg'] ?? false"
                            :module="$item['module'] ?? null"
                            :accent="$item['accent'] ?? null"
                        />
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

</div>
