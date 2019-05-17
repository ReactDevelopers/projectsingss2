<?php namespace Modules\Version\Providers;

use Illuminate\Support\ServiceProvider;

class VersionServiceProvider extends ServiceProvider
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
            'Modules\Version\Repositories\VersionRepository',
            function () {
                $repository = new \Modules\Version\Repositories\Eloquent\EloquentVersionRepository(new \Modules\Version\Entities\Version());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Version\Repositories\Cache\CacheVersionDecorator($repository);
            }
        );
// add bindings

    }
}
