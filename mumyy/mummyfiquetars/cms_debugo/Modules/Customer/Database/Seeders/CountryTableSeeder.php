<?php namespace Modules\Customer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Customer\Entities\Country;

class CountryTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
    {
        Model::unguard();
        
        // \DB::table('mm__countries')->truncate();   
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \DB::table('mm__countries')->truncate();     

        $json = \File::get("public/misc/CountryCodes.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
          Country::create(array(
            'sortname' => $obj->code,
            'name' => $obj->name,
            'phonecode' => preg_replace("/[^A-Za-z0-9]/", "", $obj->dial_code),
          ));
        }
    }

}