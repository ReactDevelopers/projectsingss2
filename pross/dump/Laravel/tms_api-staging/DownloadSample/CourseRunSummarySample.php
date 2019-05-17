<?php

namespace DownloadSample;
use App\Models\Course;
use App\Models\CourseRun;
use Excel;
use App\Lib\DataVerify\FilesHeaderVerify;

class CourseRunSummarySample {

    private $courseRunId = [];

    public function __construct() {

        $this->courseRunId = CourseRun::select('id')->limit(11000)->get()->pluck('id')->toArray();
        
    }

    public function download() {
        
        $fileHeaders = new FilesHeaderVerify([]);
        $header =  array_values($fileHeaders->courseRunSummaryCell);

        $data = []; 
        $data[] = $header;

        foreach ($this->courseRunId  as $course_run_id) {
            
            $data[] = array_merge([$course_run_id], $this->fakeData());
        }

        Excel::create('Course-run-summary', function($excel) use ($data) {            

            $excel->sheet('Course Run Summary data', function($sheet) use ($data) {
                
                $sheet->fromArray($data, null, 'A1', true, false);
            });

        })->download('xlsx');
    }

    public function fakeData() {

        $faker = \Faker\Factory::create();       

        return [

            $faker->randomFloat(2, 0, 100),  
            $faker->randomFloat(2, 0, 100),  
            $faker->randomFloat(2, 0, 100),  
            $faker->randomFloat(2, 0, 100),  
            $faker->randomFloat(2, 0, 100),  
            $faker->randomFloat(2, 0, 100),  
            $faker->randomFloat(2, 0, 100),  
        ];
    }
}