@php
    $unreadCount = $unreadNotificationsCount ?? 0;
    $userName = Auth::user()->name ?? '';
    $userInitial = $userName !== '' ? mb_substr($userName, 0, 1) : '?';
@endphp

<div id="kt_header" class="header header-fixed">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                <a href="{{ route('home.index') }}" class="app-header-brand">
                    <div class="brand-logo"><img src="{{ asset('assets/bg/logo-light.png') }}" alt="شعار البلدية"
                            onerror="this.style.display='none'"></div>
                    <div class="brand-titles">
                        <h4 class="brand-title">{{ __('Municipality of Jabalia Al-Nazlah') }}</h4>
                        {{-- <span class="brand-subtitle">{{ __('Administrative Affairs') }}</span> --}}
                    </div>
                </a>
            </div>
        </div>

        <div class="topbar">
            <button type="button" class="app-theme-toggle mr-1" id="appThemeToggle" aria-label="تبديل الوضع الليلي"
                title="تبديل الوضع الليلي">
                <svg class="theme-icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
                <svg class="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true" style="display:none;">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
            </button>

            <div class="dropdown">
                <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                    <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1 pulse pulse-primary"
                        id="kt_quick_notifications_toggle">
                        <span class="svg-icon svg-icon-xl svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <path
                                        d="M17,12 L18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 C4,12.6715729 4.67157288,12 5.5,12 L7,12 L7,9 C7,6.23857625 9.23857625,4 12,4 C14.7614237,4 17,6.23857625 17,9 L17,12 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M10,19 L14,19 C14,20.1045695 13.1045695,21 12,21 C10.8954305,21 10,20.1045695 10,19 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        @if ($unreadCount > 0)
                            <span class="pulse-ring"></span>
                            <span class="label label-sm label-danger label-inline font-weight-bold position-absolute"
                                style="top: 10px; right: 5px;">{{ $unreadCount }}</span>
                        @endif
                    </div>
                </div>
                <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg">
                    <form>
                        <div class="d-flex flex-column rounded-top app-notify-head">
                            <h4 class="d-flex flex-center rounded-top mb-0">
                                <span>{{ __('Notifications') }}</span>
                                <span
                                    class="btn btn-text btn-success btn-sm font-weight-bold btn-font-md ml-2">{{ $unreadCount }}
                                    {{ __('new') }}</span>
                            </h4>
                            <ul class="nav nav-bold nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-transparent-white nav-tabs-line-active-border-success mt-3 px-8"
                                role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active show" data-toggle="tab"
                                        href="#topbar_notifications_events">{{ __('Events') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active show p-8" id="topbar_notifications_events" role="tabpanel">
                                <div class="scroll pr-7 mr-n7" data-scroll="true" data-height="300"
                                    data-mobile-height="200">
                                    @php
                                        $latestNotifications = $latestUnreadNotifications ?? collect();
                                    @endphp
                                    @forelse($latestNotifications as $notification)
                                        @php
                                            $notificationLink = $notification->link ?? '#';
                                            if (is_string($notificationLink) && $notificationLink !== '#') {
                                                $parts = parse_url($notificationLink);
                                                $host = strtolower((string) ($parts['host'] ?? ''));
                                                if (in_array($host, ['127.0.0.1', 'localhost'], true)) {
                                                    $path = $parts['path'] ?? '/';
                                                    $query = isset($parts['query']) ? '?' . $parts['query'] : '';
                                                    $fragment = isset($parts['fragment'])
                                                        ? '#' . $parts['fragment']
                                                        : '';
                                                    $notificationLink = $path . $query . $fragment;
                                                }
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="symbol symbol-40 symbol-light-primary mr-5">
                                                <span class="symbol-label">
                                                    <span class="svg-icon svg-icon-lg svg-icon-primary">
                                                        <i class="fa {{ $notification->icon }} text-primary"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column font-weight-bold">
                                                <a href="{{ $notificationLink }}"
                                                    class="text-dark text-hover-primary mb-1 font-size-lg">{{ $notification->title }}</a>
                                                <span class="text-muted">{{ $notification->message }}</span>
                                                <span
                                                    class="text-muted font-size-sm">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="d-flex flex-center text-center text-muted min-h-200px">
                                            {{ __('No new notifications') }}
                                        </div>
                                    @endforelse
                                </div>
                                <div class="d-flex flex-center pt-7">
                                    <a href="{{ route('notifications.index') }}"
                                        class="btn btn-light-primary font-weight-bold text-center">{{ __('See All') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="topbar-item">
                <div class="app-user-chip btn btn-clean d-flex align-items-center"
                    id="kt_quick_user_toggle" role="button" tabindex="0" aria-label="{{ __('profile_page') }}">
                    <span class="user-hello d-none d-md-inline">أهلاً،</span>
                    <span class="user-name d-none d-md-inline">{{ $userName }}</span>
                    <span class="symbol symbol-lg-35 symbol-25">
                        <span class="symbol-label font-size-h5 font-weight-bold">{{ $userInitial }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
