<?php namespace Modules\Advertisement\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Advertisement\Entities\Advertisement;

class AdvertisementServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot() {
        \Validator::extend('uniqueAdvertisementCreate', function($attribute, $value, $parameters, $validator) {
            $check = Advertisement::where($attribute, $value)->whereNull('is_deleted')->get();
            if(count($check) > 0) {
                return false;
            }
            return true;
        });

        \Validator::extend('uniqueAdvertisementUpdate', function($attribute, $value, $parameters, $validator) {
            $id = reset($parameters);
            $advertisement = Advertisement::find($id);
            if(trim($advertisement->title) != trim($value)){
                $check = Advertisement::where($attribute, $value)->where('id', '!=', $id)->whereNull('is_deleted')->get();
                if(count($check) > 0) {
                    return false;
                }
            }
            return true;
        });
    }

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
            'Modules\Advertisement\Repositories\AdvertisementRepository',
            function () {
                $repository = new \Modules\Advertisement\Repositories\Eloquent\EloquentAdvertisementRepository(new \Modules\Advertisement\Entities\Advertisement());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Advertisement\Repositories\Cache\CacheAdvertisementDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Advertisement\Repositories\AdvertisementTypeRepository',
            function () {
                $repository = new \Modules\Advertisement\Repositories\Eloquent\EloquentAdvertisementTypeRepository(new \Modules\Advertisement\Entities\AdvertisementType());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Advertisement\Repositories\Cache\CacheAdvertisementTypeDecorator($repository);
            }
        );
// add bindings

    }
}
