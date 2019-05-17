<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\DataVerify\CreateCourseRunDataVerify;
use Illuminate\Support\Collection;

class UploadCourseRunTest extends ColumnTestCase
{
    protected $row = [];
    protected $dataVerifyClass = CreateCourseRunDataVerify::class;
    protected $course;

    /**
     * Passing the Blank data
     */
    public function test_when_empty_data(){
        $this->getToken();
        $this->actingAs($this->authUser);

        $course_data_ins = new CreateCourseRunDataVerify([],[]);
        $result = $course_data_ins->run();
        $this->assertTrue(!$result['status']);
    }

    /**
     * Verify the Course code 
     */
    public function test_course_code() {
        
        $faker = \Faker\Factory::create();

        $this->setColumn('course_code');
        # When Course Code is blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When course code does not exist in database
        $new_course_code = $faker->unique()->regexify('[A-Z]{4}[0-9]{2,8}');

        $this->createNewData()->setValue($new_course_code)->checkColumn(false);

    }
    /**
     * Verify the Course start date
     */

     public function test_course_start_date() {

        $faker = \Faker\Factory::create();
        $this->setColumn('course_start_date');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(false);
        
        # when contains the invalid date
        $this->createNewData()->setValue('12/12/2018')->checkColumn(false);

        # when contains special chars
        $this->createNewData()->makeSpecialChar(15)->checkColumn(false);

        # when contains multiple dates
        $this->createNewData()->setValue('12/12/2018,12/12/2018,12/12/2018')->checkColumn(false);

        # when date is valid but contain some more chars
        $this->createNewData()->setValue($this->row['course_start_date'].'dsjwhjw')->checkColumn(false);

        # when course start date is greater than course end Date
        $max_date = \Carbon\Carbon::now()->addYears(1);        
        $start_date = $faker->date('Y-m-d', $max_date);
        $end_date = \Carbon\Carbon::parse($start_date)->addDays(-2)->format('Y-m-d');
        $this->createNewData()->setValue($start_date)->setValue($end_date,'course_end_date')->checkColumn(false,'course_end_date');

        # When course start date and end date are same
        $this->createNewData()->setValue($this->row['course_end_date'])->checkColumn(true);

        # when Valid Data
        $this->createNewData()->checkColumn(true);

     }

     /**
      * Verify course end date 
      */
     public function test_course_end_date() {

        $faker = \Faker\Factory::create();
        $this->setColumn('course_end_date');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(false);
        
        # when contains the invalid date
        $this->createNewData()->setValue('12/12/2018')->checkColumn(false);

        # when contains special chars
        $this->createNewData()->makeSpecialChar(15)->checkColumn(false);

        # when contains multiple dates
        $this->createNewData()->setValue('12/12/2018,12/12/2018,12/12/2018')->checkColumn(false);

        # when date is valid but contain some more chars
        $this->createNewData()->setValue($this->row['course_end_date'].'dsjwhjw')->checkColumn(false);

        # when course start date is greater than course end Date
        $max_date = \Carbon\Carbon::now()->addYears(1);        
        $start_date = $faker->date('Y-m-d', $max_date);
        $end_date = \Carbon\Carbon::parse($start_date)->addDays(-2)->format('Y-m-d');
        $this->createNewData()->setValue($end_date)->setValue($start_date,'course_start_date')->checkColumn(false);

        # When course start date and end date are same
        $this->createNewData()->setValue($this->row['course_end_date'],'course_start_date')->checkColumn(true);

        # when Valid Data
        $this->createNewData()->checkColumn(true);

     }

