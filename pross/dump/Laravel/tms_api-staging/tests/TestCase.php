<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    use DatabaseTransactions;
    
    protected $authUser =  null;
    protected $token = null;    
    
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {   
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Getting the Access token
     **/
    public function getToken($force= true) {
        static $token;
        //\Log::info($token);
    	if(!$token || $force == true) {
            
            $user = \App\User::where('personnel_number', 99999)
	    		->first();

            if(!$user){
                
                $user = factory(\App\User::class)->create(['personnel_number'=>99999, 'email'=> env('TESTER_EMAIL') ,'role_id'=>1]);
            }

            $this->authUser = $user;

            $token = $this->token = $user->createToken('TMS')->accessToken;
            
	    }
        $this->token  = $token;
        $this->authUser = \App\User::where('personnel_number', 99999)->first();
	    return $this->token;
    }

    /**
     * Prepare the headers Array Format, which is require to access the Auth's route.
     * @return Array
     */ 
    public function getAuthHeader($force=true) {

        return 
        [
            'HTTP_Authorization' => 'Bearer ' .$this->getToken($force)
        ];
    }

    
}
