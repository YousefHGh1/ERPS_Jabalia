<?php

namespace App\Listeners;

use App\Events\RequestCreated;
use App\Events\RealTimeUpdate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Event;

class BroadcastRealTimeUpdates implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(RequestCreated $event)
    {
        // Broadcast real-time update when a new request is created
        broadcast(new RealTimeUpdate([
            'message' => 'تم إنشاء طلب جديد',
            'request_id' => $event->request->id,
            'request_type' => $event->request->requestType->name ?? 'غير محدد',
            'created_by' => $event->request->creator->name ?? 'النظام',
            'urgent' => $event->request->is_urgent,
        ], 'new_request'));

        // Also update general stats
        $this->broadcastStatsUpdate();
    }

    private function broadcastStatsUpdate()
    {
        // Get pending status ID (قيد الانتظار)
        $pendingStatusId = \App\Models\RequestStatus::where('name', 'قيد الانتظار')->first()?->id;
        
        broadcast(new RealTimeUpdate([
            'stats' => [
                'total_requests' => \App\Models\Request::count(),
                'pending_requests' => $pendingStatusId ? \App\Models\Request::where('status_id', $pendingStatusId)->count() : 0,
                'urgent_requests' => $pendingStatusId ? \App\Models\Request::where('is_urgent', true)->where('status_id', $pendingStatusId)->count() : 0,
            ]
        ], 'stats_update'));
    }

    public function failed(RequestCreated $event, \Throwable $exception)
    {
        \Log::error('Failed to broadcast real-time update: ' . $exception->getMessage());
    }
}
