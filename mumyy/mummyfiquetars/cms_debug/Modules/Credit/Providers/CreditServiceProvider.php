<?php namespace Modules\Credit\Providers;

use Illuminate\Support\ServiceProvider;

class CreditServiceProvider extends ServiceProvider
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
            'Modules\Credit\Repositories\CreditRepository',
            function () {
                $repository = new \Modules\Credit\Repositories\Eloquent\EloquentCreditRepository(new \Modules\Credit\Entities\Credit());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Credit\Repositories\Cache\CacheCreditDecorator($repository);
            }
        );
// add bindings

    }
}
