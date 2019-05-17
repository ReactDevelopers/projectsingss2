<?php

use Illuminate\Database\Seeder;
use App\Models\ProgrammeCategory;

class ProgrammeCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProgrammeCategory::insert([

            [
                'prog_category_code' =>'AD' ,
                'prog_category_name' => 'AD',
                'created_at' => date('Y-m-d H:i:s') , 
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_category_code' =>'ID' ,
                'prog_category_name' => 'ID',
                'created_at' => date('Y-m-d H:i:s') , 
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_category_code' =>'OR' ,
                'prog_category_name' => 'OR',
                'created_at' => date('Y-m-d H:i:s') , 
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'prog_category_code' =>'TD' ,
                'prog_category_name' => 'TD',
                'created_at' => date('Y-m-d H:i:s') , 
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
