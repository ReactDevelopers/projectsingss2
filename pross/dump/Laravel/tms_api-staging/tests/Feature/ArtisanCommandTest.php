<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;

use Laravel\Passport\Token;
use Lcobucci\JWT\Parser as JwtParser;
use Laravel\Passport\TokenRepository;
use Carbon\Carbon;

class  ArtisanCommandTest extends TestCase
{
	use DatabaseTransactions;

	public function test_user_cached_clear_command() {

		$new_user = factory(\App\User::class)->create();
		$users = \App\User::getCached();

		$isExist =  false;

		foreach ($users as $user) {
			
			if($user['id']  == $new_user->id) {

				$isExist = true;
			}
		}

		$this->assertTrue($isExist);

		# changing the user name 

		$name = 'Hitesh Kumar';
		$new_user->update(['name'=> $name]);

		# Excute the command to check the Clear user Cache

		Artisan::call('user:cached_clear');

		# Check, is name  updated ?

		$users = \App\User::getCached();

		$isExist =  false;

		foreach ($users as $user) {
			
			if($user['id']  == $new_user->id && $user['name'] = $name) {

				$isExist = true;
			}
		}
		$this->assertTrue($isExist);
	}

	public function test_delete_token_after_30_days() {

		$tokens  =  new TokenRepository();
		$jwt = new JwtParser();
		
		# Create a TOken
		$token = $this->getToken(true);

    	$token_id = $jwt->parse($token)->getClaim('jti');
    	$tokens->find($token_id)->update(['created_at'=> Carbon::now()->addDays(-31)->format('Y-m-d H:i:s')]);

    	# Execute the command to delete the token

    	Artisan::call('passport:delete_old_token');

    	$this->assertTrue(!$tokens->find($token_id));
	}

}