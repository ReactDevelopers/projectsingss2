<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\DataVerify\SummaryDataVerify;
use Illuminate\Support\Collection;

class UploadCourseSummaryTest extends ColumnTestCase
{
    protected $row = [];
    protected $dataVerifyClass = SummaryDataVerify::class;
    protected $course;

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

    /**
     * Verify all column value.
     */
    public function test_all_column() {

        $this->getToken();
        $this->actingAs($this->authUser);
        $faker = \Faker\Factory::create();

        $columns = ['overall','trainers_delivery','content_relevance','site_visits','facilities','admin','response_rate'];

        foreach($columns as $col) {

            $this->setColumn($col);

            # When  blank
            $this->createNewData()->makeBlank()->checkColumn(true);

            # When Contain Real Text 
            $this->createNewData()->makeSomeChar(20)->checkColumn(false);

            # When Contain float
            $this->createNewData()->setValue($faker->randomFloat(2, 0, 90))->checkColumn(true);

            # When Contain number greater than 100
            $this->createNewData()->setValue($faker->randomFloat(2, 101, 1000))->checkColumn(false);

            # When special chars
            $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

            # When valid data
            $this->createNewData()->checkColumn(true);
        }
    }
    /**
     * Create new Data
     */
    protected function createNewData() {

        $this->getToken();
        $this->actingAs($this->authUser);
        $this->row = $this->getCourseSummaryData();        
        return $this;
    }
    protected function ins() {
        
        return  new $this->dataVerifyClass($this->row, [ $this->row['course_run_id'] =>[['current_status'=>'Confirmed']] ]);
    }

    /**
     * Create the fake data of Course Run
     */
    protected function getCourseSummaryData() {
        
        $faker = \Faker\Factory::create();        

        return [
            'course_run_id'=> $faker->numberBetween(2, 30),
            'overall' => $faker->randomFloat(2, 0, 90),
            'trainers_delivery' => $faker->randomFloat(2, 0, 90),
            'content_relevance' => $faker->randomFloat(2, 0, 90),
            'site_visits' => $faker->randomFloat(2, 0, 90),
            'facilities' => $faker->randomFloat(2, 0, 90),
            'admin' => $faker->randomFloat(2, 0, 90),
            'response_rate' => $faker->randomFloat(2, 0, 90),
        ];
    }
}