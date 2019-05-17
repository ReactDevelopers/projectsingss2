<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;

class  BulkQueryTest extends TestCase
{
	use DatabaseTransactions;

	public function test_insert_multiple_row_and_ignore_duplicate() {

		$u =  new User();
		//factory(User)->raw();
		$res = $u->batchInsertIgnore([factory(User::class)->raw(),factory(User::class)->raw(),factory(User::class)->raw()]);

		//dd($res-total);
		$this->assertTrue($res['total'] == 3);

		# when pass Blank Array

		$res = $u->batchInsertIgnore([]);
		$this->assertTrue(!$res);

		$res = $u->batchInsertIgnore(['sdkjshdkjsd']);
	}

	public function test_insert_multiple_row_and_update_duplicate() {

		$u =  new User();
		//factory(User)->raw();
		$res = $u->batchInsertUpdate([factory(User::class)->raw(),factory(User::class)->raw(['email'=> null]),factory(User::class)->raw()],['name', 'email']);

		$res = $u->batchInsertUpdate([factory(User::class)->raw(),factory(User::class)->raw(),factory(User::class)->raw(['role_id' => 'select id from roles limit 1;'])],['name', 'email']);

		//dd($res-total);
		$this->assertTrue($res['total'] == 3);

		# when pass Blank Array

		$res = $u->batchInsertUpdate([],[]);
		$this->assertTrue(!$res);

		$res = $u->batchInsertUpdate(['sdkjshdkjsd'],[]);
	}
}