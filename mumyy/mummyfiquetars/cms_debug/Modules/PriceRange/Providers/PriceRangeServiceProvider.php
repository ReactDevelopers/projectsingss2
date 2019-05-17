<?php namespace Modules\PriceRange\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\PriceRange\Entities\PriceRange;

class PriceRangeServiceProvider extends ServiceProvider
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

        \Validator::extend('uniquePriceRangeCreate', function($attribute, $value, $parameters, $validator) {
            $check = PriceRange::where($attribute, $value)->whereNull('is_deleted')->get();
            if(count($check) > 0) {
                return false;
            }
            return true;
        });

        \Validator::extend('uniquePriceRangeUpdate', function($attribute, $value, $parameters, $validator) {
            $id = reset($parameters);
            $item = PriceRange::find($id);
            if(!$item){
                return false;
            }
            if(trim($item->name) != trim($value)){
                $check = PriceRange::where($attribute, $value)->where('id', '!=', $id)->whereNull('is_deleted')->get();
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
            'Modules\PriceRange\Repositories\PriceRangeRepository',
            function () {
                $repository = new \Modules\PriceRange\Repositories\Eloquent\EloquentPriceRangeRepository(new \Modules\PriceRange\Entities\PriceRange());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\PriceRange\Repositories\Cache\CachePriceRangeDecorator($repository);
            }
        );
// add bindings

    }
}
