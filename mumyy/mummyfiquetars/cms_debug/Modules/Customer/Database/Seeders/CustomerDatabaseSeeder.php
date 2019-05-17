<?php namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Customer\Entities\Country;

class CustomerDatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		// $this->call("OthersTableSeeder");
		// $this->call("Modules\\Customer\\Database\\Seeders\\CountryTableSeeder");
		$this->call(CountryTableSeeder::class);
	}
}