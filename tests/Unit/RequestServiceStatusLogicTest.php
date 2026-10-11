<?php

namespace Tests\Unit;

use App\Services\RequestService;
use Tests\TestCase;

class RequestServiceStatusLogicTest extends TestCase
{
    protected function makeService(): RequestService
    {
        return new class extends RequestService
        {
            public function __construct()
            {
            }

            public function isCompletedStatusPublic(?string $statusName): bool
            {
                return $this->isCompletedStatus($statusName);
            }

            public function isWaitingStatusPublic(?string $statusName): bool
            {
                return $this->isWaitingStatus($statusName);
            }
        };
    }

    /** @test */
    public function it_recognizes_completed_status_in_arabic_and_english(): void
    {
        $service = $this->makeService();

        $this->assertTrue($service->isCompletedStatusPublic('مكتمل'));
        $this->assertTrue($service->isCompletedStatusPublic('completed'));
        $this->assertTrue($service->isCompletedStatusPublic(' Completed '));
        $this->assertFalse($service->isCompletedStatusPublic('قيد الانتظار'));
    }

    /** @test */
    public function it_recognizes_waiting_status_in_arabic_and_english(): void
    {
        $service = $this->makeService();

        $this->assertTrue($service->isWaitingStatusPublic('قيد الانتظار'));
        $this->assertTrue($service->isWaitingStatusPublic('pending'));
        $this->assertTrue($service->isWaitingStatusPublic(' Pending '));
        $this->assertFalse($service->isWaitingStatusPublic('مكتمل'));
    }
}
