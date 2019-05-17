<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/vendor'], function (Router $router) {
    $router->bind('vendor', function ($id) {
        return app('Modules\Vendor\Repositories\VendorRepository')->find($id);
    });
    $router->get('vendors', [
        'as' => 'admin.vendor.vendor.index',
        'uses' => 'VendorController@index',
        'middleware' => 'can:vendor.vendors.index'
    ]);
    $router->get('vendors/create', [
        'as' => 'admin.vendor.vendor.create',
        'uses' => 'VendorController@create',
        'middleware' => 'can:vendor.vendors.create'
    ]);
    $router->post('vendors', [
        'as' => 'admin.vendor.vendor.store',
        'uses' => 'VendorController@store',
        'middleware' => 'can:vendor.vendors.store'
    ]);
    $router->get('vendors/{vendor}/edit', [
        'as' => 'admin.vendor.vendor.edit',
        'uses' => 'VendorController@edit',
        'middleware' => 'can:vendor.vendors.edit'
    ]);
    $router->put('vendors/{vendor}', [
        'as' => 'admin.vendor.vendor.update',
        'uses' => 'VendorController@update',
        'middleware' => 'can:vendor.vendors.update'
    ]);
    $router->delete('vendors/{vendor}', [
        'as' => 'admin.vendor.vendor.destroy',
        'uses' => 'VendorController@destroy',
        'middleware' => 'can:vendor.vendors.destroy'
    ]);

    $router->get('vendors/export-csv', [
        'as' => 'admin.vendor.vendor.exportcsv',
        'uses' => 'VendorController@getExportVendors',
        'middleware' => 'can:vendor.vendors.getExportVendors'
    ]);

    $router->bind('location', function ($id) {
        return app('Modules\Vendor\Repositories\VendorLocationRepository')->find($id);
    });
    $router->get('locations/{vendor}', [
        'as' => 'admin.vendor.location.index',
        'uses' => 'LocationController@index',
        'middleware' => 'can:vendor.locations.index'
    ]);
    $router->get('locations/create/{vendor}', [
        'as' => 'admin.vendor.location.create',
        'uses' => 'LocationController@create',
        'middleware' => 'can:vendor.locations.create'
    ]);
    $router->post('locations/{vendor}', [
        'as' => 'admin.vendor.location.store',
        'uses' => 'LocationController@store',
        'middleware' => 'can:vendor.locations.store'
    ]);
    $router->get('locations/{location}/edit/{vendor}', [
        'as' => 'admin.vendor.location.edit',
        'uses' => 'LocationController@edit',
        'middleware' => 'can:vendor.locations.edit'
    ]);
    $router->put('locations/{location}/{vendor}', [
        'as' => 'admin.vendor.location.update',
        'uses' => 'LocationController@update',
        'middleware' => 'can:vendor.locations.update'
    ]);
    $router->delete('locations/{location}/{vendor}', [
        'as' => 'admin.vendor.location.destroy',
        'uses' => 'LocationController@destroy',
        'middleware' => 'can:vendor.vendors.destroy'
    ]);

    $router->bind('vendorcategory', function ($id) {
        return app('Modules\Vendor\Repositories\VendorCategoryRepository')->find($id);
    });
    $router->get('categoryrequest', [
        'as' => 'admin.vendor.categoryrequest.index',
        'uses' => 'CategoryRequestController@index',
        'middleware' => 'can:vendor.categoryrequest.index'
    ]);
    $router->get('categoryrequest/{vendorcategory}/approve', [
        'as' => 'admin.vendor.categoryrequest.approve',
        'uses' => 'CategoryRequestController@approve',
        'middleware' => 'can:vendor.categoryrequest.index'
    ]);
    $router->get('categoryrequest/{vendorcategory}/reject', [
        'as' => 'admin.vendor.categoryrequest.reject',
        'uses' => 'CategoryRequestController@reject',
        'middleware' => 'can:vendor.categoryrequest.index'
    ]);
    $router->delete('categoryrequest/{vendorcategory}', [
        'as' => 'admin.vendor.categoryrequest.destroy',
        'uses' => 'CategoryRequestController@destroy',
        'middleware' => 'can:vendor.categoryrequest.destroy'
    ]);
    // $router->get('vendors/create', [
    //     'as' => 'admin.vendor.vendor.create',
    //     'uses' => 'VendorController@create',
    //     'middleware' => 'can:vendor.vendors.create'
    // ]);
    // $router->post('vendors', [
    //     'as' => 'admin.vendor.vendor.store',
    //     'uses' => 'VendorController@store',
    //     'middleware' => 'can:vendor.vendors.store'
    // ]);
    // $router->get('vendors/{vendor}/edit', [
    //     'as' => 'admin.vendor.vendor.edit',
    //     'uses' => 'VendorController@edit',
    //     'middleware' => 'can:vendor.vendors.edit'
    // ]);
    // $router->put('vendors/{vendor}', [
    //     'as' => 'admin.vendor.vendor.update',
    //     'uses' => 'VendorController@update',
    //     'middleware' => 'can:vendor.vendors.update'
    // ]);
    // $router->delete('vendors/{vendor}', [
    //     'as' => 'admin.vendor.vendor.destroy',
    //     'uses' => 'VendorController@destroy',
    //     'middleware' => 'can:vendor.vendors.destroy'
    // ]);
// append

});
