<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/credit'], function (Router $router) {
   
    $router->get('credits', [
        'as' => 'admin.credit.credit.index',
        'uses' => 'CreditController@index',
        'middleware' => 'can:credit.credits.index'
    ]);
    $router->get('credits/create', [
        'as' => 'admin.credit.credit.create',
        'uses' => 'CreditController@create',
        'middleware' => 'can:credit.credits.create'
    ]);
    $router->post('credits', [
        'as' => 'admin.credit.credit.store',
        'uses' => 'CreditController@store',
        'middleware' => 'can:credit.credits.store'
    ]);
    $router->get('credits/{credit}/show', [
        'as' => 'admin.credit.credit.show',
        'uses' => 'CreditController@show',
        'middleware' => 'can:credit.credits.show'
    ]);
// append

});
