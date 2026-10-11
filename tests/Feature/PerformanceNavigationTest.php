<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * اختبار عرض طبقة السكربتات (layout.script):
 *  - لا تُحمّل fullcalendar/widgets عالمياً في كل الصفحات
 *  - تحتوي سكربت التحميل المسبق (prefetch) للتنقل الفوري
 *  - توفر @stack('vendor') لتحميل مكتبات ثقيلة عند الحاجة فقط
 */
class PerformanceNavigationTest extends TestCase
{
    /** @test */
    public function rendered_script_partial_omits_heavy_global_vendor_scripts(): void
    {
        $html = view('layout.script')->render();

        $this->assertStringNotContainsString('fullcalendar.bundle.js', $html);
        $this->assertStringNotContainsString('pages/widgets.js', $html);

        // الحزم الأساسية المطلوبة في كل الصفحات تبقى
        $this->assertStringContainsString('plugins.bundle.js', $html);
        $this->assertStringContainsString('scripts.bundle.js', $html);
    }

    /** @test */
    public function rendered_script_partial_contains_prefetch_logic(): void
    {
        $html = view('layout.script')->render();

        $this->assertStringContainsString("link.rel = 'prefetch'", $html);
        $this->assertStringContainsString('mouseover', $html);
        $this->assertStringContainsString('touchstart', $html);
    }

    /** @test */
    public function rendered_script_partial_keeps_scripts_section_output(): void
    {
        // 65 صفحة تستخدم @section('scripts') — يجب أن يبقى الـ yield موجوداً في الطبقة.
        $html = view('layout.script')->render();

        $this->assertStringContainsString('toastr.min.js', $html);
    }
}
