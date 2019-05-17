<?php

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::insert([
            ['dept_code' => 'CWL2' ,'dept_name' => 'CWL2','is_user_dept'=> 'No'],
            ['dept_code' => 'CWL4' ,'dept_name' => 'CWL4','is_user_dept'=> 'No'],
            ['dept_code' => 'WRNL2' ,'dept_name' => 'WRNL2','is_user_dept'=> 'No'],
            ['dept_code' => 'WRNL4' ,'dept_name' => 'WRNL4','is_user_dept'=> 'No'],
            ['dept_code' => 'WRPL2' ,'dept_name' => 'WRPL2','is_user_dept'=> 'No'],
            ['dept_code' => 'WRPL4' ,'dept_name' => 'WRPL4','is_user_dept'=> 'No'],
            ['dept_code' => 'WSNL2' ,'dept_name' => 'WSNL2','is_user_dept'=> 'No'],
            ['dept_code' => 'WSNL4' ,'dept_name' => 'WSNL4','is_user_dept'=> 'No'],
            ['dept_code' => 'WSPL2' ,'dept_name' => 'WSPL2','is_user_dept'=> 'No'],
            ['dept_code' => 'WSPL4' ,'dept_name' => 'WSPL4','is_user_dept'=> 'No'],
            ['dept_code' => 'CW' ,'dept_name' => 'CW','is_user_dept'=> 'No'],
            ['dept_code' => 'WRN' ,'dept_name' => 'WRN','is_user_dept'=> 'No'],
            ['dept_code' => 'WRP' ,'dept_name' => 'WRP','is_user_dept'=> 'No'],
            ['dept_code' => 'WSN' ,'dept_name' => 'WSN','is_user_dept'=> 'No'],
            ['dept_code' => 'WSP' ,'dept_name' => 'WSP','is_user_dept'=> 'No'],
            ['dept_code' => 'Foundation' ,'dept_name' => 'Foundation','is_user_dept'=> 'No'],
            ['dept_code' => 'Intermediate' ,'dept_name' => 'Intermediate','is_user_dept'=> 'No'],
            ['dept_code' => 'Advanced' ,'dept_name' => 'Advanced','is_user_dept'=> 'No'],
            ['dept_code' => 'Leadership' ,'dept_name' => 'Leadership','is_user_dept'=> 'No'],
            ['dept_code' => 'All Level' ,'dept_name' => 'All Level','is_user_dept'=> 'No'],
            ['dept_code' => 'WSQ L2' ,'dept_name' => 'WSQ L2','is_user_dept'=> 'No'],
            ['dept_code' => 'WSQ L3' ,'dept_name' => 'WSQ L3','is_user_dept'=> 'No'],
            ['dept_code' => 'WSQ L4' ,'dept_name' => 'WSQ L4','is_user_dept'=> 'No'],
            ['dept_code' => 'WSQ L5' ,'dept_name' => 'WSQ L5','is_user_dept'=> 'No']
        ]);
    }
}
