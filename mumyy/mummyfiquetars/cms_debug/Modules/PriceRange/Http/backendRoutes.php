<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/pricerange'], function (Router $router) {
    $router->bind('pricerange', function ($id) {
        return app('Modules\PriceRange\Repositories\PriceRangeRepository')->find($id);
    });
    $router->get('priceranges', [
        'as' => 'admin.pricerange.pricerange.index',
        'uses' => 'PriceRangeController@index',
        'middleware' => 'can:pricerange.priceranges.index'
    ]);
    $router->get('priceranges/create', [
        'as' => 'admin.pricerange.pricerange.create',
        'uses' => 'PriceRangeController@create',
        'middleware' => 'can:pricerange.priceranges.create'
    ]);
    $router->post('priceranges', [
        'as' => 'admin.pricerange.pricerange.store',
        'uses' => 'PriceRangeController@store',
        'middleware' => 'can:pricerange.priceranges.store'
    ]);
    $router->get('priceranges/{pricerange}/edit', [
        'as' => 'admin.pricerange.pricerange.edit',
        'uses' => 'PriceRangeController@edit',
        'middleware' => 'can:pricerange.priceranges.edit'
    ]);
    $router->put('priceranges/{pricerange}', [
        'as' => 'admin.pricerange.pricerange.update',
        'uses' => 'PriceRangeController@update',
        'middleware' => 'can:pricerange.priceranges.update'
    ]);
    $router->delete('priceranges/{pricerange}', [
        'as' => 'admin.pricerange.pricerange.destroy',
        'uses' => 'PriceRangeController@destroy',
        'middleware' => 'can:pricerange.priceranges.destroy'
    ]);
// append

});
