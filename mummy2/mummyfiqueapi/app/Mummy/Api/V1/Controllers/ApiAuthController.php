<?php

namespace App\Mummy\Api\V1\Controllers;

use App\Mummy\Api\V1\Requests\AuthClientRequest;
use App\Mummy\Api\V1\Requests\RefreshTokenRequest;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiAuthController extends ApiController {

    /**
     * @SWG\Post(
     *   path="/v1/auth/client",
     *   description="<ul>
     *      <li>username : string (required)</li>
     *      <li>password : string (required)</li>
     *      <li>grant_type : enum (required)</li>
     *      <li>client_id : integer (required) </li>
     *      <li>client_secret : string (required)</li></ul>",
     *   summary="Authenticate Client to get Access Token",
     *   operationId="api.auth.client",
     *   produces={"application/json"},
     *   tags={"Auth"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/AuthClient")
     *   ),
     *   @SWG\Response(response=101, description="Wrong email or password"),
     *   @SWG\Response(response=102, description="You need to confirm your account"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     */

    protected function client(AuthClientRequest $request) {
        $credentials = $request->only('username', 'password');
//        $credentials = $this->credentials($request);

        $data = $request->all();

        $user = User::where('email', $credentials['username'])->first();

        $request->request->add([
            'grant_type'    => $data['grant_type'],
            'client_id'     => $data['client_id'],
            'client_secret' => $data['client_secret'],
            'username'      => $credentials['username'],
            'password'      => $credentials['password'],
            'scope'         => null,
        ]);

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );
        $response   = Route::dispatch($proxy);
        $data       = json_decode($response->getContent());

        if(isset($data->error)){
            $result = [
                'error' => [
                    'message' => $data->message,
                    'code' => $response->getStatusCode(),
                    'type' => $data->error
                ]
            ];
        }
        else{
            $result = [
                'data'=> [
                    'access_token' => $data->access_token,
                    'expires_in' => $data->expires_in,
                    'refresh_token' => $data->refresh_token,
                ]
            ];
        }

        return response()->json($result, 200);

//        return Route::dispatch($proxy);
    }

    /**
     * @SWG\Post(
     *   path="/v1/auth/refresh-token",
     *   description="<ul>
     *      <li>grant_type : enum (required)</li>
     *      <li>refresh_token : string (required)</li>
     *      <li>client_id : integer (required) </li>
     *      <li>client_secret : string (required)</li></ul>",
     *   summary="Refresh Token",
     *   operationId="api.auth.refreshToken",
     *   produces={"application/json"},
     *   tags={"Auth"},
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Target customer.",
     *     required=true,
     *    @SWG\Schema(ref="#/definitions/AuthRefreshToken")
     *   ),
     *   @SWG\Response(response=101, description="Wrong email or password"),
     *   @SWG\Response(response=102, description="You need to confirm your account"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     */
    protected function refreshToken(RefreshTokenRequest $request)
    {
        $request->request->add([
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
        ]);

        $proxy = Request::create(
            '/oauth/token',
            'POST'
        );
        $response   = Route::dispatch($proxy);
        $data       = json_decode($response->getContent());

        if(isset($data->error)){
            $result = [
                'error' => [
                    'message' => $data->message,
                    'code' => $response->getStatusCode(),
                    'type' => $data->error
                ]
            ];
        }
        else{
            $result = [
                'data'=> [
                    'access_token' => $data->access_token,
                    'expires_in' => $data->expires_in,
                    'refresh_token' => $data->refresh_token,
                ]
            ];
        }

        return response()->json($result, 200);

//        return Route::dispatch($proxy);
    }
}