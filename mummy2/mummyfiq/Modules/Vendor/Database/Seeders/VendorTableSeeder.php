<?php namespace Modules\Vendor\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\VendorProfile;
use Modules\Vendor\Entities\VendorLoation;
use Modules\Vendor\Entities\VendorCategory;

class VendorTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
		
		// $this->call("OthersTableSeeder");
		// 
		// 
		
		$vendors = Vendo::join('role_users', 'role_users.user_id', '=', 'users.id')
							->where('role_users', '=', Config('constant.user_role.vendor'))
							->get();

		if(count($vendors)){
			foreach ($vendors as $key => $item) {
				// vendor_profile 
				$this->addVendorProfile($item);

				// vendor_location 
				$this->addVendorLocation($item);

				// vendor_category
				$this->addVendorCategory($item);
			}
		}
	}


	public function addVendorProfile(Vendor $vendor){
		$profile =  VendorProfile::where('user_id', $vendor->id)->first();
		if(!$profile){
			$item = new VendorProfile;
			$item->name 			= $faker->jobTitle;
			$item->description 		= $faker->text;
			$item->sorts 			= $faker->randomDigit;
			$item->country_id		= NULL;
			$item->status			= 1;
			// Config('constant.status.' . $item->status) 			= 1;
			$item->save();
		}
	}

	public function addVendorLocation(Vendor $vendor){

	}

	public function addVendorCategory(Vendor $vendor){

	}

}