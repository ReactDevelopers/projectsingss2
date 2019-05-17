<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\CourseRun;
use App\Models\Course;
use App\Models\Department;
use App\User;

class StaffListTest extends ListTestCase
{

    use DatabaseTransactions;

    /**
     * Test to send blank request 
     */
    public function test_with_empty_request () {

        $this->json('GET','user', [], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }

    /**
     * Test Personnel Number Filter
     */
    public function test_personnel_number_filters() {

        $user = factory(User::class)->create();
        $customFilters = ['personnel_number'=> [
            'value' => $user->personnel_number,
            'comparator' => 'LIKE'
        ]];
        $res = $this->json('GET','user', ['customFilters'=>$customFilters], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataMatchStart('personnel_number', $user->personnel_number)->assertDataLength(1,'>=');
    }

    /**
     * Test Officer Name filter
     */
     public function test_officer_name_filter() {

        $user = factory(User::class)->create();
        $customFilters = ['name'=> [
            'value' => $user->name,
            'comparator' => 'LIKE'
        ]];
        $res = $this->json('GET','user', ['customFilters'=>$customFilters], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataMatchStart('name', $user->name)->assertDataLength(1,'>=');
     }

     /**
      * Test Email Filter
      */
     public function test_email_filter() {

        $user = factory(User::class)->create();
        $customFilters = ['email'=> [
            'value' => $user->email,
            'comparator' => 'LIKE'
        ]];
        $res = $this->json('GET','user', ['customFilters'=>$customFilters], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataMatchStart('email', $user->email)->assertDataLength(1,'>=');
     }

     /**
      * Test Department Filter
      */
      public function test_department_filter() {

        $dept = Department::first();

        $user = factory(User::class)->create(['department_id'=> $dept->id]);
        $customFilters = ['user_dept_name'=> [
            'value' => $user->department_id,
            'comparator' => '='
        ]];
        $res = $this->json('GET','user', ['customFilters'=>$customFilters], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('user_dept_name', $dept->dept_name)->assertDataLength(1,'>=');
     }

     /**
      * Test designation Filter
      */

      public function test_designation_filter() {
        
        $faker = \Faker\Factory::create();
        $des = $faker->words(2, true);

        $user = factory(User::class)->create(['designation'=>$des ]);
        $customFilters = ['designation'=> [
            'value' => $user->designation,
            'comparator' => 'LIKE'
        ]];
        $res = $this->json('GET','user', ['customFilters'=>$customFilters], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataMatchStart('designation', $user->designation)->assertDataLength(1,'>=');
     }

     /**
      * Test division Filter
      */

      public function test_division_filter() {
        
        $faker = \Faker\Factory::create();
        $des = $faker->words(2, true);

        $user = factory(User::class)->create(['division'=>$des ]);
        $customFilters = ['division'=> [
            'value' => $user->division,
            'comparator' => 'LIKE'
        ]];
        $res = $this->json('GET','user', ['customFilters'=>$customFilters], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataMatchStart('division', $user->division)->assertDataLength(1,'>=');
     }

     /**
      * Test Role Filter
      */
      public function test_role_filter() {


        $user = factory(User::class)->create();
        $customFilters = ['role_name'=> [
            'value' => $user->role_id,
            'comparator' => '='
        ]];
        $res = $this->json('GET','user', ['customFilters'=>$customFilters], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataExact('role_id', $user->role_id)->assertDataLength(1,'>=');
     }

     /**
      * Test Role Filter
      */
      public function test_supervisor_filter() {

        $supervisor = factory(User::class)->create();
        $user = factory(User::class)->create(['supervisor_personnel_number' => $supervisor->personnel_number]);

        $customFilters = ['supervisor_name'=> [
            'value' => $supervisor->name,
            'comparator' => 'LIKE'
        ]];
        $res = $this->json('GET','user', ['customFilters'=>$customFilters], $this->getAuthHeader());

        $this->assertResponseStatus(200);
        $this->setData($res)->assertDataMatchStart('supervisor_name', $supervisor->name)->assertDataLength(1,'>=');
     }

     public function test_sorting() {

        $supervisor = factory(User::class)->create();
        $user = factory(User::class)->create(['supervisor_personnel_number' => $supervisor->personnel_number]);
        $faker = \Faker\Factory::create();
        
        $columns = [
            'supervisor_name',
            'role_name',
            'division',
            'designation',
            'user_dept_name',
            'email',
            'name',
            'personnel_number',
            'supervisor_personnel_number'
        ];

        foreach($columns as $col) {

            $params['sortName'] = $col;
            $params['sortOrder'] = $faker->randomElement(['asc','desc']);

            $res = $this->json('GET','user', $params, $this->getAuthHeader());
            $this->assertResponseStatus(200);
            $this->setData($res)
                ->assertDataLength(1, '>=');
        }
    }

    /**
     * test Export Data in excel
     */
    public function test_export() {

        $supervisor = factory(User::class)->create();
        $user = factory(User::class)->create(['supervisor_personnel_number' => $supervisor->personnel_number]);

        $res = $this->json('GET','user', ['export'=> true], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        # Only selected row
        $res = $this->json('GET','user', ['export'=> true,'selected' => [$user->id]], $this->getAuthHeader());
        $this->assertResponseStatus(200);
    }
    
    /**
     * Test to Change the User role.
     */
    public function test_change_role() {

        $supervisor = factory(User::class)->create();
        $user = factory(User::class)->create(['supervisor_personnel_number' => $supervisor->personnel_number]);
        $user->delete();
        # when User does not exxist in database.
        $res = $this->json('PUT','user/change-role/'.$user->id, [],$this->getAuthHeader());
        $this->assertResponseStatus(404);

        # When Assign the N/A Role

        $supervisor = factory(User::class)->create();
        $user = factory(User::class)->create(['supervisor_personnel_number' => $supervisor->personnel_number]);

        $res = $this->json('PUT','user/change-role/'.$user->id,['role_id'=> null] ,$this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res_user = User::where('id', $user->id)->first();
        $this->assertTrue(($res_user->role_id == null) );

        # Switch Role
        $supervisor = factory(User::class)->create();
        $user = factory(User::class)->create(['supervisor_personnel_number' => $supervisor->personnel_number]);
        $role = \App\Models\Role::inRandomOrder()->first();

        $res = $this->json('PUT','user/change-role/'.$user->id,['role_id'=> $role->id] ,$this->getAuthHeader());
        $this->assertResponseStatus(200);

        $res_user = User::where('id', $user->id)->first();
        $this->assertTrue(($res_user->role_id == $role->id) );
    }
}