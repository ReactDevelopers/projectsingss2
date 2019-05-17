<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\CourseRun;
use App\Models\Course;
use App\User;

class CourseRunChangeStatusTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test when send the blank request on the change status route
     */
    public function test_blank_request() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);

        $this->json('PUT','course-run/change-status/'.$courseRun->id, [], $this->getAuthHeader());
        $this->assertResponseStatus(422);
    }

    /**
     * Test when send the wrong status, System only accept the status either Draft,Confirmed,Completed, or Closed
     */
    public function test_passing_wrong_status() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $param = [
            'status' => 'Tets shdjgj'
        ];

        $this->json('PUT','course-run/change-status/'.$courseRun->id, $param, $this->getAuthHeader());
        $this->assertResponseStatus(422);
    }

    /**
     * When try to change status as completed and current status is not confirmed.
     */
    public function test_change_status_completed_when_current_status_not_confirmed() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code,'current_status'=> 'Draft']);
        $param = [
            'status' => 'Completed'
        ];

        $this->json('PUT','course-run/change-status/'.$courseRun->id, $param, $this->getAuthHeader());
        $this->assertResponseStatus(422);
    }

    /**
     * Test when the course run end date is in future and user tries to change the status as completed.
     */
    public function test_when_change_status_completed_and_end_date_is_in_future() {

        $course = factory(Course::class)->create();
        
        $courseRun = factory(CourseRun::class)->create([
        'course_code' => $course->course_code,
        'current_status'=> 'Confirmed',
        'start_date'=>date('Y-m-d')
        ]);

        $param = [
            'status' => 'Completed'
        ];

        $this->json('PUT','course-run/change-status/'.$courseRun->id, $param, $this->getAuthHeader());
        $this->assertResponseStatus(422);
    }

    /**
     * Test when the course run test end date is in future and user tries to change the status as completed.
     */
    public function test_when_change_status_completed_and_test_end_date_is_in_future() {

        $course = factory(Course::class)->create();
        
        $start_date = \Carbon\Carbon::now()->addDays(-5);
        $end_date = \Carbon\Carbon::now()->addDays(-2);
        $test_start_date = \Carbon\Carbon::now()->addDays(-1);
        $test_end_date = \Carbon\Carbon::now()->addDays(3);

        $courseRun = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'current_status'=> 'Confirmed',
            'start_date'=> $start_date->format('Y-m-d'),
            'end_date'=> $end_date->format('Y-m-d'),
            'assessment_start_date' => $test_start_date,
            'assessment_end_date' => $test_end_date
        ]);
        
        $param = [
            'status' => 'Completed'
        ];

        $this->json('PUT','course-run/change-status/'.$courseRun->id, $param, $this->getAuthHeader());
        $this->assertResponseStatus(422);
    }
    /**
     * Test when course end date and test test has passed. (Dates are not in future)
     */
    public function test_when_change_status_completed_and_dates_not_in_future() {

        $course = factory(Course::class)->create();
        
        $start_date = \Carbon\Carbon::now()->addDays(-90);
        $end_date = \Carbon\Carbon::now()->addDays(-80);

        $courseRun = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'current_status'=> 'Confirmed',
            'start_date'=> $start_date->format('Y-m-d'),
            'end_date'=> $end_date->format('Y-m-d'),
        ]);
        
        $param = [
            'status' => 'Completed'
        ];

        $this->json('PUT','course-run/change-status/'.$courseRun->id, $param, $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    /**
     * Test change status Draft => Closed, Closed => Draft.
     */
    public function test_change_status() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'current_status' => 'Draft'
        ]);

        $this->json('PUT','course-run/change-status/'.$courseRun->id, ['status'=>'Closed'], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res = $this->json('PUT','course-run/change-status/'.$courseRun->id, ['status'=>'Draft'], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    //change-de-conflict-status
    /**
     * Change the status to check the deconflict or not and with valid request
     */
    public function test_when_course_run_deleted() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'current_status' => 'Draft'
        ]);
        $courseRun->delete();

        $this->json('PUT','course-run/change-de-conflict-status/'.$courseRun->id, ['status'=>'Yes'], $this->getAuthHeader());
        $this->assertResponseStatus(404);

        $courseRun->restore();

        $this->json('PUT','course-run/change-de-conflict-status/'.$courseRun->id, ['status'=>'Yes'], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

}