<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Module Theme Helper
 *
 * يحدّد القسم (Module) الخاص بالصفحة الحالية من اسم المسار أو مسار URL،
 * ويوفّر لونه المميز لاستخدامه في الترويسة والسايدر بار والكروت.
 *
 * الإعدادات في: config/module_themes.php
 */

if (! function_exists('module_themes_config')) {
    /**
     * كل الأقسام المعرّفة وألوانها.
     *
     * @return array<string, mixed>
     */
    function module_themes_config(): array
    {
        return config('module_themes.modules', []);
    }
}

if (! function_exists('resolve_module_theme_key')) {
    /**
     * تحليل مفتاح القسم من اسم مسار أو مسار URL (بدون الاعتماد على الطلب الحالي).
     * مفيدة للاختبار وللأوامر (artisan) والعروض التي تُبنى خارج دورة الطلب.
     */
    function resolve_module_theme_key(string $routeName = '', string $path = ''): string
    {
        $modules = module_themes_config();
        $default = (string) config('module_themes.default', 'dashboard');

        if (empty($modules)) {
            return $default;
        }

        $path = trim($path, '/');
        // إزالة بادئة اللغة إن وُجدت (مثال: ar/engineering)
        $path = preg_replace('#^(ar|en)/#', '', $path) ?? $path;

        // 1) مطابقة اسم المسار (الأدق)
        if ($routeName !== '') {
            $routeKey = strtolower($routeName);
            foreach ($modules as $key => $module) {
                foreach (($module['routes'] ?? []) as $pattern) {
                    if (Str::is(strtolower($pattern), $routeKey)) {
                        return $key;
                    }
                }
            }
        }

        // 2) مطابقة مسار URL
        if ($path !== '') {
            foreach ($modules as $key => $module) {
                foreach (($module['paths'] ?? []) as $pattern) {
                    if (Str::is(strtolower($pattern), strtolower($path))) {
                        return $key;
                    }
                }
            }
        }

        return $default;
    }
}

if (! function_exists('module_theme_key')) {
    /**
     * مفتاح القسم الحالي (مثال: engineering, legal, public_pen).
     * يُحسب مرة واحدة لكل طلب.
     */
    function module_theme_key(): string
    {
        static $resolved = null;

        if ($resolved !== null) {
            return $resolved;
        }

        return $resolved = resolve_module_theme_key(
            Route::current() ? (string) Route::current()->getName() : '',
            (string) request()->path()
        );
    }
}

if (! function_exists('module_theme')) {
    /**
     * بيانات القسم: label, icon, color, rgb + القيم المشتقة.
     *
     * @return array{key: string, label: string, icon: string, color: string, rgb: string, soft: string, strong: string}
     */
    function module_theme(?string $key = null): array
    {
        $key     = $key ?? module_theme_key();
        $modules = module_themes_config();
        $module  = $modules[$key] ?? [];
        $rgb     = (string) ($module['rgb'] ?? '54, 153, 255');

        return [
            'key'    => $key,
            'label'  => (string) ($module['label'] ?? ''),
            'icon'   => (string) ($module['icon'] ?? 'fa-gauge-high'),
            'color'  => (string) ($module['color'] ?? '#3699FF'),
            'rgb'    => $rgb,
            'soft'   => 'rgba(' . $rgb . ', 0.12)',
            'strong' => 'rgba(' . $rgb . ', 0.24)',
        ];
    }
}

if (! function_exists('module_theme_color')) {
    /**
     * لون قسم معيّن (بمفتاح صريح) — يُستخدم في السايدر بار.
     */
    function module_theme_color(string $key): string
    {
        return (string) (module_themes_config()[$key]['color'] ?? '#3699FF');
    }
}

if (! function_exists('module_theme_rgb_from_hex')) {
    /**
     * تحويل لون HEX إلى صيغة rgb مفصولة بفواصل (مطلوبة لبناء درجات الشفافية).
     * مثال: '#3699FF' => '54, 153, 255'
     */
    function module_theme_rgb_from_hex(string $hex): string
    {
        $hex = ltrim(trim($hex), '#');

        // اختصار (مثال: #39F)
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return '54, 153, 255';
        }

        return sprintf(
            '%d, %d, %d',
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        );
    }
}

if (! function_exists('module_theme_accent_vars')) {
    /**
     * متغيرات CSS للون عنصر السايدر بار.
     *
     * الأولوية للون الصريح (accent) إن وُجد — ويُستخدم لتمييز عناصر
     * متعددة تشترك في نفس القسم (مثال: عناصر لوحة التحكم) بألوان مختلفة.
     * وإلا يُستخدم لون القسم (module).
     */
    function module_theme_accent_vars(?string $accent = null, ?string $moduleKey = null): string
    {
        $color = $accent;

        if (! $color && $moduleKey && isset(module_themes_config()[$moduleKey])) {
            $color = module_theme_color($moduleKey);
        }

        if (! $color) {
            return '';
        }

        return sprintf(
            '--item-accent: %s; --item-accent-rgb: %s;',
            $color,
            module_theme_rgb_from_hex($color)
        );
    }
}

if (! function_exists('module_theme_style_vars')) {
    /**
     * متغيرات CSS للقسم الحالي (تُحقن على وسم <body>).
     */
    function module_theme_style_vars(?string $key = null): string
    {
        $theme = module_theme($key);

        return sprintf(
            '--module-accent: %s; --module-accent-rgb: %s; --module-accent-soft: %s; --module-accent-strong: %s;',
            $theme['color'],
            $theme['rgb'],
            $theme['soft'],
            $theme['strong']
        );
    }
}

if (! function_exists('module_theme_class')) {
    /**
     * كلاس القسم للاستخدام على <body> (مثال: module-engineering).
     */
    function module_theme_class(?string $key = null): string
    {
        return 'module-' . ($key ?? module_theme_key());
    }
}
