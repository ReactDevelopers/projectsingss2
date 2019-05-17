<?php

$router->bind('category', function ($id) {
    return app('Modules\Category\Repositories\CategoryRepository')->find($id);
});
$router->get('categories', [
    'as' => 'admin.category.category.index',
    'uses' => 'CategoryController@index',
    'middleware' => 'can:category.categories.index'
]);
$router->get('categories/create', [
    'as' => 'admin.category.category.create',
    'uses' => 'CategoryController@create',
    'middleware' => 'can:category.categories.create'
]);
$router->post('categories', [
    'as' => 'admin.category.category.store',
    'uses' => 'CategoryController@store',
    'middleware' => 'can:category.categories.store'
]);
$router->get('categories/{category}/edit', [
    'as' => 'admin.category.category.edit',
    'uses' => 'CategoryController@edit',
    'middleware' => 'can:category.categories.edit'
]);
$router->put('categories/{category}', [
    'as' => 'admin.category.category.update',
    'uses' => 'CategoryController@update',
    'middleware' => 'can:category.categories.update'
]);
$router->delete('categories/{category}', [
    'as' => 'admin.category.category.destroy',
    'uses' => 'CategoryController@destroy',
    'middleware' => 'can:category.categories.destroy'
]);