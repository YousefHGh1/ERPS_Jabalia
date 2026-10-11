<?php

namespace Tests\Unit;

use App\Services\AttendanceService;
use ReflectionProperty;
use Tests\TestCase;

/**
 * اختبارات تحسينات الأداء التي طُبقت:
 *  - ضغط Gzip وترويسات الكاش في public/.htaccess
 *  - تفريغ السكربتات الثقيلة من التحميل العام + التحميل المسبق للتنقل
 *  - نقل env(ZKTECO_IP) إلى config/attendance.php ليعمل config:cache بأمان
 *  - ملف .gitignore لمجلد طرق العرض المُجمّعة
 */
class PerformanceOptimizationTest extends TestCase
{
    /** @test */
    public function attendance_config_exposes_zkteco_ip(): void
    {
        $this->assertFileExists(config_path('attendance.php'));

        $value = config('attendance.zkteco_ip');

        $this->assertIsString($value);
        $this->assertNotSame('', $value, 'قيمة config(attendance.zkteco_ip) يجب ألا تكون فارغة.');
    }

    /** @test */
    public function attendance_service_reads_ip_from_config_not_env(): void
    {
        // regression: كان env('ZKTECO_IP') داخل الخدمة، فيتعطّل عند تفعيل config:cache.
        config()->set('attendance.zkteco_ip', '10.0.0.99');

        $service = $this->app->make(AttendanceService::class);

        $property = new ReflectionProperty($service, 'ip');
        $property->setAccessible(true);

        $this->assertSame('10.0.0.99', $property->getValue($service));
    }

    /** @test */
    public function htaccess_enables_gzip_compression_for_text_assets(): void
    {
        $htaccess = $this->htaccess();

        $this->assertStringContainsString('<IfModule mod_deflate.c>', $htaccess);
        $this->assertStringContainsString('AddOutputFilterByType DEFLATE', $htaccess);
        $this->assertStringContainsString('text/css', $htaccess);
        $this->assertStringContainsString('application/javascript', $htaccess);
        $this->assertStringContainsString('text/html', $htaccess);
    }

    /** @test */
    public function htaccess_does_not_disable_gzip_for_css_and_js(): void
    {
        // regression: كان هناك سطر خاطئ يعطّل الضغط عن CSS/JS (وهي أهم ما يُضغط).
        $this->assertStringNotContainsString('(?:css|js)$ no-gzip', $this->htaccess());
    }

    /** @test */
    public function htaccess_sets_cache_headers_for_static_assets(): void
    {
        $htaccess = $this->htaccess();

        $this->assertStringContainsString('<IfModule mod_expires.c>', $htaccess);
        $this->assertStringContainsString('<IfModule mod_headers.c>', $htaccess);
        $this->assertStringContainsString('Cache-Control', $htaccess);
        $this->assertStringContainsString('max-age=2592000', $htaccess);
        $this->assertStringContainsString('immutable', $htaccess);
        $this->assertStringContainsString('ExpiresByType text/css', $htaccess);
    }

    /** @test */
    public function htaccess_does_not_force_cache_on_dynamic_pages(): void
    {
        // regression: ExpiresDefault كان يمنح صفحات Laravel الحساسة كاش لمدة شهر ويعطّل bfcache.
        $htaccess = $this->htaccess();

        $this->assertStringNotContainsString('ExpiresDefault', $htaccess);
        $this->assertStringNotContainsString('no-store', $htaccess);
    }

    /** @test */
    public function layout_does_not_globally_load_heavy_vendor_scripts(): void
    {
        $script = $this->layoutScript();

        // يجب ألا تُحمَّل مكتبات ثقيلة في كل الصفحات
        $this->assertStringNotContainsString('fullcalendar.bundle.js', $script);
        $this->assertStringNotContainsString('assets/js/pages/widgets.js', $script);

        // بديلها: stack اختياري للصفحات التي تحتاجها
        $this->assertStringContainsString("@stack('vendor')", $script);
    }

    /** @test */
    public function layout_still_loads_core_bundles(): void
    {
        $script = $this->layoutScript();

        $this->assertStringContainsString('plugins.bundle.js', $script);
        $this->assertStringContainsString('scripts.bundle.js', $script);
    }

    /** @test */
    public function layout_contains_prefetch_for_instant_navigation(): void
    {
        $script = $this->layoutScript();

        $this->assertStringContainsString("link.rel = 'prefetch'", $script);
        $this->assertStringContainsString("link.as = 'document'", $script);
        $this->assertStringContainsString("'mouseover'", $script);
        $this->assertStringContainsString("'touchstart'", $script);
    }

    /** @test */
    public function compiled_views_directory_is_git_ignored(): void
    {
        $gitignore = storage_path('framework/views/.gitignore');

        $this->assertFileExists($gitignore);
        $this->assertStringContainsString('*', (string) file_get_contents($gitignore));
    }

    /** @test */
    public function css_bundle_is_available_for_production_layout(): void
    {
        $bundle = public_path('assets/css/vendor.bundle.rtl.min.css');

        if (! file_exists($bundle)) {
            $this->markTestSkipped('حزمة CSS غير مبنية بعد - نفّذ: npm run build:css');
        }

        $this->assertGreaterThan(0, filesize($bundle));
    }

    private function htaccess(): string
    {
        return (string) file_get_contents(public_path('.htaccess'));
    }

    private function layoutScript(): string
    {
        return (string) file_get_contents(resource_path('views/layout/script.blade.php'));
    }
}
