<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        

        $this->app->validator->resolver(function($translator, $data, $rules, $messages)
        {
            return new \App\Validate\ExtendedValidator($translator, $data, $rules, $messages);  
        });

        
        // $this->app->singleton('Illuminate\Contracts\Routing\ResponseFactory', function ($app) {
        //     return new \Illuminate\Routing\ResponseFactory(
        //         $app['Illuminate\Contracts\View\Factory'],
        //         $app['Illuminate\Routing\Redirector']
        //     );
        // });

        ini_set('memory_limit','-1');
        ini_set('max_execution_time', "-1");

    }

    function boot()
    {
        Schema::defaultStringLength(255); //Solved by increasing StringLength
        
    }


}
