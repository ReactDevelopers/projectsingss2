<?php

$router->bind('subcategory', function ($id) {
    return app('Modules\Category\Repositories\SubCategoryRepository')->find($id);
});
$router->get('subcategories', [
    'as' => 'admin.category.subcategory.index',
    'uses' => 'SubCategoryController@index',
    'middleware' => 'can:category.subcategories.index'
]);
$router->get('subcategories/create', [
    'as' => 'admin.category.subcategory.create',
    'uses' => 'SubCategoryController@create',
    'middleware' => 'can:category.subcategories.create'
]);
$router->post('subcategories', [
    'as' => 'admin.category.subcategory.store',
    'uses' => 'SubCategoryController@store',
    'middleware' => 'can:category.subcategories.store'
]);
$router->get('subcategories/{subcategory}/edit', [
    'as' => 'admin.category.subcategory.edit',
    'uses' => 'SubCategoryController@edit',
    'middleware' => 'can:category.subcategories.edit'
]);
$router->put('subcategories/{subcategory}', [
    'as' => 'admin.category.subcategory.update',
    'uses' => 'SubCategoryController@update',
    'middleware' => 'can:category.subcategories.update'
]);
$router->delete('subcategories/{subcategory}', [
    'as' => 'admin.category.subcategory.destroy',
    'uses' => 'SubCategoryController@destroy',
    'middleware' => 'can:category.subcategories.destroy'
]);