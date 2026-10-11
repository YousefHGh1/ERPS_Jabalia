<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Request::class => \App\Policies\RequestPolicy::class,
        \App\Models\CraftLicense::class => \App\Policies\CraftLicensePolicy::class,
        \App\Models\Mokalaf::class => \App\Policies\MokalafPolicy::class,
        \App\Models\Employee::class => \App\Policies\EmployeePolicy::class,
        \App\Models\Engineering::class => \App\Policies\EngineeringPolicy::class,
        \App\Models\Notice::class => \App\Policies\NoticePolicy::class,
        \App\Models\ExternalCollection::class => \App\Policies\ExternalCollectionPolicy::class,
        \App\Models\ExternalCollectionPayment::class => \App\Policies\ExternalCollectionPaymentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('is_admin', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('super-admin');
        });

        Gate::define('is_super_admin', function ($user) {
            return $user->hasRole('super-admin');
        });

        Gate::define('sidebar_can_see_requests_menu', function ($user) {
            return $user->can('index requests') || $user->can('index request_types');
        });

        Gate::define('sidebar_can_see_request_mail', function ($user) {
            return $user->can('index requests');
        });

        // Allow admin and super-admin to bypass all permission checks
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
                return true;
            }
        });
    }
}
