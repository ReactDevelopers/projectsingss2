<?php

use Illuminate\Database\Seeder;
use App\Mummy\Api\V1\Entities\UserRole;
use App\Mummy\Api\V1\Entities\Customer;
use App\Mummy\Api\V1\Entities\Vendors\VendorProfile;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $limitUserDemo=100;

        for($i=50; $i<=$limitUserDemo; $i++ ){
            $user = new Customer();
            $user->id = $i;
            $user->first_name = 'Demo user'.$i;
            $user->email = 'user'.$i.'@example.com';
            $user->password = Hash::make(hash('sha1','123456'));
            $user->status = '1';
            $user->save();

            if($i > 75)
            {
            	$userRole = new UserRole();
	            $userRole->user_id = $user->id;
	            $userRole->role_id = 3;
	            $userRole->save();
                $vendorProfile = new VendorProfile();
                $vendorProfile->user_id = $user->id;
                $vendorProfile->photo = 'http://admin-mummy.acc-svrs.com/assets/media/no-image.png';
                $vendorProfile->dimension = '{"width": "1024","height": "1024"}';
                $vendorProfile->save();
            }
            else
            {
            	$userRole = new UserRole();
	            $userRole->user_id = $user->id;
	            $userRole->role_id = 2;
	            $userRole->save();

            }
        }
    }
}
