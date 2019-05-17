<?php

namespace DownloadSample;
use App\Models\Course;
use App\Models\CourseRun;
use App\User;
use Excel;
use App\Lib\DataVerify\FilesHeaderVerify;

class PlacementSample {

    private $courseRunId = [];
    private $personnelNum = [];

    public function __construct() {

        $this->courseRunId = CourseRun::select('id')->inRandomOrder()->limit(100 )->get()->pluck('id')->toArray();
        $this->personnelNum = User::select('personnel_number')->inRandomOrder()->limit(100)->get()->pluck('personnel_number')->toArray();
        
    }

    public function download() {
        
        $fileHeaders = new FilesHeaderVerify([]);
        $header =  array_values($fileHeaders->placementCell);

        $data = []; 
        $data[] = $header;

        foreach ($this->courseRunId  as $course_run_id) {
            
            foreach($this->personnelNum as $pn) {
                $data[] = [$course_run_id, $pn];
            }
        }

        Excel::create('Placement', function($excel) use ($data) {            

            $excel->sheet('Placement data', function($sheet) use ($data) {
                
                $sheet->fromArray($data, null, 'A1', true, false);
            });

        })->download('xlsx');
    }
}