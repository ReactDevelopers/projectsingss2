<?php

namespace App\Mummy\Api\V1\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        //Quick FiX
        //$this->app->bind(\App\Mummy\Api\V1\Repositories\UserRepository::class, \App\Mummy\Api\V1\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Mummy\Api\V1\Repositories\ProfileRepository::class, \App\Mummy\Api\V1\Repositories\ProfileRepositoryEloquent::class);
    }
}
