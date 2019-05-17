<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\DataVerify\UpdateCourseRunDataVerify;
use Illuminate\Support\Collection;

class UploadUpdateCourseRunTest extends UploadCourseRunTest
{
    protected $row = [];
    protected $dataVerifyClass = UpdateCourseRunDataVerify::class;
    protected $course;

    public function test_course_code() {
        $this->assertTrue(true);
    }
    // public function test_no_of_trainees() {
    //     $this->assertTrue(true);
    // }

    // public function test_no_of_absentees() {

    //     $faker = \Faker\Factory::create();
    //     $this->setColumn('no_of_absentees');

    //     # When  is blank
    //     $this->createNewData()->makeBlank()->checkColumn(true);

    //     # When Contain Real Text 
    //     $this->createNewData()->makeSomeChar(20)->checkColumn(false);

    //     # When Contain DIGITS
    //     $this->createNewData()->makeDigit(10, 100)->checkColumn(true);

    //     # When Contain number greater than 4294967295
    //     $this->createNewData()->makeDigit(4294967296, 5294967295)->checkColumn(false);

    //     # When special chars
    //     $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

    //     # When valid data
    //     $this->createNewData()->checkColumn(true);
    // }

    public function test_course_run_id() {

        $faker = \Faker\Factory::create();
        $this->setColumn('course_run_id');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When Contain Real Text 
        $this->createNewData()->makeSomeChar(20)->checkColumn(false);

        # When special chars
        $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

        # When valid data
        $this->createNewData()->checkColumn(true);
    }

    // public function test_no_of_attendees() {

    //     $faker = \Faker\Factory::create();
    //     $this->setColumn('no_of_attendees');

    //     # When  is blank
    //     $this->createNewData()->makeBlank()->checkColumn(true);

    //     # When Contain Real Text 
    //     $this->createNewData()->makeSomeChar(20)->checkColumn(false);

    //     # When Contain DIGITS
    //     $this->createNewData()->makeDigit(10, 100)->checkColumn(true);

    //     # When Contain number greater than 4294967295
    //     $this->createNewData()->makeDigit(4294967296, 5294967295)->checkColumn(false);

    //     # When special chars
    //     $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

    //     # When valid data
    //     $this->createNewData()->checkColumn(true);
    // }

    protected function ins() {
        
        return  new $this->dataVerifyClass($this->row, [ $this->row['course_run_id'] =>[['current_status'=>'Confirmed']] ]);
    }

    /**
     * Create the fake data of Course Run
     */
    protected function getCourseRunData() {
        
        $faker = \Faker\Factory::create();
        //print_r(collect(factory(App\Models\Course::class)->raw())->course_code); exit;
        $this->course = $course = factory(App\Models\Course::class)->make();
        $max_date = \Carbon\Carbon::now()->addYears(1);
        
        $start_date = $faker->date('Y-m-d', $max_date);
        $end_date = \Carbon\Carbon::parse($start_date)->addDays($faker->numberBetween(0, 30))->format('Y-m-d');
        
        $test_start_date = \Carbon\Carbon::parse($end_date)->addDays($faker->numberBetween(0, 6))->format('Y-m-d');
        $test_end_date = \Carbon\Carbon::parse($test_start_date)->addDays($faker->numberBetween(0, 3))->format('Y-m-d');

        $no_of_trainees = $faker->numberBetween(2, 30);
        $no_of_absentees = $faker->numberBetween(0, $no_of_trainees);
        $no_of_attendees = $faker->numberBetween(0, ($no_of_trainees-$no_of_trainees));

        return [
            'course_run_id'=> $faker->numberBetween(2, 30),
            'course_start_date'=> $start_date,
            'course_end_date' => $end_date,
            'assessment_start_date' => $test_start_date,
            'assessment_end_date'  => $test_end_date,
            'remarks' => $faker->realText(255),
            'deconflict' => $faker->randomElement(['Yes','No','yes','no','YES','NO']),
            //'no_of_absentees' => $no_of_absentees,
            //'no_of_attendees' => $no_of_attendees
            'no_of_trainees' => $no_of_trainees,
        ];
    }
}