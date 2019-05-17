<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Lib\DataVerify\PlacementResultDataVerify;
use Illuminate\Support\Collection;
use App\Models\AbsentReason;
use App\Models\FailureReason;

class UploadPlacementResultTest extends ColumnTestCase
{
    use DatabaseTransactions;

    protected $row = [];
    protected $dataVerifyClass = PlacementResultDataVerify::class;
    protected $placementStatus = 'Confirmed';
    protected $courseRunId = null;
    protected $course_run_id_per_id = null;
    protected $perId = null;
    protected $result = null;
    protected $attendance = null;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_when_empty_data()
    {
        $this->getToken();
        $this->actingAs($this->authUser);

        $placement_data_ins = new PlacementResultDataVerify([],[]);
        $result = $placement_data_ins->run();
        $this->assertTrue(!$result['status']);
    }

    /**
     * When the Course run status is not confirmed the and try to upload the placement data init
     */
    public function test_when_course_run_status_in_not_confirmed() {

        $this->placementStatus = 'Draft';

        $this->getToken();
        $this->actingAs($this->authUser);
        $this->setColumn('course_run_id');        
        $this->createNewData()->checkColumn(false);
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
        $course_run_id = $this->row['course_run_id']+1;
        $this->course_run_id_per_id = 'ytwuetu-3276387';
        $this->setValue($course_run_id)->checkColumn(false,'course_run_id');
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
        $this->course_run_id_per_id = '8787779799';
        $this->checkColumn(false,'course_run_id');
     }

     public function test_assessment_results() {

        $this->getToken();
        $this->actingAs($this->authUser);

        $this->setColumn('assessment_results');

        
        # When  blank
        $this->result = 'Pass';
        $this->createNewData()->makeBlank()->checkColumn(true);

        # When  random text
        $this->result = 'Pass';
        $this->createNewData()->makeSomeChar(200)->checkColumn(false);

        # for Pass Value
        $this->result = 'Pass';
        $this->createNewData()->setValue('pass')
            ->checkColumn(true)
            ->checkTraslateValue('Pass');

        $this->result = 'Pass';
        $this->createNewData()->setValue('PASS')
            ->checkColumn(true)
            ->checkTraslateValue('Pass');
        
        $this->result = 'Pass';
        $this->createNewData()->setValue('Pass')
            ->checkColumn(true)
            ->checkTraslateValue('Pass');
        
        $this->result = 'Pass';
        $this->createNewData()->setValue('pAss')
            ->checkColumn(true)
            ->checkTraslateValue('Pass');

        # For Fail value
        $this->result = 'Fail';
        $this->createNewData()->setValue('fail')
            ->checkColumn(true)
            ->checkTraslateValue('Fail');

        $this->result = 'Fail';
        $this->createNewData()->setValue('FAIL')
            ->checkColumn(true)
            ->checkTraslateValue('Fail');
        
        $this->result = 'Fail';
        $this->createNewData()->setValue('Fail')
            ->checkColumn(true)
            ->checkTraslateValue('Fail');
        
        $this->result = 'Fail';
        $this->createNewData()->setValue('fAil')
            ->checkColumn(true)
            ->checkTraslateValue('Fail');
        
        # when user pass and user's attendance is Absent
        $this->result = 'Pass';
        $this->createNewData()->setValue('Absent','attendance')
            ->checkColumn(false);
    }

    public function test_attendance() {

        $this->getToken();
        $this->actingAs($this->authUser);

        $this->setColumn('attendance');

        # When  blank
        $this->createNewData()->makeBlank()->checkColumn(false);

        # When  random text
        $this->createNewData()->makeSomeChar(200)->checkColumn(false);

        # for Pass Value
        $this->attendance ='Present';
        $this->createNewData()->setValue('present')
            ->checkColumn(true)
            ->checkTraslateValue('Present');

        $this->attendance ='Present';
        $this->createNewData()->setValue('PRESENT')
            ->checkColumn(true)
            ->checkTraslateValue('Present');
        
        $this->attendance ='Present';
        $this->createNewData()->setValue('Present')
            ->checkColumn(true)
            ->checkTraslateValue('Present');
        
        $this->attendance ='Present';
        $this->createNewData()->setValue('prEsent')
            ->checkColumn(true)
            ->checkTraslateValue('Present');

        # For Fail value
        $this->attendance ='Absent';
        $this->result = 'Fail';        
        $this->createNewData()->setValue('absent')
            ->checkColumn(true)
            ->checkTraslateValue('Absent');
        
        $this->attendance ='Absent';
        $this->result = 'Fail';
        $this->createNewData()->setValue('Absent')
            ->checkColumn(true)
            ->checkTraslateValue('Absent');
        
        $this->attendance ='Absent';
        $this->result = 'Fail';
        $this->createNewData()->setValue('ABSENT')
            ->checkColumn(true)
            ->checkTraslateValue('Absent');
        
        $this->attendance ='Absent';
        $this->result = 'Fail';
        $this->createNewData()->setValue('ABSeNT')
            ->checkColumn(true)
            ->checkTraslateValue('Absent');
    }

    public function test_failure_reason() {
        
        $this->getToken();
        $this->actingAs($this->authUser);

        $this->setColumn('failure_reason');

        # When  blank
        $this->createNewData()->makeBlank()->checkColumn(($this->row['assessment_results'] == 'Pass'));

        # when Pass and enter the failer reason
        $this->result = 'Pass';
        $this->createNewData()->setValue('Missed assessment')->checkColumn(false);
    }

    public function test_absent_reason() {
        
        $this->getToken();
        $this->actingAs($this->authUser);

        $this->setColumn('absent_reason');

        # When  blank
        $this->createNewData()->makeBlank()->checkColumn(($this->row['attendance'] == 'Present'));

        # when Present and enter the attendance
        $this->result = 'Pass';
        $this->attendance ='Present';
        $this->createNewData()->setValue('Compassionate Leave')->checkColumn(false);
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

        $placemnet_data = [];
        
        $course_run_id_per_id = $this->course_run_id_per_id ? $this->course_run_id_per_id : $this->row['course_run_id'].'-'.$this->row['per_id'];
        
        $placemnet_data[$course_run_id_per_id][0] = $this->row;
        $placemnet_data[$course_run_id_per_id][0]['course_run_id_per_id'] = $course_run_id_per_id;
        $placemnet_data[$course_run_id_per_id][0]['current_status'] = $this->placementStatus;
        
        $this->courseRunId  = null;
        $this->perId = null;

        return  new $this->dataVerifyClass($this->row, $placemnet_data);
    }

    /**
     * Create the fake data of Course Run
     */
    protected function getCourseSummaryData() {
        
        $faker = \Faker\Factory::create();        
        $course_run_id = $faker->numberBetween(2, 30);
        $per_id = $faker->numberBetween(6754, 10000);

        $result = $this->result  ? $this->result : $faker->randomElement(['Pass','Fail']);
        $attendance = $this->attendance ? $this->attendance : $faker->randomElement(['Present','Absent']);

        if($result == 'Pass') {
            $attendance = 'Present';
        }
        $absent_reason = $attendance == 'Absent' ? AbsentReason::inRandomOrder()->first()->absent_reason: '';
        $failure_reason = $result == 'Fail' ? FailureReason::inRandomOrder()->first()->failure_reason: '';
        $this->result = null;
        $this->attendance = null;

        return [
            'course_run_id'=> $course_run_id,
            'per_id'=> $per_id,            
            'assessment_results'=> $result,
            'attendance' => $attendance,
            'absent_reason' => $absent_reason,
            'failure_reason' => $failure_reason
        ];
    }

}