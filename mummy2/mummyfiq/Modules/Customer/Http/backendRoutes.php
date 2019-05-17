<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/customer'], function (Router $router) {
    $router->bind('customer', function ($id) {
        return app('Modules\Customer\Repositories\CustomerRepository')->find($id);
    });
    $router->get('customers', [
        'as' => 'admin.customer.customer.index',
        'uses' => 'CustomerController@index',
        'middleware' => 'can:customer.customers.index'
    ]);
    $router->get('customers/create', [
        'as' => 'admin.customer.customer.create',
        'uses' => 'CustomerController@create',
        'middleware' => 'can:customer.customers.create'
    ]);
    $router->post('customers', [
        'as' => 'admin.customer.customer.store',
        'uses' => 'CustomerController@store',
        'middleware' => 'can:customer.customers.store'
    ]);
    $router->get('customers/{customer}/edit', [
        'as' => 'admin.customer.customer.edit',
        'uses' => 'CustomerController@edit',
        'middleware' => 'can:customer.customers.edit'
    ]);
    $router->put('customers/{customer}', [
        'as' => 'admin.customer.customer.update',
        'uses' => 'CustomerController@update',
        'middleware' => 'can:customer.customers.update'
    ]);
    $router->delete('customers/{customer}', [
        'as' => 'admin.customer.customer.destroy',
        'uses' => 'CustomerController@destroy',
        'middleware' => 'can:customer.customers.destroy'
    ]);

    $router->get('customers/export-csv', [
        'as' => 'admin.customer.customer.exportcsv',
        'uses' => 'CustomerController@getExportCustomer',
        'middleware' => 'can:customer.customers.getExportCustomer'
    ]);

});
