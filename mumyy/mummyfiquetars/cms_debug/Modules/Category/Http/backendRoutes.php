<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/category'], function (Router $router) {
    include_once('Routes/backend/category.php');
    
    include_once('Routes/backend/subcategory.php');

});
