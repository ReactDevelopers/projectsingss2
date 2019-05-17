<?php namespace Modules\Portfolio\Providers;

use Illuminate\Support\ServiceProvider;

class PortfolioServiceProvider extends ServiceProvider
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
            'Modules\Portfolio\Repositories\PortfolioRepository',
            function () {
                $repository = new \Modules\Portfolio\Repositories\Eloquent\EloquentPortfolioRepository(new \Modules\Portfolio\Entities\Portfolio());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Portfolio\Repositories\Cache\CachePortfolioDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Portfolio\Repositories\PortfolioMediaRepository',
            function () {
                $repository = new \Modules\Portfolio\Repositories\Eloquent\EloquentPortfolioMediaRepository(new \Modules\Portfolio\Entities\PortfolioMedia());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Portfolio\Repositories\Cache\CachePortfolioMediaDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Portfolio\Repositories\PortfolioRequestRepository',
            function () {
                $repository = new \Modules\Portfolio\Repositories\Eloquent\EloquentPortfolioRequestRepository(new \Modules\Portfolio\Entities\Portfolio());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Portfolio\Repositories\Cache\CachePortfolioRequestDecorator($repository);
            }
        );
// add bindings

    }
}
