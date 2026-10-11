<?php

namespace App\Providers;

use App\Repositories\RequestRepository;
use App\Services\RequestService;
use App\Services\CentralTreasuryPaymentService;
use App\Services\CacheService;
use App\Services\SecurityService;
use App\Observers\RequestObserver;
use App\Observers\EmployeeObserver;
use App\Models\Request;
use App\Models\Employee;
use App\View\Composers\HomeDashboardComposer;
use App\View\Composers\SidebarComposer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // تسجيل Services و Repositories
        $this->app->singleton(RequestService::class, function ($app) {
            return new RequestService($app->make(CentralTreasuryPaymentService::class));
        });

        $this->app->singleton(RequestRepository::class, function ($app) {
            return new RequestRepository();
        });

        // تسجيل Cache Service
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });

        // تسجيل Security Service
        $this->app->singleton(SecurityService::class, function ($app) {
            return new SecurityService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // تسجيل Observers لمسح الكاش تلقائياً
        Request::observe(RequestObserver::class);
        Employee::observe(EmployeeObserver::class);

        // تجاوز صفحات الإعدادات (الصلاحيات، الأدوار، المستخدمين) بتصميم محسن
        View::addNamespace('PermissionsUI', resource_path('views/settings'));

        // Sidebar: build menu from config and filter by permissions once per request
        View::composer('layout.main-sidebar', SidebarComposer::class);

        // Home dashboard: compute visible sections once to reduce @can calls in home.blade.php
        View::composer('home', HomeDashboardComposer::class);

        // تخزين مؤقت لإشعارات الهيدر (دقيقة واحدة) لتقليل التحميل عند كل F5 أو فتح صفحة
        View::composer('layout.main-header', function ($view) {
            if (! auth()->check()) {
                $view->with('unreadNotificationsCount', 0)->with('latestUnreadNotifications', collect());
                return;
            }
            $user = auth()->user();
            $cacheKey = 'user_notifications_' . $user->id;
            $data = Cache::remember($cacheKey, 30, function () use ($user) {
                try {
                    $latest = $user->unreadNotifications()->limit(5)->get();
                    $count = $user->unreadNotifications()->count();
                    return ['count' => $count, 'latest' => $latest];
                } catch (\Throwable $e) {
                    return ['count' => 0, 'latest' => collect()];
                }
            });
            $view->with('unreadNotificationsCount', $data['count']);
            $view->with('latestUnreadNotifications', $data['latest']);
        });
    }
}

