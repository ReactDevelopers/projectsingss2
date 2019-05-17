<?php namespace Modules\Banner\Providers;

use Illuminate\Support\ServiceProvider;

class BannerServiceProvider extends ServiceProvider
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
            'Modules\Banner\Repositories\BannerRepository',
            function () {
                $repository = new \Modules\Banner\Repositories\Eloquent\EloquentBannerRepository(new \Modules\Banner\Entities\Banner());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Banner\Repositories\Cache\CacheBannerDecorator($repository);
            }
        );
// add bindings

    }
}
