<?php namespace Modules\Category\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\SubCategory;
use Modules\Category\Services\CategoryService;

class CategoryServiceProvider extends ServiceProvider
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
        $service = app(CategoryService::class);
        View::share('categories', $service->getCategoryArray());

        \Validator::extend('uniqueCategoryInsert', function($attribute, $value, $parameters, $validator) {
            $check = Category::where($attribute, $value)->whereNull('is_deleted')->get();
            if(count($check) > 0) {
                return false;
            }
            return true;
        });

        \Validator::extend('uniqueCategoryUpdate', function($attribute, $value, $parameters, $validator) {
            $id = reset($parameters);
            $category = Category::find($id);
            if(trim($category) != trim($value)){
                $check = Category::where($attribute, $value)->where('id', '!=', $id)->whereNull('is_deleted')->get();
                if(count($check) > 0) {
                    return false;
                }
            }
            return true;
        });

        \Validator::extend('uniqueSubCategoryInsert', function($attribute, $value, $parameters, $validator) {
            $check = SubCategory::where($attribute, $value)->whereNull('is_deleted')->get();
            if(count($check) > 0) {
                return false;
            }
            return true;
        });

        \Validator::extend('uniqueSubCategoryUpdate', function($attribute, $value, $parameters, $validator) {
            $id = reset($parameters);
            $category = SubCategory::find($id);
            if(trim($category) != trim($value)){
                $check = SubCategory::where($attribute, $value)->where('id', '!=', $id)->whereNull('is_deleted')->get();
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
            'Modules\Category\Repositories\CategoryRepository',
            function () {
                $repository = new \Modules\Category\Repositories\Eloquent\EloquentCategoryRepository(new \Modules\Category\Entities\Category());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Category\Repositories\Cache\CacheCategoryDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Category\Repositories\SubCategoryRepository',
            function () {
                $repository = new \Modules\Category\Repositories\Eloquent\EloquentSubCategoryRepository(new \Modules\Category\Entities\SubCategory());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Category\Repositories\Cache\CacheSubCategoryDecorator($repository);
            }
        );
// add bindings


    }
}
