<script>
    // إخفاء شاشة التحميل بأسرع وقت (عند جاهزية DOM بدلاً من انتظار تحميل كل الموارد)
    (function() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function removeLoading() {
                document.body.classList.remove('page-loading');
            });
        } else {
            document.body.classList.remove('page-loading');
        }
    })();

    // Fix modal shaking/jittering globally
    $(document).on('show.bs.modal', '.modal', function() {
        $(this).find('.modal-dialog').css({
            'transform': 'none',
            'transition': 'none'
        });
    });

    $(document).on('shown.bs.modal', '.modal', function() {
        $(this).find('.modal-dialog').css({
            'transform': 'none',
            'transition': 'none'
        });
    });
</script>
<script>var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";</script>
<!--begin::Global Config(global config for global JS scripts)-->
<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Cairo" };</script>
<!--end::Global Config-->
<!--begin::Global Theme Bundle(used by all pages)-->
<script src="{{asset('assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{asset('assets/plugins/custom/prismjs/prismjs.bundle.js')}}"></script>
<script src="{{asset('assets/js/scripts.bundle.js')}}"></script>
<!--end::Global Theme Bundle-->
<!--begin::Page Vendors (تُحمَّل عند الحاجة فقط عبر @stack('vendor') لتسريع كل الصفحات)-->

<!--end::Page Vendors-->
<!--begin::Page Scripts(used by this page)-->
<script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
<!--end::Page Scripts-->
@stack('vendor')

<script>
    function checkNotifications() {
        $.ajax({
            url: "{{ route('notifications.latest') }}",
            type: "GET",
            success: function(data) {
                if (data.count > 0) {
                    $('#kt_quick_notifications_toggle').find('.label').text(data.count).show();
                    $('#kt_quick_notifications_toggle').find('.pulse-ring').show();
                } else {
                    $('#kt_quick_notifications_toggle').find('.label').hide();
                    $('#kt_quick_notifications_toggle').find('.pulse-ring').hide();
                }
            }
        });
    }

    // تحديث العدد عند تحميل الصفحة ثم كل 30 ثانية
    if (typeof $ !== 'undefined') {
        $(function() { checkNotifications(); });
        setInterval(checkNotifications, 30000);
    }

    // Sidebar favorites (localStorage-based)
    if (typeof $ !== 'undefined') {
        $(function () {
            var STORAGE_KEY = 'jabalia_sidebar_favorites';

            function loadFavorites() {
                try {
                    var raw = localStorage.getItem(STORAGE_KEY);
                    if (!raw) return [];
                    var parsed = JSON.parse(raw);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    return [];
                }
            }

            function saveFavorites(list) {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(list || []));
                } catch (e) {}
            }

            function syncFavoritesUI() {
                var favs = loadFavorites();
                var $menu = $('#kt_aside_menu .menu-nav');
                if (!$menu.length) return;

                // إزالة العناصر المكررة السابقة في المفضلة
                $menu.find('.menu-item-favorite-clone').remove();
                $menu.find('.menu-section[data-fav-section="true"]').remove();

                if (!favs.length) {
                    // إزالة حالة النجمة فقط
                    $menu.find('.menu-fav-toggle').removeClass('is-favorite')
                        .find('i').removeClass('fa-solid').addClass('fa-regular');
                    return;
                }

                // تمييز الروابط المفضلة في أماكنها الأصلية
                $menu.find('li[data-sidebar-key]').each(function () {
                    var key = $(this).data('sidebar-key');
                    var isFav = favs.indexOf(key) !== -1;
                    var $toggle = $(this).find('.menu-fav-toggle').first();
                    if (!$toggle.length) return;
                    if (isFav) {
                        $toggle.addClass('is-favorite');
                        $toggle.find('i').removeClass('fa-regular').addClass('fa-solid');
                    } else {
                        $toggle.removeClass('is-favorite');
                        $toggle.find('i').removeClass('fa-solid').addClass('fa-regular');
                    }
                });

                // إنشاء قسم المفضلة أعلى القائمة
                var $firstSection = $menu.find('.menu-section').first();
                var $favSection = $('<li class="menu-section" data-fav-section="true">' +
                    '<h4 class="menu-text">{{ __("المفضلة") }}</h4>' +
                    '</li>');
                if ($firstSection.length) {
                    $firstSection.before($favSection);
                } else {
                    $menu.prepend($favSection);
                }

                // إضافة الروابط المفضلة (نسخ فقط للعرض السريع)
                favs.forEach(function (key) {
                    var $original = $menu.find('li[data-sidebar-key="' + key + '"]').first();
                    if (!$original.length) return;
                    var $clone = $original.clone(true, true);
                    $clone
                        .addClass('menu-item-favorite-clone menu-item-favorite')
                        .attr('data-sidebar-key-fav', key);
                    // تأكد من أن زر النجمة ظاهر كـ favorite في الكلون
                    $clone.find('.menu-fav-toggle').addClass('is-favorite')
                        .find('i').removeClass('fa-regular').addClass('fa-solid');
                    $favSection.after($clone);
                });
            }

            // حدث الضغط على النجمة
            $(document).on('click', '.menu-fav-toggle', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $li = $(this).closest('li[data-sidebar-key]');
                if (!$li.length) return;
                var key = $li.data('sidebar-key');
                if (!key) return;
                var favs = loadFavorites();
                var idx = favs.indexOf(key);
                if (idx === -1) {
                    favs.push(key);
                } else {
                    favs.splice(idx, 1);
                }
                saveFavorites(favs);
                syncFavoritesUI();
            });

            syncFavoritesUI();
        });
    }

    // PWA: تسجيل Service Worker لنسخة الموبايل (تخزين مؤقت + عمل دون اتصال جزئي)
    if ('serviceWorker' in navigator && (location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1')) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('{{ asset("sw.js") }}').catch(function() {});
        });
    }
