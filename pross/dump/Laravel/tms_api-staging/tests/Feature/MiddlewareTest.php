<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\CourseRun;
use App\Models\Course;
use App\User;

class  MiddlewareTest extends TestCase
{
	use DatabaseTransactions;
	
	/**
	 * Test to Send the request with options Method
	 */
	public function test_to_send_option_req() {

		$this->json('OPTIONS','/' ,[]);
		$this->assertResponseStatus(200);
	}

	/**
	 * Test, When get profile details and when access token is invalid
	 */
	public function test_get_profile_and_token_invalid() {
		
		$this->json('GET','me', [], ['HTTP_Authorization' => 'Bearer dejhfkjegegj' .$this->getToken(true) ]);
        $this->assertResponseStatus(401);
	}

	/**
	 * Test, When get profile details and access token is valid
	 */
	public function test_get_profile_and_token_valid() {
		
		$this->json('GET','me', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);
	}
}