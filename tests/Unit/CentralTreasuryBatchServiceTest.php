<?php

namespace Tests\Unit;

use App\Services\CentralTreasuryBatchService;
use RuntimeException;
use Tests\TestCase;

class CentralTreasuryBatchServiceTest extends TestCase
{
    public function test_throws_when_section_id_missing(): void
    {
        $service = $this->app->make(CentralTreasuryBatchService::class);

        config()->set('central_treasury.revenue_sources.public_pen_requests.section_id', null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('لم يُضبط قسم الصندوق المركزي لمصدر «public_pen_requests»');

        $service->resolveSectionIdForSource('public_pen_requests');
    }

    public function test_returns_section_id_when_configured(): void
    {
        $service = $this->app->make(CentralTreasuryBatchService::class);

        config()->set('central_treasury.revenue_sources.public_pen_requests.section_id', 7);

        $this->assertSame(7, $service->resolveSectionIdForSource('public_pen_requests'));
    }
}

