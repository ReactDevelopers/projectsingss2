<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Mummy\Api\V1\Events\SendMessageVendor' => [
            'App\Mummy\Api\V1\Listeners\SendMessageEmailToVendor',
        ],
        'App\Mummy\Api\V1\Events\SendReviewVendor' => [
            'App\Mummy\Api\V1\Listeners\SendReviewEmailToVendor',
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
