<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class  CustomValidationTest extends TestCase
{
	/**
	 * To test the validation which verifies that field should only conatins the comma seperated emails
	 */
	public function test_emails_validation() {

		# when blank Request
		$v = \Validator::make(['f1' => null], ['f1'=> 'emails']);
		$this->assertTrue(!$v->fails());

		# when contain invalid email
		$v = \Validator::make(['f1' => 'hitesh@test.com,hitesh'], ['f1'=> 'emails']);
		$this->assertTrue($v->fails());

		# when contain valid email
		$faker = \Faker\Factory::create();
		$v = \Validator::make(['f1' => "{$faker->email},{$faker->email}"], ['f1'=> 'emails']);
		$this->assertTrue(!$v->fails());
	}

	/**
	 * To test the validation which verifies that the field should contains only the given words [Here, we are ignoring the case sensitive]
	 */
	public function test_enum_in_validation() {

		# when blank Request
		$v = \Validator::make(['f1' => null], ['f1'=> 'in_enum:Test']);
		$this->assertTrue(!$v->fails());

		# when valid Request
		$v = \Validator::make(['f1' => 'test'], ['f1'=> 'in_enum:Test']);
		$this->assertTrue(!$v->fails());
			
	}
	/**
	 * Test the validation , which uses to check the file Extension.
	 */
	public function test_file_extention_validation() {

		//UploadedFile::fake()->image('test2.pdf')

		# when blank Request
		$v = \Validator::make(['f1' => null], ['f1'=> 'ext_in:png']);
		$this->assertTrue(!$v->fails());

		# when invalid Request
		$v = \Validator::make(['f1' => 'sdkqwhdkjqwhdkj'], ['f1'=> 'ext_in:png']);
		$this->assertTrue(!$v->fails());

		# when valid Request
		$v = \Validator::make(['f1' => UploadedFile::fake()->image('test2.png')], ['f1'=> 'ext_in:png']);
		$this->assertTrue(!$v->fails());

		# when invalid Request
		$v = \Validator::make(['f1' => UploadedFile::fake()->image('test2.jpeg')], ['f1'=> 'ext_in:png']);
		$this->assertTrue($v->fails());
	}

	/**
	 * Test the validation, which verifies the date should be greater than from the another field date.
	 */
	public function test_date_after_or_equal_validation() {

		$v = \Validator::make(['f1' => null,'f2' => null], ['f2'=> 'in_arr_after_equal_date:f1']);
		$this->assertTrue(!$v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('Y-m-d'),'f2' =>Carbon::now()->addDays(-1)->format('Y-m-d')], ['f2'=> 'in_arr_after_equal_date:f1']);
		$this->assertTrue($v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('Y-m-d'),'f2' =>Carbon::now()->format('Y-m-d')], ['f2'=> 'in_arr_after_equal_date:f1']);
		$this->assertTrue(!$v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('Y-m-d'),'f2' =>Carbon::now()->addDays(2)->format('Y-m-d')], ['f2'=> 'in_arr_after_equal_date:f1']);
		$this->assertTrue(!$v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('d/m/Y'),'f2' =>Carbon::now()->addDays(2)->format('d/m/Y')], ['f2'=> 'in_arr_after_equal_date:f1']);
		$this->assertTrue($v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('d/m/Y'),'f2' =>Carbon::now()->addDays(2)->format('Y-m-d')], ['f2'=> 'in_arr_after_equal_date:f1']);

		$this->assertTrue(!$v->fails());
	}

	/**
	 * Test the validation, which verifies the date should be greater than from the another field date.
	 */
	public function test_date_after_validation() {

		$v = \Validator::make(['f1' => null,'f2' => null], ['f2'=> 'in_arr_after_date:f1']);
		$this->assertTrue(!$v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('Y-m-d'),'f2' =>Carbon::now()->addDays(-1)->format('Y-m-d')], ['f2'=> 'in_arr_after_date:f1']);
		$this->assertTrue($v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('Y-m-d'),'f2' =>Carbon::now()->format('Y-m-d')], ['f2'=> 'in_arr_after_date:f1']);
		$this->assertTrue($v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('Y-m-d'),'f2' =>Carbon::now()->addDays(2)->format('Y-m-d')], ['f2'=> 'in_arr_after_date:f1']);
		$this->assertTrue(!$v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('d/m/Y'),'f2' =>Carbon::now()->addDays(2)->format('d/m/Y')], ['f2'=> 'in_arr_after_date:f1']);
		$this->assertTrue($v->fails());

		$v = \Validator::make(['f1' => Carbon::now()->format('d/m/Y'),'f2' =>Carbon::now()->addDays(2)->format('Y-m-d')], ['f2'=> 'in_arr_after_date:f1']);

		$this->assertTrue(!$v->fails());
	}

}