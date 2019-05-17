<?php

use Illuminate\Database\Seeder;
use App\Models\FailureReason;
use App\Models\AssessmentType as Model;

class AssessmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::insert([
            ['assessment_type_name' => 'Case Study', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Interview', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'MCQ', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'MCQ+Case Study', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'MCQ+Practical', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'None', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Oral', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Practical', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Project', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Report/Paper', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Written', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Written - Open Book', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Written+Case Study', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Written+Interview', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            ['assessment_type_name' => 'Written+Practical', 'created_at'=> date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')],
            
        ]);
    }
}
