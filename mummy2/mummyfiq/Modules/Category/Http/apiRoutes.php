<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->post('fetch', ['uses' => 'CategoryController@fetch', 'as' => 'api.category.fetch']);
$router->get('get', ['uses' => 'CategoryController@get', 'as' => 'api.category.get']);
