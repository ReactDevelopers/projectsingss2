<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/report'], function (Router $router) {
    $router->bind('reportreview', function ($id) {
        return app('Modules\Report\Repositories\ReviewRepository')->find($id);
    });
    $router->get('reviews', [
        'as' => 'admin.report.review.index',
        'uses' => 'ReviewController@index',
        'middleware' => 'can:report.reviews.index'
    ]);
    $router->get('reviews/{reportreview}/detail', [
        'as' => 'admin.report.review.detail',
        'uses' => 'ReviewController@detail',
        'middleware' => 'can:report.reviews.detail'
    ]);
    $router->delete('reviews/{reportreview}', [
        'as' => 'admin.report.review.destroy',
        'uses' => 'ReviewController@destroy',
        'middleware' => 'can:report.reviews.destroy'
    ]);
    $router->get('reviews/exportcsv', [
        'as' => 'admin.report.review.exportcsv',
        'uses' => 'ReviewController@exportcsv',
        'middleware' => 'can:report.reviews.exportcsv'
    ]);
    $router->bind('reportcomment', function ($id) {
        return app('Modules\Report\Repositories\CommentRepository')->find($id);
    });
    $router->get('comments', [
        'as' => 'admin.report.comment.index',
        'uses' => 'CommentController@index',
        'middleware' => 'can:report.comments.index'
    ]);
    $router->get('comments/exportcsv', [
        'as' => 'admin.report.comment.exportcsv',
        'uses' => 'CommentController@exportcsv',
        'middleware' => 'can:report.comments.exportcsv'
    ]);
    $router->get('comments/{reportcomment}/detail', [
        'as' => 'admin.report.comment.detail',
        'uses' => 'CommentController@detail',
        'middleware' => 'can:report.comments.detail'
    ]);
    $router->delete('comments/{reportcomment}', [
        'as' => 'admin.report.comment.destroy',
        'uses' => 'CommentController@destroy',
        'middleware' => 'can:report.comments.destroy'
    ]);
// append


});
