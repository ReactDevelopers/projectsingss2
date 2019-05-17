<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use App\Models\ProgrammeType;
use App\Models\Department;
use App\Models\AssessmentType;
use App\Models\TrainingLocation;

class InsertNewdataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        ProgrammeType::batchInsertIgnore([
            ['prog_type_code'=> 'ACT', 'prog_type_name'=>'ACT','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'CON', 'prog_type_name'=>'CON','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'CSV', 'prog_type_name'=>'CSV','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'CYS', 'prog_type_name'=>'CYS','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'DGL', 'prog_type_name'=>'DGL','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'ENF', 'prog_type_name'=>'ENF','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'EXD', 'prog_type_name'=>'EXD','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'TEC', 'prog_type_name'=>'TEC','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'FNP', 'prog_type_name'=>'FNP','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'HRM', 'prog_type_name'=>'HRM','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'IAU', 'prog_type_name'=>'IAU','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'INT', 'prog_type_name'=>'INT','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'LDS', 'prog_type_name'=>'LDS','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'LST', 'prog_type_name'=>'LST','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'ONB', 'prog_type_name'=>'ONB','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'OEX', 'prog_type_name'=>'OEX','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'SAF', 'prog_type_name'=>'SAF','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'SOF', 'prog_type_name'=>'SOF','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'OJT', 'prog_type_name'=>'OJT','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'WSQ', 'prog_type_name'=>'WSQ','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['prog_type_code'=> 'OTH', 'prog_type_name'=>'OTH','created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')]
        ]);

        Department::batchInsertIgnore([
            ['dept_code' => 'L1', 'dept_name' => 'L1', 'is_user_dept'=> 'No'],
            ['dept_code' => 'L2', 'dept_name' => 'L2', 'is_user_dept'=> 'No'],
            ['dept_code' => 'L3', 'dept_name' => 'L3', 'is_user_dept'=> 'No'],
            ['dept_code' => 'L4', 'dept_name' => 'L4', 'is_user_dept'=> 'No'],
            ['dept_code' => 'L5', 'dept_name' => 'L5', 'is_user_dept'=> 'No'],
            ['dept_code' => 'L1, L2', 'dept_name' => 'L1, L2', 'is_user_dept'=> 'No'],
            ['dept_code' => 'L1, L2, L3', 'dept_name' => 'L1, L2, L3', 'is_user_dept'=> 'No'],
            ['dept_code' => 'L1, L2, L3, L4', 'dept_name' => 'L1, L2, L3, L4', 'is_user_dept'=> 'No'],
            ['dept_code' => 'All Levels ', 'dept_name' => 'All Levels ', 'is_user_dept'=> 'No'],
            ['dept_code' => 'Foundation ', 'dept_name' => 'Foundation ', 'is_user_dept'=> 'No'],
            ['dept_code' => 'Intermediate ', 'dept_name' => 'Intermediate ', 'is_user_dept'=> 'No'],
            ['dept_code' => 'Advanced ', 'dept_name' => 'Advanced ', 'is_user_dept'=> 'No'],
            ['dept_code' => 'Expert', 'dept_name' => 'Expert', 'is_user_dept'=> 'No'],
            ['dept_code' => 'Leadership', 'dept_name' => 'Leadership', 'is_user_dept'=> 'No']
        ]);

        AssessmentType::batchInsertIgnore([
            ['assessment_type_name'=> 'Case Study', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Interview', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'MCQ', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'MCQ & Case Study', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'MCQ & Practical', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Oral', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Practical', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Project', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Report/Paper', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Written ', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Written - Open Book', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Written & Case Study', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Written & Interview', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'Written & Practical', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['assessment_type_name'=> 'None', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
        ]);
        
        TrainingLocation::insert(['location' => 'Local& Overseas']);

        Artisan::call('cache:clear');
        $this->command->info('New data has been inserted.');
        
    }
}