</script>

<script>
    (function () {
        function isDark() {
            return document.body.classList.contains('app-theme-dark') || document.body.classList.contains('dashboard-dark');
        }

        function syncToggleIcons(dark) {
            var moon = document.querySelector('#appThemeToggle .theme-icon-moon');
            var sun = document.querySelector('#appThemeToggle .theme-icon-sun');
            if (moon) moon.style.display = dark ? 'none' : 'block';
            if (sun) sun.style.display = dark ? 'block' : 'none';
            var btn = document.getElementById('appThemeToggle');
            if (btn) {
                btn.setAttribute('aria-label', dark ? 'تفعيل الوضع الفاتح' : 'تفعيل الوضع الليلي');
                btn.setAttribute('title', dark ? 'تفعيل الوضع الفاتح' : 'تفعيل الوضع الليلي');
            }
        }

        function applyShellTheme(dark) {
            document.body.classList.toggle('app-theme-dark', dark);
            document.body.classList.toggle('dashboard-dark', dark);
            try {
                localStorage.setItem('theme', dark ? 'dark' : 'light');
                localStorage.setItem('municipality_dashboard_theme', dark ? 'dark' : 'light');
            } catch (e) {}
            syncToggleIcons(dark);
        }

        document.addEventListener('DOMContentLoaded', function () {
            syncToggleIcons(isDark());
            var toggle = document.getElementById('appThemeToggle');
            if (!toggle) return;
            toggle.addEventListener('click', function () {
                applyShellTheme(!isDark());
            });
        });
    })();
</script>

<script>
    // تسريع التنقل: تحميل مسبق (prefetch) للصفحات الداخلية عند مرور المؤشر أو لمس الرابط.
    // يجعل الانتقال بين الصفحات شبه فوري لأن المتصفح يكون قد حمّل الصفحة مسبقاً.
    (function () {
        var prefetched = {};
        function prefetch(url) {
            if (!url || prefetched[url]) return;
            prefetched[url] = true;
            var link = document.createElement('link');
            link.rel = 'prefetch';
            link.as = 'document';
            link.href = url;
            document.head.appendChild(link);
        }
        function onEnter(e) {
            var el = e.target;
            if (!el || typeof el.closest !== 'function') return;
            var a = el.closest('a[href]');
            if (!a || a.target === '_blank' || a.hasAttribute('download')) return;
            var href = a.getAttribute('href');
            if (!href || /^(mailto:|tel:|javascript:|#)/i.test(href)) return;
            try {
                var u = new URL(a.href, location.href);
                if (u.origin !== location.origin) return;
                if (u.pathname === location.pathname) return;
                if (/\.(?:pdf|zip|xlsx?|docx?|jpe?g|png)$/i.test(u.pathname)) return;
                prefetch(u.href);
            } catch (err) {}
        }
        document.addEventListener('mouseover', onEnter, { passive: true });
        document.addEventListener('touchstart', onEnter, { passive: true });
        document.addEventListener('focusin', onEnter, true);
    })();
</script>

@stack('js')
@yield('scripts')
