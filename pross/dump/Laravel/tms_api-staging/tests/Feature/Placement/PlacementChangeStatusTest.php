<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\CourseRun;
use App\Models\Course;
use App\Models\Placement;
use App\User;
use Illuminate\Http\UploadedFile;

class PlacementChangeStatusTest extends TestCase
{
    use DatabaseTransactions;
    private $courseRun1;
    private $courseRun2;
    private $courseRun3;
    private $courseRun4;

    private $placement1;
    private $placement2;
    private $placement3;
    private $placement4;

    public function test_change_status_when_conflict() {
        
        $this->insertSomeCourseRunAndPlacement();

        $course = factory(Course::class)->create();

        # Test Case 1
        $this->verifyTestCases($course, '2017-12-01', '2018-02-06', '2018-02-06','2018-02-06');
        
        #Test Case 2
        $this->verifyTestCases($course, '2018-02-03', '2018-02-03', '2018-02-03','2018-02-03');

        #Test Case 3
        $this->verifyTestCases($course, '2017-12-08', '2018-01-01', '2018-01-03','2018-01-03');

        #Test Case 4
        $this->verifyTestCases($course, '2017-12-08', '2018-02-03', '2018-02-03','2018-02-03');

        #Test Case 5
        $this->verifyTestCases($course, '2018-01-16', '2018-02-18', '2018-02-18','2018-02-18');

        #Test Case 6
        $this->verifyTestCases($course, '2018-02-14', '2018-02-18', '2018-02-18','2018-02-18');

        #Test Case 7
        $this->verifyTestCases($course, '2018-03-24', '2018-03-25', '2018-03-26','2018-03-27');

        #Test Case 8
        $this->verifyTestCases($course, '2018-04-25', '2018-04-29', '2018-05-08','2018-05-08');

        #Test Case 9
        $this->verifyTestCases($course, '2017-12-01', '2018-02-06', '2017-12-01','2018-02-18');

        #Test Case 10
        $this->verifyTestCases($course, '2017-12-02', '2018-02-07', '2018-03-24','2018-03-25');

        #Test Case 11
        $this->verifyTestCases($course, '2017-12-03', '2018-02-08', '2018-04-25','2018-04-29');

        #Test Case 12
        $this->verifyTestCases($course, '2017-12-04', '2018-02-09', '2018-02-14','2018-02-18');

        #Test Case 13
        $this->verifyTestCases($course, '2017-12-01', '2018-02-06', '2017-12-01','2018-02-06');

        #Test Case 14
        $this->verifyTestCases($course, '2017-12-02', '2018-02-07', '2018-02-03','2018-02-03');

        #Test Case 15
        $this->verifyTestCases($course, '2017-12-03', '2018-02-08', '2017-12-08','2018-01-01');

        #Test Case 16
        $this->verifyTestCases($course, '2017-12-04', '2018-02-09', '2017-12-08','2018-02-03');

        #Test Case 17
        $this->verifyTestCases($course, '2017-12-01', '2018-05-29', '2018-05-30','2018-05-31');

    }

