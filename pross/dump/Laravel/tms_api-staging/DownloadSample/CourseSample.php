<?php

namespace DownloadSample;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\TrainingLocation;
use App\Models\Department;
use App\Models\AssessmentType;
use Excel;
use App\Lib\DataVerify\FilesHeaderVerify;

class CourseSample {

    private $category = [];
    private $type = [];
    private $location = [];
    private $provider = [];
    private $department = [];
    private $assessment_type = [];

    public function __construct() {

        $this->category = ProgrammeCategory::get(['prog_category_name'])->pluck('prog_category_name')->toArray();
        $this->type = ProgrammeType::get(['prog_type_name'])->pluck('prog_type_name')->toArray();
        $this->location = TrainingLocation::get(['location'])->pluck('location')->toArray();
        $this->department = Department::get(['dept_name'])->pluck('dept_name')->toArray();
        $this->assessment_type = AssessmentType::get(['assessment_type_name'])->pluck('assessment_type_name')->toArray();
    }

    public function download() {
        
        $fileHeaders = new FilesHeaderVerify([]);
        $header =  array_values($fileHeaders->courseCell);

        $data = []; 
        $data[] = $header;
        $i = 0 ;

        while ($i <= 11000) {
            
            $data[] = $this->fakeData();
            $i++;
        }

        Excel::create('Course', function($excel) use ($data) {

            $excel->sheet('Course data', function($sheet) use ($data) {

                $sheet->fromArray($data, null, 'A1', false, false);

            });

        })->download('xlsx');
    }

    public function fakeData() {

        $faker = \Faker\Factory::create();
        $grantsubsidy_yn = $faker->randomElement(['Yes','No']);
        $if_yes_provide_value = 0;
        $cost_per_pax = $faker->randomFloat(2, 100, 999999);

        if($grantsubsidy_yn === 'Yes') {
            
            $min = $cost_per_pax - 50;
            $max = $cost_per_pax;
            $if_yes_provide_value = $faker->randomFloat(2, $min, $max);
        }

        return [
            $faker->unique()->regexify('[A-Z]{4}[0-9]{2,8}'),
            $faker->text(255),
            $faker->numberBetween(2,30),
            $faker->randomElement($this->category),
            $faker->randomElement($this->type),
            $faker->randomElement($this->department),
            $faker->randomElement($this->assessment_type),
            $faker->randomElement(['Yes','Yes by law','No']),
            $faker->text(50),
            $faker->randomElement($this->location),
            $faker->text(35),
            $cost_per_pax,
            $grantsubsidy_yn,
            $if_yes_provide_value,
            $faker->text(255)
        ];
    }
}