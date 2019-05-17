<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/portfolio'], function (Router $router) {
    $router->bind('portfolio', function ($id) {
        return app('Modules\Portfolio\Repositories\PortfolioRepository')->find($id);
    });
    $router->get('portfolios', [
        'as' => 'admin.portfolio.portfolio.index',
        'uses' => 'PortfolioController@index',
        'middleware' => 'can:portfolio.portfolios.index'
    ]);
    $router->get('portfolios/create', [
        'as' => 'admin.portfolio.portfolio.create',
        'uses' => 'PortfolioController@create',
        'middleware' => 'can:portfolio.portfolios.create'
    ]);
    $router->post('portfolios', [
        'as' => 'admin.portfolio.portfolio.store',
        'uses' => 'PortfolioController@store',
        'middleware' => 'can:portfolio.portfolios.store'
    ]);
    $router->get('portfolios/{portfolio}/edit', [
        'as' => 'admin.portfolio.portfolio.edit',
        'uses' => 'PortfolioController@edit',
        'middleware' => 'can:portfolio.portfolios.edit'
    ]);
    $router->put('portfolios/{portfolio}', [
        'as' => 'admin.portfolio.portfolio.update',
        'uses' => 'PortfolioController@update',
        'middleware' => 'can:portfolio.portfolios.update'
    ]);
    $router->delete('portfolios/{portfolio}', [
        'as' => 'admin.portfolio.portfolio.destroy',
        'uses' => 'PortfolioController@destroy',
        'middleware' => 'can:portfolio.portfolios.destroy'
    ]);

    $router->get('portfolios/export-csv', [
        'as' => 'admin.portfolio.portfolio.exportcsv',
        'uses' => 'PortfolioController@getExportPortfolio',
        'middleware' => 'can:portfolio.portfolios.getExportPortfolio'
    ]);

    $router->post('portfolios/fecthIndex', ['as' => 'admin.portfolio.portfolio.fetchIndex', 'uses' => 'PortfolioController@fetchdataIndex']);

    $router->bind('portfoliorequest', function ($id) {
        return app('Modules\Portfolio\Repositories\PortfolioRequestRepository')->find($id);
    });
    $router->get('portfoliorequest', [
        'as' => 'admin.portfolio.portfoliorequest.index',
        'uses' => 'PortfolioRequestController@index',
        'middleware' => 'can:portfolio.portfoliorequest.index'
    ]);
    $router->get('portfoliorequest/{portfoliorequest}/edit', [
        'as' => 'admin.portfolio.portfoliorequest.edit',
        'uses' => 'PortfolioRequestController@edit',
        'middleware' => 'can:portfolio.portfoliorequest.edit'
    ]);
    $router->put('portfoliorequest/{portfoliorequest}', [
        'as' => 'admin.portfolio.portfoliorequest.update',
        'uses' => 'PortfolioRequestController@update',
        'middleware' => 'can:portfolio.portfoliorequest.update'
    ]);
    $router->get('portfoliorequest/{portfoliorequest}/approve', [
        'as' => 'admin.portfolio.portfoliorequest.approve',
        'uses' => 'PortfolioRequestController@approve',
        'middleware' => 'can:portfolio.portfoliorequest.index'
    ]);
    $router->get('portfoliorequest/{portfoliorequest}/reject', [
        'as' => 'admin.portfolio.portfoliorequest.reject',
        'uses' => 'PortfolioRequestController@reject',
        'middleware' => 'can:portfolio.portfoliorequest.index'
    ]);
    $router->delete('portfoliorequest/{portfoliorequest}', [
        'as' => 'admin.portfolio.portfoliorequest.destroy',
        'uses' => 'PortfolioRequestController@destroy',
        'middleware' => 'can:portfolio.portfoliorequest.destroy'
    ]);
// append

});
