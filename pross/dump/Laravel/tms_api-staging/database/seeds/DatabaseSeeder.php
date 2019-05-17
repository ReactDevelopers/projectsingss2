<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        
        if ($this->command->confirm('Are you sure? After your confirmation, the system will truncate all tables. (Mostly, this command uses to setup the project and insert the require data.)')) {

            Schema::disableForeignKeyConstraints();
                DB::table('roles')->truncate();
                DB::table('users')->truncate();
                DB::table('courses')->truncate();
                DB::table('course_runs')->truncate();
                DB::table('placements')->truncate();
                DB::table('placement_status_histories')->truncate();
                DB::table('course_run_status_histories')->truncate();
                DB::table('training_locations')->truncate();
                DB::table('programme_categories')->truncate();
                DB::table('programme_types')->truncate();
                DB::table('departments')->truncate();
                DB::table('failure_reasons')->truncate();
                DB::table('absent_reasons')->truncate();
                //DB::table('course_providers')->truncate(); 
                DB::table('email_templates')->truncate(); 
                DB::table('assessment_types')->truncate();
                DB::table('oauth_access_tokens')->truncate();

            Schema::enableForeignKeyConstraints();

            # Insert Roles
            $this->call('RolesTableSeeder');
            
            # Insert Admin user
            factory(\App\User::class)->create(['personnel_number'=>99999,'role_id'=>1]);
            # Insert Viewer User
            factory(\App\User::class)->create(['personnel_number'=>99998,'role_id'=>2,'supervisor_personnel_number'=> 99999]);


            # Insert the Training Location Data
            $this->call('TraningLocationTableSeeder');

            # Insert Programme Category
            $this->call('ProgrammeCategoriesTableSeeder');

            # Insert Programme Type
            $this->call('ProgrammeTypesTableSeeder');

            # Insert Department data
            $this->call('DepartmentTableSeeder');

            #FailureReasonsTableSeeder
            $this->call('FailureReasonsTableSeeder');
            #AbsentReasonsTableSeeder
            $this->call('AbsentReasonsTableSeeder');

            $this->call('CourseProvidersTableSeeder');
            $this->call('EmailTemplateSeeder');
            $this->call('AssessmentTypeSeeder');

            Artisan::call('cache:clear');
            $this->command->info('Sample data has been inserted.');
        }
    }
}
