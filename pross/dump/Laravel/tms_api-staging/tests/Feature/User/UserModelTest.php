<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\CourseRun;
use App\Models\Course;
use App\User;

class  UserModelTest extends TestCase
{	
	/**
	 * Test to get the users who supervised by the user.
	 */
	public function test_to_get_supervisor_of() {

		$u1 = factory(User::class)->create(['supervisor_personnel_number' => 99999]);
		//print_r($user)
		$user = User::where('personnel_number', 99999)->with('supervisorOf')->first();
		//$is_present = false;

		$subordinate_list = $user->supervisorOf->filter(function($v) use($u1) {
			return ($v->personnel_number == $v->personnel_number);
		})->first();

		$this->assertTrue($subordinate_list ?  true : false);
	}
}