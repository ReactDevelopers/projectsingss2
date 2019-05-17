<?php namespace Modules\Audittrail\Providers;

use Illuminate\Support\ServiceProvider;

class AuditTrailServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        /*$this->app['audit-trail'] = $this->app->share(function ($app) {
            $AuditTrail = $app->make('Modules\Audittrail\Contracts\AuditTrail');
            return $AuditTrail;
        });*/
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Audittrail\Repositories\LogRepository',
            function () {
                $repository = new \Modules\Audittrail\Repositories\Eloquent\EloquentLogRepository(new \Modules\Audittrail\Entities\Log());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Audittrail\Repositories\Cache\CacheLogDecorator($repository);
            }
        );

        $this->app->bind('audit-trail', function () {
            return $this->app->make("Modules\\Audittrail\\Contracts\\AuditTrail");
        });

        $this->app->bind('Modules\Audittrail\Contracts\AuditTrail', 'Modules\Audittrail\Services\AuditTrail');


        $this->app->bind(
                    'Modules\Audittrail\Transformers\LogTransformerInterface',
                    "Modules\\Audittrail\\Transformers\\LogTransformer"
                );
// add bindings

    }
}
