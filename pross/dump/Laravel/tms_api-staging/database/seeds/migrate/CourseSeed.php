<?php

use Illuminate\Database\Seeder;

class CourseSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = \DB::connection('mysql_old')->table('course')->select([
        	'Category as category',
            'id as id',
        	'TargetGroup as target_group',
        	'Title as title',
    		\DB::raw('IF(DeletedStatus =0,null, CURRENT_DATE() ) as deleted_at'),
    		'AddedDate as created_at',
    		'LastUpdatedDate as updated_at',
    		'SpecialRequirement as special_requirement',
    		'PreRequisite as pre_requisite',
    		\DB::raw(" ROUND ((Days + Hours/8),2) as duration_in_days")
    	])->get();

    	$data = json_decode($data->toJson(),true);
    	\App\Models\Course::insert($data);

    }
}
