<?php

$router->bind('packageservice', function ($id) {
    return app('Modules\Package\Repositories\PackageServiceRepository')->find($id);
});
$router->get('packageservices', [
    'as' => 'admin.package.packageservice.index',
    'uses' => 'PackageServiceController@index',
    'middleware' => 'can:package.packageservices.index'
]);
$router->get('packageservices/create', [
    'as' => 'admin.package.packageservice.create',
    'uses' => 'PackageServiceController@create',
    'middleware' => 'can:package.packageservices.create'
]);
$router->post('packageservices', [
    'as' => 'admin.package.packageservice.store',
    'uses' => 'PackageServiceController@store',
    'middleware' => 'can:package.packageservices.store'
]);
$router->get('packageservices/{packageservice}/edit', [
    'as' => 'admin.package.packageservice.edit',
    'uses' => 'PackageServiceController@edit',
    'middleware' => 'can:package.packageservices.edit'
]);
$router->put('packageservices/{packageservice}', [
    'as' => 'admin.package.packageservice.update',
    'uses' => 'PackageServiceController@update',
    'middleware' => 'can:package.packageservices.update'
]);
$router->delete('packageservices/{packageservice}', [
    'as' => 'admin.package.packageservice.destroy',
    'uses' => 'PackageServiceController@destroy',
    'middleware' => 'can:package.packageservices.destroy'
]);