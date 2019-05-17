<?php

namespace App\Providers;

use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        'vendor' => 'Hotels for Hope',
        'product' => 'Contract Lodge',
        'street' => '336 S. Congress, Ste 512',
        'location' => 'Austin TX 78704',
        'phone' => '512-691-9555',
    ];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = 'nate.ritter@hotelsforhope.com';

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [
        'nate.ritter@hotelsforhope.com',
    ];

    /**
     * Indicates if the application will expose an API.
     *
     * @var bool
     */
    protected $usesApi = true;

    /**
     * Finish configuring Spark for the application.
     *
     * @return void
     */
    public function booted()
    {
        Spark::freePlan()
            ->features([
                "Uh, it's free",
            ]);
    }

    public function register()
    {
        // if ($this->app->environment() == 'production') {
        //     Spark::ensureEmailIsVerified();
        // }
    }
}