    /**
     * Test Conflict
     * @group tes321
     */
    public function test_conflict() {
        
        $this->insertSomeCourseRunAndPlacement();
        $course = factory(Course::class)->create();

        # Test Case 1
        $this->verifyTestCasesForConflict($course, '2017-12-01', '2018-02-06', '2018-02-06','2018-02-06',[
            $this->placement1->id,
            $this->placement2->id
        ]);

        #Test Case 2
        $this->verifyTestCasesForConflict($course, '2018-02-03', '2018-02-03', '2018-02-03','2018-02-03',[
            $this->placement2->id
        ]);

        // #Test Case 3
        $this->verifyTestCasesForConflict($course, '2017-12-08', '2018-01-01', '2018-01-03','2018-01-03',[
            $this->placement1->id
        ]);

        #Test Case 4
        $this->verifyTestCasesForConflict($course, '2017-12-08', '2018-02-03', '2018-02-03','2018-02-03',[
            $this->placement1->id,
            $this->placement2->id,
        ]);


        #Test Case 5
        $this->verifyTestCasesForConflict($course, '2018-01-16', '2018-02-18', '2018-02-18','2018-02-18',[
            $this->placement1->id,
            $this->placement2->id,
        ]);


        #Test Case 6
        $this->verifyTestCasesForConflict($course, '2018-02-14', '2018-02-18', '2018-02-18','2018-02-18',[
            $this->placement2->id,
        ]);

        #Test Case 7
        $this->verifyTestCasesForConflict($course, '2018-03-24', '2018-03-25', '2018-03-26','2018-03-27', [
            $this->placement3->id
        ]);

        // #Test Case 8
        $this->verifyTestCasesForConflict($course, '2018-04-25', '2018-04-29', '2018-05-08','2018-05-08',[
            $this->placement4->id
        ]);

        // #Test Case 9
        $this->verifyTestCasesForConflict($course, '2017-12-01', '2018-02-06', '2017-12-01','2018-02-18',[
            $this->placement1->id,
            $this->placement2->id
        ]);

        #Test Case 10
        $this->verifyTestCasesForConflict($course, '2017-12-02', '2018-02-07', '2018-03-24','2018-03-25',[
            $this->placement1->id,
            $this->placement2->id,
            $this->placement3->id
        ]);

        #Test Case 11
        $this->verifyTestCasesForConflict($course, '2017-12-03', '2018-02-08', '2018-04-25','2018-04-29',[
            $this->placement1->id,
            $this->placement2->id,
            $this->placement4->id
        ]);

        #Test Case 12
        $this->verifyTestCasesForConflict($course, '2017-12-04', '2018-02-09', '2018-02-14','2018-02-18',[
            $this->placement1->id,
            $this->placement2->id
        ]);

        #Test Case 13
        $this->verifyTestCasesForConflict($course, '2017-12-01', '2018-02-06', '2017-12-01','2018-02-06',[
            $this->placement1->id,
            $this->placement2->id
        ]);

        #Test Case 14
        $this->verifyTestCasesForConflict($course, '2017-12-02', '2018-02-07', '2018-02-03','2018-02-03',[
            $this->placement1->id,
            $this->placement2->id,
        ]);

        #Test Case 15
        $this->verifyTestCasesForConflict($course, '2017-12-03', '2018-02-08', '2017-12-08','2018-01-01', [
            $this->placement1->id,
            $this->placement2->id
        ]);

        #Test Case 16
        $this->verifyTestCasesForConflict($course, '2017-12-04', '2018-02-09', '2017-12-08','2018-02-03', [
            $this->placement1->id,
            $this->placement2->id
        ]);

         #Test Case 17
         $this->verifyTestCasesForConflict($course, '2017-12-01', '2018-05-29', '2018-05-30','2018-05-31',[
            $this->placement1->id,
            $this->placement2->id,
            $this->placement3->id,
            $this->placement4->id
         ]);
    }
    /**
     * @group test_dca
     */
    public function test_when_deconflict_allow() {

        $course = factory(Course::class)->create();
        $user = factory(User::class)->create();

        $courseRun = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> '2017-12-04',
            'end_date'=> '2018-02-09',
            'assessment_start_date' => '2018-02-10',
            'assessment_end_date' => '2018-02-10',
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'No'
        ]);

        Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        $courseRun1 = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> '2018-02-07',
            'end_date'=> '2018-02-09',
            'assessment_start_date' => '2018-02-10',
            'assessment_end_date' => '2018-02-10',
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'Yes'
        ]);

        $placement = Placement::create(['course_run_id' => $courseRun1->id,'personnel_number'=>  $user->personnel_number,'current_status'=>'Draft']);

        $res = $this->json('POST','placement/check-conflict', ['placement_id'=>[$placement->id] ], $this->getAuthHeader());
        
        $response = json_decode($res->response->getContent(), true);

        $data = $t = collect(isset($response['errors'][$placement->id]) ? $response['errors'][$placement->id] : []);
        $data = $data->pluck('conflict_in_placement_id')->toArray();
        $this->assertTrue( (count($data) ==1) );
    }

    private function verifyTestCases($course, $start_date, $end_date, $assessment_start_date, $assessment_end_date ){

        $courseRun = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> $start_date,
            'end_date'=> $end_date,
            'assessment_start_date' => $assessment_start_date,
            'assessment_end_date' => $assessment_end_date,
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'Yes'
        ]);

        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> 99999,'current_status'=>'Draft']);
        $this->json('PUT','placement/make-status-confirmed', ['placement_id'=>[$placement->id] ], $this->getAuthHeader());
        
        $placement = Placement::where('id', $placement->id)->first();
        $this->assertTrue(($placement->current_status == 'Draft'));
        $this->assertResponseStatus(200);
    }

    private function verifyTestCasesForConflict($course, $start_date, $end_date, $assessment_start_date, $assessment_end_date ,$accepted_conflict_in= []){

        $courseRun = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> $start_date,
            'end_date'=> $end_date,
            'assessment_start_date' => $assessment_start_date,
            'assessment_end_date' => $assessment_end_date,
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'Yes'
        ]);


        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> 99999,'current_status'=>'Draft']);
        
        $res = $this->json('POST','placement/check-conflict', ['placement_id'=>[$placement->id] ], $this->getAuthHeader());
        
        $response = json_decode($res->response->getContent(), true);
        $data = $t = collect(isset($response['errors'][$placement->id]) ? $response['errors'][$placement->id] : []);
        $data = $data->pluck('conflict_in_placement_id')->toArray();
        
        foreach($data as $error_in_course_run_id) {
            // if(!in_array($error_in_course_run_id, $accepted_conflict_in) ) {

            //     print_r($error_in_course_run_id);
            //     print_r($accepted_conflict_in); exit;
            // }
            $this->assertTrue( in_array($error_in_course_run_id, $accepted_conflict_in) );
        }
    }

    private function insertSomeCourseRunAndPlacement() {

        $course = factory(Course::class)->create();
        $this->courseRun1 = $courseRun1 = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> '2018-01-01',
            'end_date'=>'2018-01-01',
            'assessment_start_date' => '2018-01-17',
            'assessment_end_date' => '2018-01-17',
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'Yes'
        ]);

        //print_r($courseRun1->toArray());

        $this->courseRun2 = $courseRun2 = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> '2018-02-02',
            'end_date'=>'2018-02-05',
            'assessment_start_date' => '2018-02-13',
            'assessment_end_date' => '2018-02-17',
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'Yes'
        ]);

        $this->courseRun3 = $courseRun3 = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> '2018-03-07',
            'end_date'=>'2018-03-10',
            'assessment_start_date' => '2018-03-23',
            'assessment_end_date' => '2018-03-27',
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'Yes'
        ]);

        $this->courseRun4 = $courseRun4 = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> '2018-04-15',
            'end_date'=>'2018-04-24',
            'assessment_start_date' => '2018-04-28',
            'assessment_end_date' => '2018-04-30',
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'Yes'
        ]);

        (new Placement)->forceDelete();
        
       $this->placement1 = Placement::create(['course_run_id' => $courseRun1->id,'personnel_number'=> 99999,'current_status'=>'Confirmed']);
       $this->placement2= Placement::create(['course_run_id' => $courseRun2->id,'personnel_number'=> 99999,'current_status'=>'Confirmed']);
       $this->placement3 = Placement::create(['course_run_id' => $courseRun3->id,'personnel_number'=> 99999,'current_status'=>'Confirmed']);
       $this->placement4 = Placement::create(['course_run_id' => $courseRun4->id,'personnel_number'=> 99999,'current_status'=>'Confirmed']);
    }

    /**
     * Test When Get conflict in  subordinate placement
     * @group conflict_in_subordinate_placement
     **/
    public function test_conflict_in_subordinate_placement() {

        $course = factory(Course::class)->create();
        
        $cr = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> '2018-04-15',
            'end_date'=>'2018-04-24',
            'assessment_start_date' => '2018-04-28',
            'assessment_end_date' => '2018-04-30',
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'Yes'
        ]);

        $cr2 = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'start_date'=> '2018-04-15',
            'end_date'=>'2018-04-24',
            'assessment_start_date' => '2018-04-28',
            'assessment_end_date' => '2018-04-30',
            'current_status'=>'Confirmed',
            'should_check_deconflict'=>'Yes'
        ]);

        $u1 = factory(User::class)->create();
        $u2 = factory(User::class)->create(['supervisor_personnel_number' => $u1->personnel_number]);
        
        Placement::create(['course_run_id' => $cr2->id,'personnel_number'=> $u2->personnel_number,'current_status'=>'Confirmed']);


        $placement = Placement::create(['course_run_id' => $cr->id,'personnel_number'=> $u1->personnel_number,'current_status'=>'Draft']);
        
        $res = $this->json('POST','placement/check-conflict', ['placement_id'=>[$placement->id] ], $this->getAuthHeader());
        
        $response = json_decode($res->response->getContent(), true);

        //print_r($response); exit;
        $data = $t = collect(isset($response['errors'][$placement->id]) ? $response['errors'][$placement->id] : []);
        $data = $data->pluck('type')->toArray();
        $this->assertTrue($data[0] === 'subordinate');
    }

    /**
     * Test to change the status as cancel or draft
     **/
    public function test_placement_change_status_cancel_or_draft() {

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id ]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        # Blank request
        $res = $this->json('POST','placement/update-status/'.$placement->id, [], $this->getAuthHeader());
        $this->assertResponseStatus(422);

        # valid Request        
        $res = $this->json('POST','placement/update-status/'.$placement->id, ['status'=>'Cancelled'], $this->getAuthHeader());
        $this->assertResponseStatus(200);        
    }   

     /**
     * Test to change the status as confirm when Class size is over.
     * @group classSizeOver
     **/
    public function test_placement_change_status_confirmed_when_class_size_over() {

        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id ,'no_of_trainees' => 1]);

        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Draft']); 

        $this->json('PUT','placement/make-status-confirmed', ['placement_id'=>[$placement->id] ], $this->getAuthHeader());

        $placement = Placement::where('id', $placement->id)->first();
        $this->assertTrue(($placement->current_status == 'Confirmed'));
        $this->assertResponseStatus(200);

        $placement2 = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user2->personnel_number,'current_status'=>'Draft']); 
        

        $res = $this->json('POST','placement/check-conflict', ['placement_id'=>[$placement2->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);  
        $res->seeJson(['error_code'=> 'CSO']);
    }

}
