<?php

namespace App\Observers;

use App\Models\Request;
use App\Services\CacheService;

class RequestObserver
{
    /**
     * Handle the Request "created" event.
     */
    public function created(Request $request): void
    {
        // مسح الكاش المتعلق بالطلبات عند إنشاء طلب جديد
        CacheService::clearRequestsCache();
    }

    /**
     * Handle the Request "updated" event.
     */
    public function updated(Request $request): void
    {
        // مسح الكاش المتعلق بالطلبات عند تحديث طلب
        CacheService::clearRequestsCache();
    }

    /**
     * Handle the Request "deleted" event.
     */
    public function deleted(Request $request): void
    {
        // مسح الكاش المتعلق بالطلبات عند حذف طلب
        CacheService::clearRequestsCache();
    }

    /**
     * Handle the Request "restored" event.
     */
    public function restored(Request $request): void
    {
        CacheService::clearRequestsCache();
    }

    /**
     * Handle the Request "force deleted" event.
     */
    public function forceDeleted(Request $request): void
    {
        CacheService::clearRequestsCache();
    }
}
