<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\CourseRun;
use App\Models\Course;
use App\Models\Placement;
use App\User;

class CourseRunListTest extends ListTestCase
{
    use DatabaseTransactions;

    public function test_with_empty_request () {

        $this->json('GET','course-run', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    /**
     * Test the Course Code filtetr
     */
    public function test_course_code_filter() {
        
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);

        $customFilters = ['course_code'=> [
            'value' => $courseRun->course_code,
            'comparator' => '='
        ]];
        $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
        
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('course_code', $course->course_code)->assertDataLength(1,'>=');
    }
    /**
     * Test the Course Title filtetr
     */
    public function test_course_title_filter() {

        $course = factory(Course::class)->create();
         $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
 
         $customFilters = ['title'=> [
             'value' => $course->title,
             'comparator' => 'LIKE'
         ]];

         $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
         $this->assertResponseStatus(200);
         $this->setData($res)->assertDataMatchStart('title', $course->title)->assertDataLength(1,'>=');
    }

    /**
     * Test Course Start/End Date filter 
     */

     public function test_course_start_end_date_filter() {
        
         $course = factory(Course::class)->create();
         $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        
         $customFilters = [
        'date_range'=> [
            'value' => ['start_date'=> $courseRun->start_date, 'end_date' => $courseRun->end_date],
            'comparator' => 'date-range'
        ]];

        $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
        
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataDate('start_date', $courseRun->start_date, '>=')
            ->assertDataDate('end_date',  $courseRun->end_date, '<=')
            ->assertDataLength(1,'>=');
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

        $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('prog_category_name', $pc->prog_category_name);
    }

    /**
     * Test Assessment start/end date filter
     */

    public function test_assessment_start_end_dates_filter() {
        
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);

        $customFilters = [
        'test_date_range'=> [
            'value' => ['start_date'=> $courseRun->assessment_start_date, 'end_date' => $courseRun->assessment_end_date],
            'comparator' => 'date-range'
        ]];

       $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
       
       $this->assertResponseStatus(200);
       $this->setData($res)->assertDataDate('assessment_start_date', $courseRun->assessment_start_date, '>=')
           ->assertDataDate('assessment_end_date',  $courseRun->assessment_end_date, '<=')
           ->assertDataLength(1,'>=');  
    }

