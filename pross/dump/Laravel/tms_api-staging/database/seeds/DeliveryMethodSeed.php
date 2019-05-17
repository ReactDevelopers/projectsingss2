<?php

use Illuminate\Database\Seeder;
use App\Models\DeliveryMethod;

class DeliveryMethodSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

    	DeliveryMethod::insert([
    		['name' => 'AOP', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'AR, VR, MR', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Blended Learning', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Classroom', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Classroom & Practical', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Classroom & eLearning', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'eLearning', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Mobile Learning ', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Practical', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Project', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Seminar', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Simulator', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Study Trip', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Team Based Learning', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    		['name' => 'Others', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
    	]);
    }
}