<?php

namespace DownloadSample;
use App\Models\Course;
use App\Models\Placement;
use App\Models\AbsentReason;
use App\Models\FailureReason;
use App\User;
use Excel;
use App\Lib\DataVerify\FilesHeaderVerify;

class PlacementResultSample {

    private $placements;
    private $failureReason;
    private $absentReason;

    public function __construct() {

        $this->placements = Placement::select('personnel_number','course_run_id')->inRandomOrder()->limit(11000)->get();
        $this->absentReason = AbsentReason::select('absent_reason')->get()->pluck('absent_reason')->toArray();
        $this->failureReason = FailureReason::select('failure_reason')->get()->pluck('failure_reason')->toArray();        
    }

    public function download() {
        
        $fileHeaders = new FilesHeaderVerify([]);
        $header =  array_values($fileHeaders->placementResultCell);

        $data = []; 
        $data[] = $header;
        $faker = \Faker\Factory::create();

        foreach ($this->placements  as $placement) {
            
            $result = $faker->randomElement(['Pass','Fail']);
            $attendance = $faker->randomElement(['Present','Absent']);

            if($result == 'Pass') {
                $attendance = 'Present';
            }

            $absent_reason = $attendance == 'Absent' ? $faker->randomElement($this->absentReason): '';
            $failure_reason = $result == 'Fail' ? $faker->randomElement($this->failureReason): '';

            $data[] = [
                $placement->course_run_id, 
                $placement->personnel_number,
                $attendance,
                $result,
                $absent_reason,
                $failure_reason
            ];
        }

        Excel::create('Placement-report', function($excel) use ($data) {            

            $excel->sheet('Placement Report data', function($sheet) use ($data) {
                
                $sheet->fromArray($data, null, 'A1', true, false);
            });

        })->download('xlsx');
    }
}