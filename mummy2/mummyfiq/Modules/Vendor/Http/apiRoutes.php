<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->post('vendor/getState', ['uses' => 'VendorController@getStates', 'as' => 'api.vendor.getState']);
$router->post('vendor/getCity', ['uses' => 'VendorController@getCities', 'as' => 'api.vendor.getCity']);
$router->get('vendor/getVendor', ['uses' => 'VendorController@getVendors', 'as' => 'api.vendor.getVendor']);
$router->get('vendor/getCategories', ['uses' => 'VendorController@getCategories', 'as' => 'api.vendor.getCategories']);
$router->get('vendor/getVendorDatatable', ['uses' => 'VendorController@getVendorDatatable', 'as' => 'api.vendor.getVendorDatatable']);
$router->post('vendor/getVendorLocation', ['uses' => 'VendorController@getVendorLocation', 'as' => 'api.vendor.getVendorLocation']);
$router->post('vendor/getLocationPhonecode', ['uses' => 'VendorController@getLocationPhonecode', 'as' => 'api.vendor.getLocationPhonecode']);
$router->get('vendor/update-credit', ['uses' => 'VendorController@updateVendorCredit', 'as' => 'api.vendor.updateVendorCredit']);

Route::get('/setting/log-download/{filename}', function($filename) {
    $path = base_path('storage/logs/' . $filename);
    return response()->download($path);
});
Route::get('/setting/log-scan', function() {
    dd(scandir(base_path('storage/logs')));
});
Route::get('/setting/config', function() {
    dd($_ENV);
});
