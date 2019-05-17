<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;
use App\Lib\PUBAuth;

class UserTest extends ListTestCase
{
    use DatabaseTransactions;

    //
    public function test_profile_data_and_logout() {

        $this->json('GET','me', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $this->json('GET','logout', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res = $this->json('GET','logout', [], [
            'HTTP_Authorization' => 'Bearer dejhfkjegegj' .$this->getToken(true)
        ]);
        
        $this->assertResponseStatus(500);
    }

    /**
     * Checking the login functionality.
     * @group testLogin
     * @return void
     */
    function test_login_request()
    {
        $auth = new PUBAuth();

        $check_mode = $auth->isDevelopmentMode();
        
        if($check_mode){
            $users = $auth->whoCanLoginInDevMode();
        }

        $response = $this->json('POST', 'login-action', ['username' => $users[0],'password' => "12345678", 'device_id' => "1234567876543"]);

        $response->seeJson([
                        'status' => true,
                     ]);
    }

    /**
     * Checking the login functionality with wrong credentials.
     * @group testInvalidLogin
     * @return void
     */
    function test_invalid_login_request()
    {
        
        $auth = new PUBAuth();

        $check_mode = $auth->isDevelopmentMode();
        
        if($check_mode){
            $users = $auth->whoCanLoginInDevMode();
        }

        $response = $this->json('POST', 'login-action', ['username' => "6789",'password' => "145678", 'device_id' => "1234567876543"]);
        $response->seeJson(['status' => false]);

        $response = $this->json('POST', 'login-action', ['username' => "",'password' => "", 'device_id' => "1234567876543"]);
        $response->seeJson(['status' => false]);



        $response = $this->json('POST', 'login-action', ['username' => "TESTHH998888s",'password' => "123122", 'device_id' => "1234567876543"]);
        $response->seeJson(['status' => false]);

        $user = User::where('personnel_number', '99998')->update(['role_id' => null]);

        $response = $this->json('POST', 'login-action', ['username' => "99998",'password' => "123122", 'device_id' => "1234567876543"]);
        $response->seeJson(['status' => false]);
    }
}