     public function test_assessment_start_date() {

        $faker = \Faker\Factory::create();
        $this->setColumn('assessment_start_date');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(true);
        
        # when contains the invalid date
        $this->createNewData()->setValue('12/12/2018')->checkColumn(false);

        # when contains special chars
        $this->createNewData()->makeSpecialChar(15)->checkColumn(false);

        # when contains multiple dates
        $this->createNewData()->setValue('12/12/2018,12/12/2018,12/12/2018')->checkColumn(false);

        # when date is valid but contain some more chars
        $this->createNewData()->setValue($this->row['course_end_date'].'dsjwhjw')->checkColumn(false);

        # when course assessment_start_date is less than course end Date
        $this->createNewData()
            ->setValue(\Carbon\Carbon::parse($this->row['course_end_date'])->addDays(-1)->format('Y-m-d'))
            ->checkColumn(false);

        # When course assessment_start_date and course end date and assessment_end_date are same
        $this->createNewData()
        ->setValue($this->row['course_end_date'])
        ->setValue($this->row['course_end_date'],'assessment_end_date')
        ->checkColumn(true);

        # When course assessment_end_date is less than assessment_start_date
        $this->createNewData()
            ->setValue(\Carbon\Carbon::parse($this->row['assessment_start_date'])->addDays(-1)->format('Y-m-d'),'assessment_end_date')
            ->checkColumn(false, 'assessment_end_date');

        # when Valid Data
        $this->createNewData()->checkColumn(true);

     }

     public function test_no_of_trainees() {

        $faker = \Faker\Factory::create();
        $this->setColumn('no_of_trainees');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When Contain Real Text 
        $this->createNewData()->makeSomeChar(20)->checkColumn(false);

        # When Contain DIGITS
        $this->createNewData()->makeDigit(10, 100)->checkColumn(true);

        # When Contain number greater than 4294967295
        $this->createNewData()->makeDigit(4294967296, 5294967295)->checkColumn(false);

        # When special chars
        $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

        # When valid data
        $this->createNewData()->checkColumn(true);
     }

     public function test_remarks() {
        
        $faker = \Faker\Factory::create();
        $this->setColumn('remarks');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(true);
        # When  contains  more than 300 characters         
        $this->createNewData()->makeSomeChar(300)->checkColumn(false);
        # When  contains special chars
        $this->createNewData()->makeSpecialChar(20)->checkColumn(true);

        # When conatains valid data
        $this->createNewData()->makeSomeChar(255)->checkColumn(true);
     }

     public function test_deconflict() {
        
        $faker = \Faker\Factory::create();
        $this->setColumn('deconflict');

        # When  blank
        $this->createNewData()->makeBlank()->checkColumn(true)->checkTraslateValue('Yes','should_check_deconflict');

        # When  random text
        $this->createNewData()->makeSomeChar(200)->checkColumn(false);

        # When  value is yes but y is in small letter
        $this->createNewData()->setValue('yes')->checkColumn(true)->checkTraslateValue('Yes','should_check_deconflict');

        # When  value is no but n is in small letter
        $this->createNewData()->setValue('no')->checkColumn(true)->checkTraslateValue('No','should_check_deconflict');

        # When  value is caps
        $this->createNewData()->setValue('YES')->checkColumn(true)->checkTraslateValue('Yes','should_check_deconflict');

        # When  value is caps
        $this->createNewData()->setValue('NO')->checkColumn(true)->checkTraslateValue('No', 'should_check_deconflict');

        # When  valid data
        $this->createNewData()->setValue('Yes')->checkColumn(true);

        # When  valid data
        $this->createNewData()->setValue('No')->checkColumn(true);

     }


    /**
     * Create new Data
     */
    protected function createNewData() {

        $this->getToken();
        $this->actingAs($this->authUser);
        $this->row = $this->getCourseRunData();        
        return $this;
    }
    protected function ins() {
        
        return  new $this->dataVerifyClass($this->row, $this->course->pluck('course_code')->toArray());
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
            'course_code'=> $course->course_code,
            'course_start_date'=> $start_date,
            'course_end_date' => $end_date,
            'assessment_start_date' => $test_start_date,
            'assessment_end_date'  => $test_end_date,
            'no_of_trainees' => $no_of_trainees,
            'remarks' => $faker->realText(255),
            'deconflict' => $faker->randomElement(['Yes','No'])
        ];
    }
}