<?php namespace Modules\Customer\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Customer\Entities\Customer;

class CustomerServiceProvider extends ServiceProvider
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
        \Validator::extend('uniqueEmailCustomerCreate', function($attribute, $value, $parameters, $validator) {
            $check = Customer::where($attribute, $value)->whereNull('is_deleted')->get();
            if(count($check) > 0) {
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
            'Modules\Customer\Repositories\CustomerRepository',
            function () {
                $repository = new \Modules\Customer\Repositories\Eloquent\EloquentCustomerRepository(new \Modules\Customer\Entities\Customer());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Customer\Repositories\Cache\CacheCustomerDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Customer\Repositories\CustomerSettingRepository',
            function () {
                $repository = new \Modules\Customer\Repositories\Eloquent\EloquentCustomerSettingRepository(new \Modules\Customer\Entities\CustomerSetting());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Customer\Repositories\Cache\CacheCustomerSettingDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Customer\Repositories\CustomerChildrenRepository',
            function () {
                $repository = new \Modules\Customer\Repositories\Eloquent\EloquentCustomerChildrenRepository(new \Modules\Customer\Entities\CustomerChildren());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Customer\Repositories\Cache\CacheCustomerChildrenDecorator($repository);
            }
        );
// add bindings

    }
}
