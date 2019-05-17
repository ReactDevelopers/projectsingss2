<?php namespace Modules\Package\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Package\Entities\Package;
use Modules\Package\Entities\PackageService;
use Modules\Package\Entities\Plan;

class PackageServiceProvider extends ServiceProvider
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

        \Validator::extend('uniquePackageCreate', function($attribute, $value, $parameters, $validator) {
            $check = Package::where($attribute, $value)->whereNull('is_deleted')->get();
            if(count($check) > 0) {
                return false;
            }
            return true;
        });

        \Validator::extend('uniquePackageUpdate', function($attribute, $value, $parameters, $validator) {
            $id = reset($parameters);
            $package = Plan::find($id);
            if(!$package){
                return false;
            }
            if(trim($package->name) != trim($value)){
                $check = Plan::where($attribute, $value)->where('id', '!=', $id)->whereNull('is_deleted')->get();
                if(count($check) > 0) {
                    return false;
                }
            }
            return true;
        });

        \Validator::extend('uniquePackageServiceCreate', function($attribute, $value, $parameters, $validator) {
            $check = PackageService::where($attribute, $value)->whereNull('is_deleted')->get();
            if(count($check) > 0) {
                return false;
            }
            return true;
        });

        \Validator::extend('uniquePackageServiceUpdate', function($attribute, $value, $parameters, $validator) {
            $id = reset($parameters);
            $service = PackageService::find($id);
            if(!$service){
                return false;
            }
            if(trim($service->name) != trim($value)){
                $check = PackageService::where($attribute, $value)->where('id', '!=', $id)->whereNull('is_deleted')->get();
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
            'Modules\Package\Repositories\PackageRepository',
            function () {
                $repository = new \Modules\Package\Repositories\Eloquent\EloquentPackageRepository(new \Modules\Package\Entities\Package());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Package\Repositories\Cache\CachePackageDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Package\Repositories\PackageServiceRepository',
            function () {
                $repository = new \Modules\Package\Repositories\Eloquent\EloquentPackageServiceRepository(new \Modules\Package\Entities\PackageService());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Package\Repositories\Cache\CachePackageServiceDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Package\Repositories\PlanRepository',
            function () {
                $repository = new \Modules\Package\Repositories\Eloquent\EloquentPlanRepository(new \Modules\Package\Entities\Plan());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Package\Repositories\Cache\CachePlanDecorator($repository);
            }
        );
// add bindings


    }
}
