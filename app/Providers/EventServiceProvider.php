<?php

namespace App\Providers;

use App\Events\RequestCreated;
use App\Events\RealTimeUpdate;
use App\Events\UserPermissionsChanged;
use App\Events\EmployeeCreated;
use App\Events\EmployeeUpdated;
use App\Events\EmployeeDeleted;
use App\Listeners\SendRequestNotifications;
use App\Listeners\BroadcastRealTimeUpdates;
use App\Listeners\ClearSidebarCacheOnPermissionChange;
use App\Listeners\LogEmployeeActivity;
use App\Models\RequestResponse;
use App\Observers\RequestResponseObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        RequestCreated::class => [
            // SendRequestNotifications يرسل إشعارات مكررة - يتم الإرسال من RequestService::createWorkflowResponses
            BroadcastRealTimeUpdates::class,
        ],

        UserPermissionsChanged::class => [
            ClearSidebarCacheOnPermissionChange::class,
        ],

        EmployeeCreated::class => [
            LogEmployeeActivity::class,
        ],

        EmployeeUpdated::class => [
            LogEmployeeActivity::class,
        ],

        EmployeeDeleted::class => [
            LogEmployeeActivity::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Register Observer for RequestResponse
        RequestResponse::observe(RequestResponseObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