    /**
     * Test Created by Filter
     */
    public function test_created_by_filter() {
        
        $user = User::where('personnel_number', 99999)->first();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code, 'creator_id'=> $user->id ]);
       
        $customFilters = [
        'created_by'=> [
            'value' => $courseRun->creator_id,
            'comparator' => '='
        ]];

       $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
       
       $this->assertResponseStatus(200);
       $this->setData($res)->assertDataExact('created_by', $user->name)->assertDataLength(1,'>=');
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

        $course = factory(Course::class)->create(['programme_type_id'=> $py->id]);
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);

        $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('prog_type_name', $py->prog_type_name)->assertDataLength(1,'>=');
    }

    public function test_filters() {
               

        # Test Course Run id Filter        
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);

        $customFilters = ['id'=> [
            'value' => $courseRun->id,
            'comparator' => '='
        ]];

        $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('id', $courseRun->id)->assertDataCount(1);


       # test no_of_trainee Filter        
       $this->verifyNumericColumn(['no_of_trainee'],[], ['no_of_trainee' => 'no_of_trainees']);

       # test summary_uploaded Filter
       $course = factory(Course::class)->create();
       $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code,'summary_uploaded'=>'Yes']);
       
        $customFilters = [
        'summary_uploaded'=> [
            'value' => $courseRun->summary_uploaded,
            'comparator' => '='
        ]];

        $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)
            ->assertDataLength(1, '>=');
    
        # Verify summary Filter 
        $this->verifyNumericColumn(['admin','trainer_delivery','content_relevance','site_visits', 'facilities'],[], []);

    }

    /**
     * Test Other Similar Section Like, 
     * Sumbit/Update Post Course Summary Data, 
     * Course Run Report
     */
    public function test_other_similar_section () {

        # test summary_uploaded Filter
       $course = factory(Course::class)->create();
       $courseRun = factory(CourseRun::class)->create([
           'course_code' => $course->course_code,
           'summary_uploaded'=>'Yes',
           'current_status' => 'Confirmed'
       ]);
       $courseRun = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'summary_uploaded'=>'Yes',
            'current_status' => 'Confirmed',
            'start_date' => \Carbon\Carbon::now()->addDays(-2)->format('Y-m-d'),
            'end_date' => \Carbon\Carbon::now()->addDays(5)->format('Y-m-d')
        ]);

        $res = $this->json('GET','course-run/edit-status-list', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res = $this->json('GET','course-run/post-summary-list', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res = $this->json('GET','course-run/report-list', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res = $this->json('GET','viewer/course-run', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataLength(1,'>=');
    }

    /**
     * Test Other Similar Section Like, 
     * Sumbit/Update Post Course Summary Data, 
     * Course Run Report
     */
    public function test_other_similar_section_for_export () {

        # test summary_uploaded Filter
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code,'summary_uploaded'=>'Yes']);

        $courseRun = factory(CourseRun::class)->create([
            'course_code' => $course->course_code,
            'summary_uploaded'=>'Yes',
            'current_status' => 'Confirmed',
            'start_date' => \Carbon\Carbon::now()->addDays(-2)->format('Y-m-d'),
            'end_date' => \Carbon\Carbon::now()->addDays(5)->format('Y-m-d')
        ]);

        $placement = Placement::create([
            'course_run_id' => $courseRun->id,
            'personnel_number'=> $user->personnel_number,
            'result_uploaded'=>'Yes',
            'attendance' => 'Absent',
            'assessment_results' => 'Fail',
            'failure_reason_id'=> 1,
            'absent_reason_id' => 8,
            'current_status'=>'Confirmed'
        ]);

        $res = $this->json('GET','course-run/edit-status-list', ['export'=> true, 'selected'=> [$courseRun->id] ], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res = $this->json('GET','course-run/post-summary-list', ['export'=> true, 'selected'=> [$courseRun->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res = $this->json('GET','course-run/report-list', ['export'=> true, 'selected'=> [$courseRun->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res = $this->json('GET','viewer/course-run', ['export'=> true, 'selected'=> [$courseRun->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    /**
     * Test List Sorting
     */
    public function test_sorting() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
        $faker = \Faker\Factory::create();

        $columns = [
            'prog_category_name',
            'id',
            'course_code',
            'course_title',
            'title',
            'duration',
            'date_range',
            'test_date_range',
            'created_by',
            'enrolled',
            'no_of_trainee',
            'prog_type_name',
            'trainer_delivery',
            'content_relevance',
            'site_visits',
            'facilities',
            'admin',
            'current_status',
            'no_of_failure',
            'no_of_absantee',
            'wrong_key'
        ];

        foreach($columns as $col) {

            $params['sortName'] = $col;
            $params['sortOrder'] = $faker->randomElement(['asc','desc']);

            $res = $this->json('GET','course-run', $params, $this->getAuthHeader());
                $this->assertResponseStatus(200);
                $this->setData($res)
                    ->assertDataLength(1, '>=');
        }
    }

    public function test_export() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);

        $res = $this->json('GET','course-run', ['export'=> true], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        # Only selected row
        $res = $this->json('GET','course-run', ['export'=> true,'selected' => [$courseRun->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    public function test_delete_course_run() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);

        $this->json('DELETE','course-run/'.$courseRun->id,[], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        
    }

    public function test_batch_delete_course_run() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);

        $this->json('DELETE','course-run',['ids'=> [$courseRun->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    public function test_delete_course_run_summary() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code,'summary_uploaded'=> 'Yes']);

        $this->json('DELETE','course-run/summary/'.$courseRun->id,[], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    public function test_batch_delete_course_run_summary() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code,'summary_uploaded'=> 'Yes']);

        $this->json('DELETE','course-run/summary',['ids'=> [$courseRun->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    /**
     * To get the detail of a Course run
     */
    public function test_get_course_run() {

        $course = factory(Course::class)->create();
        $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code,'summary_uploaded'=> 'Yes']);

        $this->json('GET','course-run/'.$courseRun->id,[], $this->getAuthHeader());

        $this->assertResponseStatus(200);

        $courseRun->delete();

        $this->json('GET','course-run/'.$courseRun->id,[], $this->getAuthHeader());
        $this->assertResponseStatus(404);

    }

    /**
     * Verify the Numeric Column.
     */
    protected function verifyNumericColumn(Array $columns, Array $result_key= [], Array $db_key= []) {

        foreach($columns as $filter_key) {

            $operators = ['=','<=','>=','>','<'];

            $result_key_n = isset($result_key[$filter_key]) ? $result_key[$filter_key] : $filter_key;
            $db_key_n = isset($db_key[$filter_key]) ? $db_key[$filter_key] : $filter_key;

            foreach($operators as $operator) {

                $course = factory(Course::class)->create();
                $courseRun = factory(CourseRun::class)->create(['course_code' => $course->course_code]);
                
                $val = $courseRun->{$db_key_n};

                if($operator == '<') {
                    $val++;
                }
                if($operator == '>') {
                    $val--;
                }

                $customFilters  = [];
                $customFilters[$filter_key] = [
                    'value' => $val,
                    'comparator' => $operator
                ];

                $res = $this->json('GET','course-run', ['customFilters'=> $customFilters], $this->getAuthHeader());
                $this->assertResponseStatus(200);
                $this->setData($res)
                    ->assertDataNumeric($result_key_n, $val, $operator)
                    ->assertDataLength(1, '>=');
            }
        }
    }


}