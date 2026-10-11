<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\EmployeeRepository;
use App\Services\EmployeeService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmployeeRepository::class, function ($app) {
            return new EmployeeRepository(new \App\Models\Employee());
        });

        $this->app->singleton(EmployeeService::class, function ($app) {
            return new EmployeeService($app->make(EmployeeRepository::class));
        });
    }

    public function boot(): void
    {
        //
    }
}
