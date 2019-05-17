<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\CourseRun;
use App\Models\Course;
use App\Models\Placement;
use App\User;

class CourseListTest extends ListTestCase
{
    use DatabaseTransactions;

    public function test_with_empty_request () {

        $this->json('GET','course', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }
    
    /**
     * Test Filters
     */
    public function test_filter() {

        $course = factory(Course::class)->create();

        $customFilters = [

            'course_code' => ['value'=> $course->course_code,'comparator'=> 'Like'],
            'course_title' => ['value'=> $course->title,'comparator'=> 'Like'],
            'duration_in_days' => ['value'=> $course->duration_in_days,'comparator'=> '='],
            'prog_type_name' => ['value'=> $course->programme_type_id,'comparator' => '=' ],
            'prog_category_name' => ['value'=> $course->programme_category_id,'comparator' => '=' ]
        ];

        $res = $this->json('GET','course', ['customFilters'=> $customFilters], $this->getAuthHeader());
        
        $this->assertResponseStatus(200);
        $this->setData($res)
            ->assertDataMatchStart('course_code', $course->course_code)
            ->assertDataMatchStart('course_title', $course->title)
            ->assertDataExact('duration_in_days', $course->duration_in_days)
            ->assertDataExact('prog_type_name', ProgrammeType::where('id',$course->programme_type_id)->first()->prog_type_name)
            ->assertDataExact('prog_category_name', ProgrammeCategory::where('id',$course->programme_category_id)->first()->prog_category_name)
            ->assertDataLength(1,'>=');
    }

    public function test_sorting() {

        $course = factory(Course::class)->create();
        
        $faker = \Faker\Factory::create();

        $columns = [
            'course_code',
            'course_title',
            'duration_in_days',
            'prog_type_name',
            'prog_category_name',
            'wrong'
        ];

        foreach($columns as $col) {

            $params['sortName'] = $col;
            $params['sortOrder'] = $faker->randomElement(['asc','desc']);

            $res = $this->json('GET','course', $params, $this->getAuthHeader());
                $this->assertResponseStatus(200);
                $this->setData($res)
                    ->assertDataLength(1, '>=');
        }
    }

    /**
     * Test the Export Functionality.
     */
    public function test_export() {

        $course = factory(Course::class)->create();

        $res = $this->json('GET','course', ['export'=> true,'selected'=>[$course->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }
    /**
     * To get the detail of a Course
     */
    public function test_get_course() {

        $course = factory(Course::class)->create();

        $this->json('GET','course/'.$course->id, [], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $course->delete();

        $this->json('GET','course/'.$course->id, [], $this->getAuthHeader());
        $this->assertResponseStatus(404);

    }

    /**
     * Test Single and bulk course delete
     */
    public function test_course_delete() {

        $course = factory(Course::class)->create();

        $this->json('DELETE','course/'.$course->id,[], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $course = factory(Course::class)->create();
        $this->json('DELETE','course',['ids'=> [$course->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $this->json('DELETE','course',[], $this->getAuthHeader());
        $this->assertResponseStatus(422);
    }

    /**
     * Test to Get the list of Valid Data
     */
     public function test_get_valid_data() {

        $course = factory(Course::class)->create();

        $res = $this->json('GET','get-list', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);
     }
}