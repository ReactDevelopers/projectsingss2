<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/banner'], function (Router $router) {
    $router->bind('banner', function ($id) {
        return app('Modules\Banner\Repositories\BannerRepository')->find($id);
    });
    $router->get('banners', [
        'as' => 'admin.banner.banner.index',
        'uses' => 'BannerController@index',
        'middleware' => 'can:banner.banners.index'
    ]);
    $router->get('banners/create', [
        'as' => 'admin.banner.banner.create',
        'uses' => 'BannerController@create',
        'middleware' => 'can:banner.banners.create'
    ]);
    $router->post('banners', [
        'as' => 'admin.banner.banner.store',
        'uses' => 'BannerController@store',
        'middleware' => 'can:banner.banners.store'
    ]);
    $router->get('banners/{banner}/edit', [
        'as' => 'admin.banner.banner.edit',
        'uses' => 'BannerController@edit',
        'middleware' => 'can:banner.banners.edit'
    ]);
    $router->put('banners/{banner}', [
        'as' => 'admin.banner.banner.update',
        'uses' => 'BannerController@update',
        'middleware' => 'can:banner.banners.update'
    ]);
    $router->delete('banners/{banner}', [
        'as' => 'admin.banner.banner.destroy',
        'uses' => 'BannerController@destroy',
        'middleware' => 'can:banner.banners.destroy'
    ]);
// append

});
