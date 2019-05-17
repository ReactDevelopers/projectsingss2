<?php

$router->bind('package', function ($id) {
    return app('Modules\Package\Repositories\PlanRepository')->find($id);
});
$router->get('packages', [
    'as' => 'admin.package.package.index',
    'uses' => 'PackageController@index',
    'middleware' => 'can:package.packages.index'
]);
$router->get('packages/create', [
    'as' => 'admin.package.package.create',
    'uses' => 'PackageController@create',
    'middleware' => 'can:package.packages.create'
]);
$router->post('packages', [
    'as' => 'admin.package.package.store',
    'uses' => 'PackageController@store',
    'middleware' => 'can:package.packages.store'
]);
$router->get('packages/{package}/edit', [
    'as' => 'admin.package.package.edit',
    'uses' => 'PackageController@edit',
    'middleware' => 'can:package.packages.edit'
]);
$router->put('packages/{package}', [
    'as' => 'admin.package.package.update',
    'uses' => 'PackageController@update',
    'middleware' => 'can:package.packages.update'
]);
$router->delete('packages/{package}', [
    'as' => 'admin.package.package.destroy',
    'uses' => 'PackageController@destroy',
    'middleware' => 'can:package.packages.destroy'
]);