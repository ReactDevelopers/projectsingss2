<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    protected $lang_prefix = '';

    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(Router $router,Request $request)
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes($router,$request);

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes($router,$request)
    {
        $locale = $request->segment(1);

        $enableLang = language();
        $defaultLocale = \App::getLocale();
        
        if(array_key_exists($locale, $enableLang)){
            $this->app->setLocale($locale);
            $this->lang_prefix = $locale;
            if($locale == $defaultLocale){
                $isDefault = true;
            }
        }else{
            $this->app->setLocale($defaultLocale);
        }

        /*$locale = $request->segment(1);
        $config_url = config_path() . DIRECTORY_SEPARATOR . 'multilanguage.json';
        $isDefault = false;

        if (file_exists($config_url)) {

            $params = json_decode(file_get_contents($config_url));
            $enabledLang = $params->enabled;

            if(in_array($locale, $enabledLang)){

               $this->app->setLocale($locale);
               $this->lang_prefix = $locale;
               if($locale == $params->default){

                    $isDefault = true;
               }
            }else{

                $this->app->setLocale($params->default);
            }
        }*/

        Route::group([
            'middleware' => 'web',
            'prefix'=>$this->lang_prefix,
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }
}
