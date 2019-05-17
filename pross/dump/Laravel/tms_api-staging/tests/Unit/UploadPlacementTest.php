<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\DataVerify\PlacementDataVerify;
use Illuminate\Support\Collection;

class UploadPlacementTest extends ColumnTestCase
{
    use DatabaseTransactions;

    protected $row = [];
    protected $dataVerifyClass = PlacementDataVerify::class;
    protected $courseRunStatus = 'Confirmed';
    protected $courseRunId = null;
    protected $perId = null;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_when_empty_data()
    {
        $this->getToken();
        $this->actingAs($this->authUser);

        $placement_data_ins = new PlacementDataVerify([],[],[],[]);
        $result = $placement_data_ins->run();
        $this->assertTrue(!$result['status']);
    }

    /**
     * When the Course run status is not confirmed the and try to upload the placement data init
     */
    public function test_when_course_run_status_in_not_confirmed() {

        $this->courseRunStatus = 'Draft';

        $this->getToken();
        $this->actingAs($this->authUser);
        $this->setColumn('course_run_id');        
        $this->createNewData()->checkColumn(true);
    }

    /**
     * Verify the Course run id column value
     */

     public function test_course_run_id() {

        $this->setColumn('course_run_id');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When Contain Real Text 
        $this->createNewData()->makeSomeChar(20)->checkColumn(false);

        # When special chars
        $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

        
        # when course_run_id does not exist in database.        
        $this->createNewData();
        $this->courseRunId = $this->row['course_run_id']+1;
        $this->checkColumn(false);
     }

     /**
     * Verify the Per_id column value
     */

    public function test_per_id() {

        $this->setColumn('per_id');

        # When  is blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When Contain Real Text 
        $this->createNewData()->makeSomeChar(20)->checkColumn(false);

        # When special chars
        $this->createNewData()->makeSpecialChar(30)->checkColumn(false);

        
        # when per_id does not exist in database.        
        $this->createNewData();
        $this->perId = $this->row['per_id']+1;
        $this->checkColumn(false);
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

        $course_run_data = [];
        $course_run_id = $this->courseRunId ? $this->courseRunId : $this->row['course_run_id'];

        $course_run_data[$course_run_id][0] = $this->row;
        $course_run_data[$course_run_id][0]['id'] = $course_run_id;
        $course_run_data[$course_run_id][0]['current_status'] = $this->courseRunStatus;
        $this->courseRunId  = null;
        
        $per_id = $this->perId ? $this->perId : $this->row['per_id'];
        $this->perId = null;

        return  new $this->dataVerifyClass($this->row, $course_run_data, [$per_id],[]);
    }

    /**
     * Create the fake data of Course Run
     */
    protected function getCourseSummaryData() {
        
        $faker = \Faker\Factory::create();        
        
        return [
            'course_run_id'=> $faker->numberBetween(2, 30),
            'per_id'=> $faker->numberBetween(6754, 10000),
        ];
    }

}