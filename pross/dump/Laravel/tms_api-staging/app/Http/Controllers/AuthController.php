<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lib\PUBAuth;

class AuthController extends Controller
{
	

    protected $auth;

    public function __construct(){

       $this->auth = new PUBAuth('users');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function loginAction(Request $request)
    {
        $validate = $this->validateLogin($request);       

        if($validate->fails()){

            $this->errors = $validate->errors();
            $this->status = false;
            $this->message = trans('message.login_failed');
            return $this->response(200);
        }

        if ($this->attemptLogin($request)) {

            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    
    protected function validateLogin(Request $request)
    {
        $validate = $this->getValidationFactory()->make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        return $validate;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->auth->check($request->username, $request->password);
    }
    
   
    /**
     * Send the response after authentication successful.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
       
        $this->status = true;
        $this->message = $this->auth->message;
        $token = $this->auth->user->createToken('TMS')->accessToken;
        \App\User::where('id',$this->auth->user->id)->update([

            'num_success_login'=> \DB::raw('num_success_login+1'),
            'last_success_login_attempt' => date('Y-m-d H:i:s')
        ]);
        
        $this->data = $this->auth->user->append('is_supervisor');
        $this->data->token = $token;
        return $this->response();
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $this->errors = [['username'=> trans('auth.failed')]];
        $this->status = false;
        $this->message = $this->auth->message;
        return $this->response(200);
    }       
}