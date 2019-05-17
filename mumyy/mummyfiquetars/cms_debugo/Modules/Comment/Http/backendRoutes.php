<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/comment'], function (Router $router) {
    $router->bind('comment', function ($id) {
        return app('Modules\Comment\Repositories\CommentRepository')->find($id);
    });
    $router->get('comments', [
        'as' => 'admin.comment.comment.index',
        'uses' => 'CommentController@index',
        'middleware' => 'can:comment.comments.index'
    ]);
    $router->get('comments/create', [
        'as' => 'admin.comment.comment.create',
        'uses' => 'CommentController@create',
        'middleware' => 'can:comment.comments.create'
    ]);
    $router->post('comments', [
        'as' => 'admin.comment.comment.store',
        'uses' => 'CommentController@store',
        'middleware' => 'can:comment.comments.store'
    ]);
    $router->get('comments/{comment}/edit', [
        'as' => 'admin.comment.comment.edit',
        'uses' => 'CommentController@edit',
        'middleware' => 'can:comment.comments.edit'
    ]);
    $router->put('comments/{comment}', [
        'as' => 'admin.comment.comment.update',
        'uses' => 'CommentController@update',
        'middleware' => 'can:comment.comments.update'
    ]);
    $router->delete('comments/{comment}', [
        'as' => 'admin.comment.comment.destroy',
        'uses' => 'CommentController@destroy',
        'middleware' => 'can:comment.comments.destroy'
    ]);
    $router->get('comments/export-csv', [
        'as' => 'admin.comment.comment.exportcsv',
        'uses' => 'CommentController@getExportComment',
        'middleware' => 'can:comment.comments.getExportComment'
    ]);


    $router->bind('vendorcomment', function ($id) {
        return app('Modules\Comment\Repositories\VendorcommentRepository')->find($id);
    });
    $router->get('vendorcomments', [
        'as' => 'admin.comment.vendorcomment.index',
        'uses' => 'VendorcommentController@index',
        'middleware' => 'can:comment.vendorcomments.index'
    ]);
    $router->get('vendorcomments/{vendorcomment}/edit', [
        'as' => 'admin.comment.vendorcomment.edit',
        'uses' => 'VendorcommentController@edit',
        'middleware' => 'can:comment.vendorcomments.edit'
    ]);
    $router->put('vendorcomments/{vendorcomment}', [
        'as' => 'admin.comment.vendorcomment.update',
        'uses' => 'VendorcommentController@update',
        'middleware' => 'can:comment.vendorcomments.update'
    ]);
    $router->delete('vendorcomments/{vendorcomment}', [
        'as' => 'admin.comment.vendorcomment.destroy',
        'uses' => 'VendorcommentController@destroy',
        'middleware' => 'can:comment.vendorcomments.destroy'
    ]);
    $router->get('vendorcomments/getExportVendorComment', [
        'as' => 'admin.comment.vendorcomment.getExportVendorComment',
        'uses' => 'VendorcommentController@getExportVendorComment',
        'middleware' => 'can:comment.vendorcomments.getExportVendorComment'
    ]);
    

});
