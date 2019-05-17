<?php

namespace App\Providers;

use App\Mummy\Api\V1\Middleware\ApiLog;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('ApiLog', function ($app) {
            return new ApiLog();
        });

        //API quickfix for L5
        $this->app->bind(\App\Mummy\Api\V1\Repositories\UserRepository::class, \App\Mummy\Api\V1\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Mummy\Api\V1\Repositories\CustomerRepository::class, \App\Mummy\Api\V1\Repositories\CustomerRepositoryEloquent::class);
        $this->app->bind(\App\Mummy\Api\V1\Repositories\VendorRepository::class, \App\Mummy\Api\V1\Repositories\VendorRepositoryEloquent::class);
    }
}
