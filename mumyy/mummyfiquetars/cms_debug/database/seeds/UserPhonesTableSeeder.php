<?php

use Illuminate\Database\Seeder;
use Modules\Vendor\Entities\UserPhone;
use Modules\Vendor\Services\VendorService;

class UserPhonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $service = app(VendorService::class);

        $vendors = $service->all();
        $faker = \Faker\Factory::create();
        if(count($vendors)){
        	foreach ($vendors as $key => $item) {
	        	$user = new UserPhone;
	            $user->user_id = $item->id;
	            $user->phone_number = $faker->numberBetween(9000000, 9999999);
	            $user->country_code = '84';
	            $user->is_verifyed = 1;
	            $user->is_primary = 1;
	            $user->save();
	        }
        }
    }
}
