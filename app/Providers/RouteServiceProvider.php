<?php

namespace App\Providers;

use App\Models\Request as RequestModel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Custom route model binding for 'request' parameter to avoid conflict with Request class
        Route::bind('request', function ($value) {
            return RequestModel::findOrFail($value);
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('password-update', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/user.php'));

            // Domain-based route files (تجميع المسارات حسب الوحدات)
            Route::middleware('web')
                ->group(base_path('routes/mokalafs.php'));

            Route::middleware('web')
                ->group(base_path('routes/requests.php'));

            Route::middleware('web')
                ->group(base_path('routes/crafts.php'));

            Route::middleware('web')
                ->group(base_path('routes/notices.php'));

            Route::middleware('web')
                ->group(base_path('routes/web_performance.php'));
        });
    }
}