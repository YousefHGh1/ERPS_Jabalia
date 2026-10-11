<?php

namespace App\Listeners;

use App\Events\UserPermissionsChanged;
use App\View\Composers\SidebarComposer;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener to clear sidebar cache when user permissions change
 */
class ClearSidebarCacheOnPermissionChange implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param UserPermissionsChanged $event
     * @return void
     */
    public function handle(UserPermissionsChanged $event): void
    {
        SidebarComposer::clearCache($event->userId);
    }
}