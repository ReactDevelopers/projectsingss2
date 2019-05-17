<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\CourseRun;
use App\Models\Course;
use App\User;

class  ListTestCase extends TestCase
{
    protected $data  = null;

    protected function setData($res, $debug = false) {
        
        $response = json_decode($res->response->getContent(), true);
        $this->data = collect($response['data']['data']);
        if($debug){
            print_r($this->data); exit;
        }

        return $this;
    }

    protected function assertDataExact($key, $value_should_be) {

        if(($this->data)) foreach($this->data as $data) {

            $this->assertTrue( ($data[$key] ==  $value_should_be) );
        }
        return $this;
    }

    protected function assertDataMatchStart($key, $value_should_be) {

        if(($this->data)) foreach($this->data as $data) {
            
            $this->assertTrue( (strpos((string)$data[$key], (string)$value_should_be) !== false) );
        }
        
        return $this;
    }
    protected function assertDataNumeric($key, $val, $operator) {

        if(($this->data)) foreach($this->data as $data) {
             
            if($operator == '=') {

                $this->assertTrue( ($data[$key]) == ($val) );

            } else if($operator == '>=' ) {

                $this->assertTrue( ($data[$key]) >= ($val) );
                
            } else if($operator == '<=' ) {
                
                $this->assertTrue( ($data[$key]) <= ($val) );

            } else if($operator == '<' ) {
                
                $this->assertTrue( ($data[$key]) < ($val) );

            } else if($operator == '>' ) {

                $this->assertTrue( ($data[$key]) > ($val) );                
            }
        }
        //exit;
        return $this;
    }

    protected function assertDataDate($key, $val, $operator) {

        if(($this->data)) foreach($this->data as $data) {
             
            if($operator == '=') {

                $this->assertTrue( strtotime($data[$key]) == strtotime($val) );

            } else if($operator == '>=' ) {

                $this->assertTrue( strtotime($data[$key]) >= strtotime($val) );
                
            } else if($operator == '<=' ) {
                
                $this->assertTrue( strtotime($data[$key]) <= strtotime($val) );
            } else if($operator == '<' ) {
                
                $this->assertTrue( strtotime($data[$key]) < strtotime($val) );

            } else if($operator == '>' ) {

                $this->assertTrue( strtotime($data[$key]) > strtotime($val) );                
            }
        }
        return $this;
    }

    protected function assertDataCount($count) {

        if(($this->data)) {
            $this->assertTrue(( $this->data->count() == $count ));
        }
    }

    protected function assertDataLength($length, $operator) {

        if(($this->data)) {
            
            $real_length = $this->data->count();

            if($operator == '=') {

                $this->assertTrue( ($real_length == $length) );

            } else if($operator == '>=' ) {

                $this->assertTrue( ($real_length >= $length) );
                
            } else if($operator == '<=' ) {
                
                $this->assertTrue( ($real_length <= $length));

            } else if($operator == '<' ) {
                
                $this->assertTrue( ($real_length < $length) );

            } else if($operator == '>' ) {

                $this->assertTrue( ($real_length > $length) );                
            }
        }
    }

    protected function filterParamValue() {
        
        $faker = \Faker\Factory::create();
        $max_date = \Carbon\Carbon::now()->addYears(1);        
        $start_date = $faker->date('Y-m-d', $max_date);
        $end_date = \Carbon\Carbon::parse($start_date)->addDays($faker->numberBetween(0, 30))->format('Y-m-d');

        $customFilterData = [

            'prog_category_name' => [
                'value' => (ProgrammeCategory::inRandomOrder()->first())->id,
                'comparator' => '='
            ],
            'prog_type_name' => [
                'value' => (ProgrammeType::inRandomOrder()->first())->id,
                'comparator' => '='
            ],
            'id' => [
                'value' => (CourseRun::inRandomOrder()->first())->id,
                'comparator' => '='
            ],
            'course_code' => [
                'value' => (Course::inRandomOrder()->first())->course_code,
                'comparator' => 'LIKE'
            ],
            'course_title' => [
                'value' => (Course::inRandomOrder()->first())->title,
                'comparator' => 'LIKE'
            ],
            'title' => [
                'value' => (Course::inRandomOrder()->first())->title,
                'comparator' => 'LIKE'
            ],
            'duration_in_days' => [
                'value' => $faker->numberBetween(0, 30),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'duration_in_days' => [
                'value' => $faker->numberBetween(0, 30),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'created_by' => [
                'value' => (User::inRandomOrder()->first())->id,
                'comparator' => '='
            ],
            'no_of_trainee' => [
                'value' => $faker->numberBetween(0, 30),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'summary_uploaded' => [
                'value' => 'Yes',
                'comparator' => '='
            ],
            'date_range' => [
                'value' => ['start_date'=> $start_date, 'end_date' => $end_date ],
                'comparator' => 'date-range'
            ],
            'test_date_range' => [
                'value' => ['start_date'=> $start_date, 'end_date' => $end_date ],
                'comparator' => 'date-range'
            ],
            'enrolled' => [
                'value' => $faker->numberBetween(0, 5),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'current_status' => [
                'value' => $faker->randomElement(['Closed','Completed','Confirmed','Draft']),
                'comparator' => '='
            ],
            'admin' => [
                'value' => $faker->numberBetween(0, 100),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'trainer_delivery' => [
                'value' => $faker->numberBetween(0, 100),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'content_relevance' => [
                'value' => $faker->numberBetween(0, 100),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'site_visits' => [
                'value' => $faker->numberBetween(0, 100),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'no_of_failure' => [
                'value' => $faker->numberBetween(0, 100),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'facilities' => [
                'value' => $faker->numberBetween(0, 100),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ],
            'no_of_absantee' => [
                'value' => $faker->numberBetween(0, 100),
                'comparator' => $faker->randomElement(['>','>=','=','<','<='])
            ]

        ];

        return $customFilterData;
    }

    
}