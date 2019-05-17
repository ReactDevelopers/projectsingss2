<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\CourseRun;
use App\Models\Course;
use App\Models\Placement;
use App\User;
use App\Models\FailureReason;
use App\Models\AbsentReason;
use App\Models\Department;

class PlacementListTest extends ListTestCase
{
    use DatabaseTransactions;

    public function test_with_empty_request () {

        $this->json('GET','placement/maintain-list', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    /**
     * Test the Course Code filtetr
     */
    public function test_course_code_filter() {
        
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> 99999,'current_status'=>'Confirmed']);

        $customFilters = ['course_code'=> [
            'value' => $courseRun->course_code,
            'comparator' => '='
        ]];
        $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
        //print_r($res->response->getContent()); exit;
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('course_code', $course->course_code)->assertDataLength(1,'>=');
    }

    /**
     * Test the Course Title filtetr
     */
    public function test_course_title_filter() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> 99999,'current_status'=>'Confirmed']);

         $customFilters = ['title'=> [
             'value' => $course->title,
             'comparator' => 'LIKE'
         ]];

         $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
         $this->assertResponseStatus(200);
         $this->setData($res)->assertDataMatchStart('title', $course->title)->assertDataLength(1,'>=');
    }

    /**
      * Test the Programme Category Filter
      */
      public function test_prog_cate_filter() {

        $pc = ProgrammeCategory::inRandomOrder()->first();
        $customFilters = ['prog_category_name'=> [
            'value' => $pc->id,
            'comparator' => '='
        ]];

        $course = factory(Course::class)->create(['programme_category_id'=> $pc->id]);
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> 99999,'current_status'=>'Confirmed']);

        $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('prog_category_name', $pc->prog_category_name);
    }

    /**
     * Test Created by Filter
     */
    public function test_created_by_filter() {
        
        $user = User::where('personnel_number', 99999)->first();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id ]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> 99999,'current_status'=>'Confirmed','creator_id'=> $user->id]);

        $customFilters = [
        'created_by'=> [
            'value' => $courseRun->creator_id,
            'comparator' => '='
        ]];

       $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());       

       $this->assertResponseStatus(200);
       $this->setData($res)->assertDataExact('created_by', $user->name)->assertDataLength(1,'>=');
    }

    /**
     * Test percipient_name Filter
     */
    public function test_percipient_name_filter() {
        
        $user = User::where('personnel_number', 99999)->first();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id ]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> 99999, 'current_status'=>'Confirmed']);

        $customFilters = [
        'percipient_name'=> [
            'value' => $user->id,
            'comparator' => '='
        ]];

       $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());       
       $this->assertResponseStatus(200);
       $this->setData($res)->assertDataExact('percipient_name', $user->name)->assertDataLength(1,'>=');
    }

    /**
     * Test supervisor_name Filter
     */
    public function test_supervisor_name_filter() {
        
        $supervisor = factory(User::class)->create();
        $user = factory(User::class)->create(['supervisor_personnel_number'=> $supervisor->personnel_number]);

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id ]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number, 'current_status'=>'Confirmed']);

        $customFilters = [
        'supervisor_name'=> [
            'value' => $supervisor->name,
            'comparator' => 'LIKE'
        ]];

       $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());       
       $this->assertResponseStatus(200);
       $this->setData($res)->assertDataMatchStart('supervisor_name', $supervisor->name)->assertDataLength(1,'>=');
    }

     /**
     * Test Personnel Number Filter
     */
    public function test_personnel_number_filters() {

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id ]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        $customFilters = ['personnel_number'=> [
            'value' => $user->personnel_number,
            'comparator' => 'LIKE'
        ]];
        $res = $this->json('GET','placement/maintain-list', ['customFilters'=>$customFilters], $this->getAuthHeader());
        

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataMatchStart('personnel_number', $user->personnel_number)->assertDataLength(1,'>=');
    }

    /**
     * Test Course Start/End Date filter 
     */

    public function test_course_start_end_date_filter() {
        
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        $customFilters = [
       'date_range'=> [
           'value' => ['start_date'=> $courseRun->start_date, 'end_date' => $courseRun->end_date],
           'comparator' => 'date-range'
       ]];

       $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
       
       $this->assertResponseStatus(200);
       $this->setData($res)
            ->assertDataDate('start_date', $courseRun->start_date, '>=')
           ->assertDataDate('end_date',  $courseRun->end_date, '<=')
           ->assertDataLength(1,'>=');
    }

    /**
      * Test User Department Filter
      */
      public function test_department_filter() {

        $dept = Department::first();
        $user = factory(User::class)->create(['department_id'=> $dept->id]);
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        $customFilters = ['user_dept_name'=> [
            'value' => $user->department_id,
            'comparator' => '='
        ]];
        $res = $this->json('GET','placement/maintain-list', ['customFilters'=>$customFilters], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('user_dept_name', $dept->dept_name)->assertDataLength(1,'>=');
     }

     /**
     * Test Placement Current Status filter 
     */
    public function test_currnet_status_filter() {
        
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        $customFilters = [
       'current_status'=> [
           'value' => 'Confirmed',
           'comparator' => '='
       ]];

       $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
       
       $this->assertResponseStatus(200);
       $this->setData($res)->assertDataExact('current_status', 'Confirmed')->assertDataLength(1,'>=');
    }

     /**
     * Test Placement Result Uploaded filter 
     */
    public function test_result_uploaded_filter() {
        
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        $customFilters = [
        'result_uploaded'=> [
            'value' => 'No',
            'comparator' => '='
        ]];

       $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
       
       $this->assertResponseStatus(200);
       $this->setData($res)->assertDataExact('result_uploaded', 'No')->assertDataLength(1,'>=');
    }

     /**
     * Test Course Run ID filter 
     */
    public function test_course_run_id_filter() {
        
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        $customFilters = [
        'course_run_id'=> [
            'value' => $courseRun->id,
            'comparator' => 'LIKE'
        ]];

       $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
       
       $this->assertResponseStatus(200);
       $this->setData($res)->assertDataMatchStart('course_run_id', $courseRun->id)->assertDataLength(1,'>=');
    }

    /**
     * Test Assessment start/end date filter
     */

    public function test_assessment_start_end_dates_filter() {
        
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        $customFilters = [
        'test_date_range'=> [
            'value' => ['start_date'=> $courseRun->assessment_start_date, 'end_date' => $courseRun->assessment_end_date],
            'comparator' => 'date-range'
        ]];

       $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
       
       $this->assertResponseStatus(200);
       $this->setData($res)->assertDataDate('assessment_start_date', $courseRun->assessment_start_date, '>=')
           ->assertDataDate('assessment_end_date',  $courseRun->assessment_end_date, '<=')
           ->assertDataLength(1,'>=');  
    }
    
    /**
     * Test Programme type filter
     */
    public function test_prog_type_name_filter () {

        # Test Type Filter
        $py = ProgrammeType::inRandomOrder()->first();
        $customFilters = ['prog_type_name'=> [
            'value' => $py->id,
            'comparator' => '='
        ]];
        
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create(['programme_type_id'=> $py->id]);
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $placement = Placement::create(['course_run_id' => $courseRun->id,'personnel_number'=> $user->personnel_number,'current_status'=>'Confirmed']);

        $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('prog_type_name', $py->prog_type_name)->assertDataLength(1,'>=');
        
        # Test Method to get Single Course run placement
        $res = $this->json('GET','placement/maintain-list/'.$courseRun->id, ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)
        ->assertDataExact('prog_type_name', $py->prog_type_name)
        ->assertDataExact('course_run_id', $courseRun->id)
        ->assertDataLength(1,'>=');
    }

    /**
     * Test failure_reason Filter
     */

     public function test_failure_reason_filter() {
         
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $failureReason = FailureReason::inRandomOrder()->first();

        $placement = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> $user->personnel_number,
            'result_uploaded'=>'Yes',
            'attendance' => 'Present',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'current_status'=>'Confirmed'
        ]);

        $customFilters = [
            
            'failure_reason'=> [
                'value' => $failureReason->id,
                'comparator' => '='
            ],
            'current_status'=> [
                'value' => ['Confirmed','Draft'],
                'comparator' => '='
            ]
        ];

        $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('failure_reason', $failureReason->failure_reason)->assertDataLength(1,'>=');
     }

     /**
     * Test absent_reason Filter
     */

    public function test_absent_reason_filter() {
         
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $failureReason = FailureReason::inRandomOrder()->first();
        $absentReason = AbsentReason::inRandomOrder()->first();

        $placement = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> $user->personnel_number,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);

        $customFilters = [
            'absent_reason'=> 
                [
                    'value' => $absentReason->id,
                    'comparator' => '='
                ],
            'attendance'=> 
                [
                    'value' => 'Absent',
                    'comparator' => '='
                ],
            'assessment_results'=> 
                [
                    'value' => 'Fail',
                    'comparator' => '='
                ]
        ];

        $res = $this->json('GET','placement/maintain-list', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)
            ->assertDataExact('absent_reason', $absentReason->absent_reason)
            ->assertDataExact('attendance', 'Absent')
            ->assertDataExact('assessment_results', 'Fail')
            ->assertDataLength(1,'>=');
     }

     public function test_sorting() {

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $failureReason = FailureReason::inRandomOrder()->first();
        $absentReason = AbsentReason::inRandomOrder()->first();

        $placement = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> $user->personnel_number,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);
        
        $faker = \Faker\Factory::create();

        $columns = [
            'course_code',
            'title',
            'percipient_name',
            'date_range',
            'test_date_range',
            'assessment_results',
            'user_dept_name',
            'supervisor_name',
            'course_run_id',
            'course_title',
            'prog_type_name',
            'attendance',
            'prog_category_name',
            'personnel_number',
            'percipient_name',
            'division',
            'branch',
            'failure_reason',
            'absent_reason'
        ];

        foreach($columns as $col) {

            $params['sortName'] = $col;
            $params['sortOrder'] = $faker->randomElement(['asc','desc']);

            $res = $this->json('GET','placement/maintain-list', $params, $this->getAuthHeader());
                $this->assertResponseStatus(200);
                $this->setData($res)
                    ->assertDataLength(1, '>=');
        }
    }

    public function test_export() {

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $failureReason = FailureReason::inRandomOrder()->first();
        $absentReason = AbsentReason::inRandomOrder()->first();
        $placement = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> $user->personnel_number,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);

        $res = $this->json('GET','placement/maintain-list', ['export'=> true], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        # Only selected row
        $res = $this->json('GET','placement/maintain-list', ['export'=> true,'selected' => [$placement->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    /**
     * Test to delete the placement
     */

    public function test_to_delete_placement() {

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $failureReason = FailureReason::inRandomOrder()->first();
        $absentReason = AbsentReason::inRandomOrder()->first();
        $placement = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> $user->personnel_number,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);
        $this->json('DELETE','placement',['ids'=> [$placement->id]], $this->getAuthHeader());
        $res_placement = Placement::where('id', $placement->id)->first();
        $this->assertResponseStatus(200);
        $this->assertTrue(!$res_placement);
    }

    public function test_to_delete_placement_result() {

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $failureReason = FailureReason::inRandomOrder()->first();
        $absentReason = AbsentReason::inRandomOrder()->first();
        $placement = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> $user->personnel_number,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);
        $this->json('DELETE','placement/result',['ids'=> [$placement->id]], $this->getAuthHeader());
        $res_placement = Placement::where('id', $placement->id)->first();
        $this->assertResponseStatus(200);
        $this->assertTrue( ($res_placement->result_uploaded === 'No') );
    }

    /**
     * TEst Section, My placement List, My subordinate Placement List, Submit/Update Post Course Run Data
     */
    public function test_similar_section(){

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $failureReason = FailureReason::inRandomOrder()->first();
        $absentReason = AbsentReason::inRandomOrder()->first();

        $placement = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> $user->personnel_number,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);

        $placement2 = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> 99999,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);

        $placement3 = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> 99998,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);

        $customFilters = [
            'absent_reason'=> 
                [
                    'value' => $absentReason->id,
                    'comparator' => '='
                ],
            'attendance'=> 
                [
                    'value' => 'Absent',
                    'comparator' => '='
                ],
            'assessment_results'=> 
                [
                    'value' => 'Fail',
                    'comparator' => '='
                ]
        ];
        
        # My Placement List
        $res = $this->json('GET','viewer/placement', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)
        ->assertDataExact('personnel_number', 99999)
        ->assertDataLength(1,'>=');

        # My subordinate Placement List
        $res = $this->json('GET','viewer/subordinate-placement', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
       // print_r($res->response->getContent()); exit;
        $this->setData($res)
        ->assertDataExact('supervisor_personnel_number', 99999)
        ->assertDataLength(1,'>=');

        # Submit/Update Post Course Run Data
        $res = $this->json('GET','placement/post-course-list', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('result_uploaded', 'Yes')->assertDataLength(1,'>=');

        # Placement Report
        $res = $this->json('GET','placement/report-list', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    /**
     * TEst Section, My placement List, My subordinate Placement List, Submit/Update Post Course Run Data
     */
    public function test_similar_section_for_export(){

        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $failureReason = FailureReason::inRandomOrder()->first();
        $absentReason = AbsentReason::inRandomOrder()->first();
        $placement = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> $user->personnel_number,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);

        $placement2 = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> 99999,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);

        $placement3 = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> 99998,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> $failureReason->id,
            'absent_reason_id' => $absentReason->id,
            'current_status'=>'Confirmed'
        ]);

        $customFilters = [
            'absent_reason'=> 
                [
                    'value' => $absentReason->id,
                    'comparator' => '='
                ],
            'attendance'=> 
                [
                    'value' => 'Absent',
                    'comparator' => '='
                ],
            'assessment_results'=> 
                [
                    'value' => 'Fail',
                    'comparator' => '='
                ]
        ];
        
        # My Placement List
        $res = $this->json('GET','viewer/placement', ['customFilters'=> $customFilters, 'export' => true, 'selected'=>[$placement2->id] ], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        # My subordinate Placement List
        $res = $this->json('GET','viewer/subordinate-placement', ['customFilters'=> $customFilters,'export' => true, 'selected'=>[$placement3->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        # Submit/Update Post Course Run Data
        $res = $this->json('GET','placement/post-course-list', ['export' => true,'selected'=>[$placement2->id, $placement3->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        # Placement Report
        $res = $this->json('GET','placement/report-list', ['export' => true,'selected'=>[$placement2->id, $placement3->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }
}