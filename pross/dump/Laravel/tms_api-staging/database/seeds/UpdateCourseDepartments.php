<?php

use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\Artisan;

class UpdateCourseDepartments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Department::whereIn('dept_code', [
        	'CWL2',
        	'CWL4',
        	'WRNL2',
        	'WRNL4',
        	'WRPL2',
        	'WRPL4',
        	'WSNL2',
        	'WSNL4',
        	'WSPL2',
        	'WSPL4',
        	'CW',
        	'WRN',
        	'WRP',
        	'WSN',
        	'WSP',
        	'Foundation',
        	'Intermediate',
        	'Advanced',
        	'Leadership',
        	'All Level',
        	'WSQ L2',
        	'WSQ L3',
        	'WSQ L4',
        	'WSQ L5'
        ])->update(['is_user_dept' => 'No']);

        Artisan::call('cache:clear');
    }
}
