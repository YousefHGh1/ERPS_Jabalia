<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register module services
        $this->registerModuleServices();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load module routes
        $this->loadModuleRoutes();
        
        // Load module views
        $this->loadModuleViews();
        
        // Load module migrations
        $this->loadModuleMigrations();
    }

    /**
     * Register module services
     */
    private function registerModuleServices(): void
    {
        // Craft Licenses Module
        $this->app->singleton('App\Modules\CraftLicenses\Services\CraftLicenseService', function ($app) {
            return new \App\Modules\CraftLicenses\Services\CraftLicenseService();
        });

        // Engineering Module
        $this->app->singleton('App\Modules\Engineering\Services\EngineeringService', function ($app) {
            return new \App\Modules\Engineering\Services\EngineeringService();
        });

        // Employees Module
        $this->app->singleton('App\Modules\Employees\Services\EmployeeService', function ($app) {
            return new \App\Modules\Employees\Services\EmployeeService();
        });
    }

    /**
     * Load module routes
     */
    private function loadModuleRoutes(): void
    {
        $modules = ['CraftLicenses', 'Engineering', 'Employees'];

        foreach ($modules as $module) {
            $routeFile = base_path("app/Modules/{$module}/routes/web.php");
            if (file_exists($routeFile)) {
                Route::middleware('web')
                    ->prefix(strtolower($module))
                    ->name(strtolower($module) . ".")
                    ->group($routeFile);
            }

            $apiRouteFile = base_path("app/Modules/{$module}/routes/api.php");
            if (file_exists($apiRouteFile)) {
                Route::middleware('api')
                    ->prefix("api/".strtolower($module))
                    ->name("api." . strtolower($module) . ".")
                    ->group($apiRouteFile);
            }
        }
    }

    /**
     * Load module views
     */
    private function loadModuleViews(): void
    {
        $modules = ['CraftLicenses', 'Engineering', 'Employees'];

        foreach ($modules as $module) {
            $viewPath = base_path("app/Modules/{$module}/Views");
            if (is_dir($viewPath)) {
                $this->loadViewsFrom($viewPath, strtolower($module));
            }
        }
    }

    /**
     * Load module migrations
     */
    private function loadModuleMigrations(): void
    {
        $modules = ['CraftLicenses', 'Engineering', 'Employees'];

        foreach ($modules as $module) {
            $migrationPath = base_path("app/Modules/{$module}/Database/Migrations");
            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'App\Modules\CraftLicenses\Services\CraftLicenseService',
            'App\Modules\Engineering\Services\EngineeringService',
            'App\Modules\Employees\Services\EmployeeService',
        ];
    }
}
