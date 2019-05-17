<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/review'], function (Router $router) {
    $router->bind('review', function ($id) {
        return app('Modules\Review\Repositories\ReviewRepository')->find($id);
    });
    $router->get('reviews', [
        'as' => 'admin.review.review.index',
        'uses' => 'ReviewController@index',
        'middleware' => 'can:review.reviews.index'
    ]);
    $router->get('reviews/create', [
        'as' => 'admin.review.review.create',
        'uses' => 'ReviewController@create',
        'middleware' => 'can:review.reviews.create'
    ]);
    $router->post('reviews', [
        'as' => 'admin.review.review.store',
        'uses' => 'ReviewController@store',
        'middleware' => 'can:review.reviews.store'
    ]);
    $router->get('reviews/{review}/edit', [
        'as' => 'admin.review.review.edit',
        'uses' => 'ReviewController@edit',
        'middleware' => 'can:review.reviews.edit'
    ]);
    $router->put('reviews/{review}', [
        'as' => 'admin.review.review.update',
        'uses' => 'ReviewController@update',
        'middleware' => 'can:review.reviews.update'
    ]);
    $router->delete('reviews/{review}', [
        'as' => 'admin.review.review.destroy',
        'uses' => 'ReviewController@destroy',
        'middleware' => 'can:review.reviews.destroy'
    ]);
});
