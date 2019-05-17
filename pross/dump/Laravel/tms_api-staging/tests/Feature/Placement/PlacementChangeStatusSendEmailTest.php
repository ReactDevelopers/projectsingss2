<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\CourseRun;
use App\Models\Course;
use App\Models\Placement;
use App\User;
use Illuminate\Http\UploadedFile;

class PlacementChangeStatusSendEmailTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test to change the status as cancel or draft
     */
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
     * Test, Email Template of Confirm Status
     *@group test321
     */
    public function test_get_email_template_for_confirm_status() {

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id ]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Draft']);

        # Blank Request
        $res = $this->json('POST','get-email-template', [], $this->getAuthHeader());
        $this->assertResponseStatus(422);

        $res = $this->json('POST','get-email-template', ['placement_id'=> [$placement->id], 'status'=>'Confirmed'], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        # Action request to change the status and send email
        $email_data = json_decode($res->response->getContent(),true);
        $attachments = [UploadedFile::fake()->image('test.pdf'), UploadedFile::fake()->image('test2.pdf')];
        $param = $email_data['data'];
        $param['placement_id'] = [ $placement->id ];
        $param['status'] = 'Confirmed';

        $response = $this->call('POST','send-email-and-change-status',$param, [],['attachments'=> $attachments], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        
    }

    /**
     * Test, Email Template of cancelled Status
     */
    public function test_get_email_template_for_cancelled_status() {

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id ]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Draft']);


        # A vaild Request
        $res = $this->json('POST','get-email-template', ['placement_id'=> [$placement->id], 'status'=>'Cancelled'], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        # Action request to change the status and send email
        $email_data = json_decode($res->response->getContent(),true);
        $attachments = [UploadedFile::fake()->image('test.pdf'), UploadedFile::fake()->image('test2.pdf')];
        $param = $email_data['data'];
        $param['placement_id'] = [ $placement->id ];
        $param['status'] = 'Cancelled';

        $response = $this->call('POST','send-email-and-change-status',$param, [],['attachments'=> $attachments], $this->getAuthHeader());
        $this->assertResponseStatus(200);


        # When Placement Does not exists.
        $placement->delete();
        $res = $this->json('POST','get-email-template', ['placement_id'=> [$placement->id], 'status'=>'Cancelled'], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $res->seeJson(['status' => false]);
    }

    /**
     * Test to send blank request on route, which uses to send email and change status as Confirm or Cancelled
     */
    public function test_blank_param_to_change_status_with_email() {

        $res = $this->json('POST','send-email-and-change-status', [], $this->getAuthHeader());
        $this->assertResponseStatus(422);
    }

    /**
     * Test, Get confirm status email template, when the course run has been Deleted.
     * @group test_tt
     */
    public function test_get_email_template_for_confirm_status_course_run_deleted() {

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id,'no_of_trainees'=>1 ]);

        $courseRun->delete();

        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Draft']);


        $res = $this->json('POST','get-email-template', ['placement_id'=> [$placement->id], 'status'=>'Confirmed'], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $res->seeJson(['error_code' => 'INVALID_PLACE']);        
    }

    /**
     * Test, check conflict of two placement, when the course run class size is only one.
     * @group test_tt
     */
    public function test_check_conflict_of_two_placement_when_course_run_class_size_one() {

        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id,'no_of_trainees'=>1 ]);

        $courseRun->delete();

        $placement1 = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Draft']);
         $placement2 = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user2->personnel_number,'current_status'=>'Draft']);

         sleep(1);
        $res = $this->json('POST','placement/check-conflict', ['placement_id'=> [$placement1->id, $placement2->id], 'status'=>'Confirmed'], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $res->seeJson(['error_code' => 'CSO']);        
    }

    /**
     * Test, change confirm status of two placement, when the course run class size is only one.
     * @group test_tt1
     */
    public function test_change_confirm_status_of_two_placement_when_course_run_class_size_one() {

        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $course = factory(Course::class)->create();

        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id,'no_of_trainees'=>1 ]);

        $placement1 = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Draft']);
         $placement2 = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user2->personnel_number,'current_status'=>'Draft']);

         sleep(1);

        $res = $this->json('PUT','placement/make-status-confirmed', ['placement_id'=> [$placement1->id, $placement2->id], 'status'=>'Confirmed'], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $res->seeJson(['error_code' => 'INVALID_PLACE']);        
    }

    /**
     * Test, change confirm status of two placement, when status is already confirmed.
     * @group test_tt1
     */
    public function test_change_confirm_status_of_two_placement_when_already_confirmed() {

        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $course = factory(Course::class)->create();

        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id,'no_of_trainees'=>2 ]);

        $placement1 = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);
         $placement2 = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user2->personnel_number,'current_status'=>'Confirmed']);

         sleep(1);

        $res = $this->json('PUT','placement/make-status-confirmed', ['placement_id'=> [$placement1->id, $placement2->id], 'status'=>'Confirmed'], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $res->seeJson(['error_code' => 'INVALID_PLACE']);        
    }
}
