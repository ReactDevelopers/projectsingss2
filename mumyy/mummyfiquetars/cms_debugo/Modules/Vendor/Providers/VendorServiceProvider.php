<?php namespace Modules\Vendor\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Modules\Customer\Entities\Country;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Services\VendorService;
use Modules\Vendor\Entities\VendorLocation;

class VendorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {        
        $service = app(VendorService::class);
        View::share('countries', $service->getCountryArray());

        \Validator::extend('uniqueEmailVendorCreate', function($attribute, $value, $parameters, $validator) {
            $check = Vendor::where($attribute, $value)->whereNull('is_deleted')->get();
            if(count($check) > 0) {
                return false;
            }
            return true;
        });

        \Validator::extend('uniqueEmailVendorUpdate', function($attribute, $value, $parameters, $validator) {
            $id = reset($parameters);
            $vendor = Vendor::find($id);
            if(trim($vendor->email) != trim($value)){
                $check = Vendor::where($attribute, $value)->where('id', '!=', $id)->whereNull('is_deleted')->get();
                if(count($check) > 0) {
                    return false;
                }
            }
            return true;
        });

        \Validator::extend('checkLocation', function($attribute, $value, $parameters, $validator) {
            $service = app(VendorService::class);
            $location = $service->getLocation($value);
            if(empty($location)) {
                return false;
            }
            return true;
        });

        \Validator::extend('checkVendorPortfolio', function($attribute, $value, $parameters, $validator) {
            $service = app(VendorService::class);
            $vendor = $service->findBy('id', $parameters[0]);
            $portfolio = $service->getPortfolio($vendor);
            if(empty($portfolio) && $value) {
                return false;
            }
            return true;
        });

        \Validator::extend('checkUniqueLocation', function($attribute, $value, $parameters, $validator) {
            if(isset($parameters[1]) && !empty(isset($parameters[1]))){
                $location = VendorLocation::where('city_id', $value)->where('user_id', '=', $parameters[0])->where('id', '!=', $parameters[1])->first();
            }else{
                $location = VendorLocation::where('city_id', $value)->where('user_id', '=', $parameters[0])->first();
            }
            if(count($location)) {
                return false;
            }
            return true;
        });

        \Validator::extend('alphanumeric', function($attribute, $value, $parameters, $validator) {
            // if(preg_match('/[\W]+/', $value) || preg_match('/_/', $value)){
            //     return false;
            // }

            if(preg_match('/ /', $value)){
                return false;
            }
            
            if(preg_match('/[A-Za-z]/', $value) && preg_match('/[0-9]/', $value)){
                return true;
            }
            return false;
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
            'Modules\Vendor\Repositories\VendorRepository',
            function () {
                $repository = new \Modules\Vendor\Repositories\Eloquent\EloquentVendorRepository(new \Modules\Vendor\Entities\Vendor());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Vendor\Repositories\Cache\CacheVendorDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Vendor\Repositories\VendorProfileRepository',
            function () {
                $repository = new \Modules\Vendor\Repositories\Eloquent\EloquentVendorProfileRepository(new \Modules\Vendor\Entities\VendorProfile());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Vendor\Repositories\Cache\CacheVendorProfileDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Vendor\Repositories\VendorLocationRepository',
            function () {
                $repository = new \Modules\Vendor\Repositories\Eloquent\EloquentVendorLocationRepository(new \Modules\Vendor\Entities\VendorLocation());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Vendor\Repositories\Cache\CacheVendorLocationDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Vendor\Repositories\VendorCategoryRepository',
            function () {
                $repository = new \Modules\Vendor\Repositories\Eloquent\EloquentVendorCategoryRepository(new \Modules\Vendor\Entities\VendorCategory());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Vendor\Repositories\Cache\CacheVendorCategoryDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Vendor\Repositories\VendorPhoneRepository',
            function () {
                $repository = new \Modules\Vendor\Repositories\Eloquent\EloquentVendorPhoneRepository(new \Modules\Vendor\Entities\VendorPhone());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Vendor\Repositories\Cache\CacheVendorPhoneDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Vendor\Repositories\VendorCreditRepository',
            function () {
                $repository = new \Modules\Vendor\Repositories\Eloquent\EloquentVendorCreditRepository(new \Modules\Vendor\Entities\VendorCredit());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Vendor\Repositories\Cache\CacheVendorCreditDecorator($repository);
            }
        );
// add bindings

    }
}
