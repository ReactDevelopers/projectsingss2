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


/*
* We only ever want to show the index page, except for api 
* requests which will be ignored by the web router
*/
Route::get('{slug}', function () {
    return view('index');
})->where('slug', '(?!api)([A-z\d-\/_.]+)?');