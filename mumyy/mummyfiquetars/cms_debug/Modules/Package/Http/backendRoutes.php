<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/package'], function (Router $router) {
    
    include_once('Routes/backend/package.php');
    
    include_once('Routes/backend/packageservice.php');
// append


});
