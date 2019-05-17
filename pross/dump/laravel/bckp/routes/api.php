<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('projects', 'ProjectController@store');
Route::post('projects', 'LoginController@validate_login');
Route::post('register', 'ProjectController@register');
Route::post('upload', 'ProjectController@upload');
Route::post('weather', 'ProjectController@weather');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
