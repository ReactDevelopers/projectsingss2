<?php namespace Modules\Review\Providers;

use Illuminate\Support\ServiceProvider;

class ReviewServiceProvider extends ServiceProvider
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
            'Modules\Review\Repositories\ReviewRepository',
            function () {
                $repository = new \Modules\Review\Repositories\Eloquent\EloquentReviewRepository(new \Modules\Review\Entities\Review());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Review\Repositories\Cache\CacheReviewDecorator($repository);
            }
        );
// add bindings

    }
}
