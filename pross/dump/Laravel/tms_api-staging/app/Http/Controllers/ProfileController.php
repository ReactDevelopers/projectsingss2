<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;
use Lcobucci\JWT\Parser as JwtParser;
use Laravel\Passport\TokenRepository;

class ProfileController extends Controller
{
	
	/**
	 * Getting the Login user data
	 * @param  Request $request 
	 * @return Json           
	 */
	public function myProfile(Request $request){

		$this->data = app('auth')->user()->append('is_supervisor');
		return $this->response();
	}

	/**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request, JwtParser $jwt, TokenRepository $tokens)
    {
    	try{
	    	
	    	$token = $request->header('Authorization');
	    	$token = str_replace('Bearer ', '', $token);
	    	$token_id = $jwt->parse($token)->getClaim('jti');
	    	$tokens->find($token_id)->delete();

	    }
	    catch(\RuntimeException $e) {

	    	//return $this->response(500);
	    	throw new FlattenException('Invalid Token');	    	
	    }
	    
	    $this->message = trans('message.logout_success');
	    return $this->response();
    }
}