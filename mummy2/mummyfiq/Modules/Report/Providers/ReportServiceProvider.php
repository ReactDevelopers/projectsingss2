<?php namespace Modules\Report\Providers;

use Illuminate\Support\ServiceProvider;

class ReportServiceProvider extends ServiceProvider
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
            'Modules\Report\Repositories\ReviewRepository',
            function () {
                $repository = new \Modules\Report\Repositories\Eloquent\EloquentReviewRepository(new \Modules\Report\Entities\Review());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Report\Repositories\Cache\CacheReviewDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Report\Repositories\CommentRepository',
            function () {
                $repository = new \Modules\Report\Repositories\Eloquent\EloquentCommentRepository(new \Modules\Report\Entities\Comment());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Report\Repositories\Cache\CacheCommentDecorator($repository);
            }
        );
// add bindings


    }
}
