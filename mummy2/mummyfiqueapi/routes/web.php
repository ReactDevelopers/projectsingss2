<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::get('customer/activation/{token}', '\App\Mummy\Api\V1\Controllers\CustomersController@activateUser')->name('user.activate');
Route::get('reset-password/{id}/{token}', '\App\Mummy\Api\V1\Controllers\CustomersController@getResetPassword');
Route::post('/reset-password/post', ['as' => 'auth.reset.post', 'uses' => '\App\Mummy\Api\V1\Controllers\CustomersController@postResetPassword']);
