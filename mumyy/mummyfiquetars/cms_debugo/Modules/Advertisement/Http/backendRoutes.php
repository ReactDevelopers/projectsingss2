<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/advertisement'], function (Router $router) {
    $router->bind('advertisement', function ($id) {
        return app('Modules\Advertisement\Repositories\AdvertisementRepository')->find($id);
    });
    $router->get('advertisements', [
        'as' => 'admin.advertisement.advertisement.index',
        'uses' => 'AdvertisementController@index',
        'middleware' => 'can:advertisement.advertisements.index'
    ]);
    $router->get('advertisements/create', [
        'as' => 'admin.advertisement.advertisement.create',
        'uses' => 'AdvertisementController@create',
        'middleware' => 'can:advertisement.advertisements.create'
    ]);
    $router->post('advertisements', [
        'as' => 'admin.advertisement.advertisement.store',
        'uses' => 'AdvertisementController@store',
        'middleware' => 'can:advertisement.advertisements.store'
    ]);
    $router->get('advertisements/{advertisement}/edit', [
        'as' => 'admin.advertisement.advertisement.edit',
        'uses' => 'AdvertisementController@edit',
        'middleware' => 'can:advertisement.advertisements.edit'
    ]);
    $router->put('advertisements/{advertisement}', [
        'as' => 'admin.advertisement.advertisement.update',
        'uses' => 'AdvertisementController@update',
        'middleware' => 'can:advertisement.advertisements.update'
    ]);
    $router->delete('advertisements/{advertisement}', [
        'as' => 'admin.advertisement.advertisement.destroy',
        'uses' => 'AdvertisementController@destroy',
        'middleware' => 'can:advertisement.advertisements.destroy'
    ]);
// append

});
