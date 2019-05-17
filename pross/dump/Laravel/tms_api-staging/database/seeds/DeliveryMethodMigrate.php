<?php

use Illuminate\Database\Seeder;

class DeliveryMethodMigrate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::statement("UPDAte courses as c 
			LEFT JOIN delivery_methods as dm ON dm.name = delivery_method
			set c.delivery_method_id = IF(dm.id is null, (SELECT id from delivery_methods where name ='Others'), dm.id)");

        DB::statement("update courses as c 
		set delivery_method_id = (SELECT id from delivery_methods where name ='Team Based Learning')
		where delivery_method = 'TBL'");
		
    }
}
