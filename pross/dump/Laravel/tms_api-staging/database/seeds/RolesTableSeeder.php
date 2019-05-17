<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            [
                'name' => 'admin',
                'title' =>'Super Admin', 
                'description' => 'Super Admin' 
            ],
            [
                'name' => 'viewer',
                'title' =>'Viewer', 
                'description' => 'Viewer' 
            ],
            [
                'name' => 'subadmin',
                'title' =>'Admin', 
                'description' => 'Admin' 
            ]
        ]);
    }
}
