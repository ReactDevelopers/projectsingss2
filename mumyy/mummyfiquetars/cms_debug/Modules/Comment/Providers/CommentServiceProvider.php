<?php namespace Modules\Comment\Providers;

use Illuminate\Support\ServiceProvider;

class CommentServiceProvider extends ServiceProvider
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
            'Modules\Comment\Repositories\CommentRepository',
            function () {
                $repository = new \Modules\Comment\Repositories\Eloquent\EloquentCommentRepository(new \Modules\Comment\Entities\Comment());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Comment\Repositories\Cache\CacheCommentDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Comment\Repositories\VendorcommentRepository',
            function () {
                $repository = new \Modules\Comment\Repositories\Eloquent\EloquentVendorcommentRepository(new \Modules\Comment\Entities\Vendorcomment());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Comment\Repositories\Cache\CacheVendorcommentDecorator($repository);
            }
        );
// add bindings

    }
}
