<?php namespace Modules\Advertisement\Database\Factories;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModelFactory{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
		
		// $this->call("OthersTableSeeder");
	}

}