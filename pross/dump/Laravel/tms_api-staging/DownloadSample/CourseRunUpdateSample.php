<?php

namespace DownloadSample;
use App\Models\Course;
use App\Models\CourseRun;
use Excel;
use App\Lib\DataVerify\FilesHeaderVerify;

class CourseRunUpdateSample {

    private $courseRunId = [];

    public function __construct() {

        $this->courseRunId = CourseRun::select('id')->limit(11000)->get()->pluck('id')->toArray();
        
    }

    public function download() {
        
        $fileHeaders = new FilesHeaderVerify([]);
        $header =  array_values($fileHeaders->updateCourseRunCell);

        $data = []; 
        $data[] = $header;

        foreach ($this->courseRunId  as $course_run_id) {
            
            $data[] = array_merge([$course_run_id], $this->fakeData());
        }

        Excel::create('Course-run-update', function($excel) use ($data) {            

            $excel->sheet('Course Run Update data', function($sheet) use ($data) {
                $sheet->setColumnFormat([
                    'B:E' => 'dd/mm/yyyy'
                ]);
                $sheet->fromArray($data, null, 'A1', true, false);
            });

        })->download('xlsx');
    }

    public function fakeData() {

        $faker = \Faker\Factory::create();
        
        $course_run = factory(CourseRun::class)->raw();

        //dd($course_run);

        return [

            \PHPExcel_Shared_Date::PHPToExcel(strtotime($course_run['start_date'])),
            \PHPExcel_Shared_Date::PHPToExcel(strtotime($course_run['end_date'])),
            \PHPExcel_Shared_Date::PHPToExcel(strtotime($course_run['assessment_start_date'])),
            \PHPExcel_Shared_Date::PHPToExcel(strtotime($course_run['assessment_end_date'])),
            $course_run['no_of_attendees'],
            //$course_run['no_of_absentees'],
            $course_run['should_check_deconflict'],
            $course_run['remarks']         
        ];
    }
}