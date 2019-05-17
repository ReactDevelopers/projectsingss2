<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/portfolio'], function (Router $router) {
    $router->bind('portfolio', function ($id) {
        return app('Modules\Portfolio\Repositories\PortfolioRepository')->find($id);
    });
    $router->post('file', ['uses' => 'PortfolioController@store', 'as' => 'api.portfolio.store']);
    $router->post('media/link', ['uses' => 'PortfolioController@linkMedia', 'as' => 'api.portfolio.link']);
    $router->post('media/unlink', ['uses' => 'PortfolioController@unlinkMedia', 'as' => 'api.portfolio.unlink']);
    $router->get('media/all', ['uses' => 'PortfolioController@all', 'as' => 'api.portfolio.all', ]);
    $router->post('media/sort', ['uses' => 'PortfolioController@sortMedia', 'as' => 'api.portfolio.sort']);

});
