<?php

namespace DownloadSample;
use App\Models\Course;
use App\Models\CourseRun;
use App\User;
use Excel;
use App\Lib\DataVerify\FilesHeaderVerify;

class SupervisorSample {

    private $personnelNum = [];

    public function __construct() {

        $this->personnelNum = User::select('personnel_number')->inRandomOrder()->limit(10000)->get()->pluck('personnel_number')->toArray();      
    }

    public function download() {
        
        $fileHeaders = new FilesHeaderVerify([]);
        $header =  array_values($fileHeaders->supervisorCell);
        $faker = \Faker\Factory::create();

        $users = array_chunk($this->personnelNum, count($this->personnelNum)/2 );

        $subordinates = $users[0];
        $supervisor = $users[1];

        $data = []; 
        $data[] = $header;
            
        foreach($subordinates as $u) {
            
            $data[] = [$u,  $faker->randomElement($supervisor) ];
        }

        Excel::create('Supervisor', function($excel) use ($data) {            

            $excel->sheet('Supervisor data', function($sheet) use ($data) {
                
                $sheet->fromArray($data, null, 'A1', true, false);
            });

        })->download('xlsx');
    }
}