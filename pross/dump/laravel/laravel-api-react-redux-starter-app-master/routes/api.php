<?php

use Illuminate\Http\Request;
use Tymon\JWTauth;
use Dingo\Api\Routing\Router;



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

$api = app('Dingo\Api\Routing\Router');


$api->version('v1', function (Router $api) {

    // unprotected routes
    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });

    $api->post('register', 'App\Http\Controllers\Auth\RegisterController@create');
    
    $api->post('authenticate', 'App\Http\Controllers\AuthenticateController@authenticate');
    $api->post('logout', 'App\Http\Controllers\AuthenticateController@logout');
    $api->get('token', 'App\Http\Controllers\AuthenticateController@getToken');


    //protected route group
    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        
        // get the authenticated user
        $api->get('authenticated_user', 'App\Http\Controllers\AuthenticateController@authenticatedUser');
        
        // protected resource
        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to protected resources granted! You are seeing this text as you provided the token correctly.'
            ]);
        });

        // refresh token
        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);
    });
});
