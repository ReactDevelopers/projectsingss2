<?php

namespace DownloadSample;
use App\Models\Course;
use App\Models\CourseRun;
use Excel;
use App\Lib\DataVerify\FilesHeaderVerify;

class CourseRunSample {

    private $courseCode = [];

    public function __construct() {

        $this->courseCode = Course::select('course_code')->inRandomOrder()->limit(200)->get()->pluck('course_code')->toArray();
        
    }

    public function download() {
        
        $fileHeaders = new FilesHeaderVerify([]);
        $header =  array_values($fileHeaders->createCourseRunCell);

        $data = []; 
        $data[] = $header;
        $i = 0 ;

        while ($i <= 11001) {
            
            $data[] = $this->fakeData();
            $i++;
        }

        Excel::create('Course-run', function($excel) use ($data) {            

            $excel->sheet('Course Run data', function($sheet) use ($data) {
                $sheet->setColumnFormat([
                    'B:E' => 'dd/mm/yyyy'
                ]);
                $sheet->fromArray($data, null, 'A1', true, false);
            });

        })->download('xlsx');
    }

    public function fakeData() {

        $faker = \Faker\Factory::create();
        
        $course_run = factory(CourseRun::class)->make();

        return [

            $faker->randomElement($this->courseCode),            
            \PHPExcel_Shared_Date::PHPToExcel(strtotime($course_run->start_date)),
            \PHPExcel_Shared_Date::PHPToExcel(strtotime($course_run->end_date)),
            \PHPExcel_Shared_Date::PHPToExcel(strtotime($course_run->assessment_start_date)),
            \PHPExcel_Shared_Date::PHPToExcel(strtotime($course_run->assessment_end_date)),
            $course_run->no_of_trainees,
            $course_run->should_check_deconflict,
            $course_run->remarks         
        ];
    }
}