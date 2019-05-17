<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

use App\Models\ProgrammeCategory;
use App\Models\ProgrammeType;
use App\Models\CourseRun;
use App\Models\Course;
use App\Models\Placement;
use App\User;
use App\Models\FailureReason;
use App\Models\AbsentReason;
use App\Models\Department;

class PlacementResultUploadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_upload_with_blank_request() {
        

        $response = $this->call('POST','placement/upload-result', [],[],[], $this->getAuthHeader(true));
        $this->assertResponseStatus(422);
        $this->seeJson(['errors' => ['file'=> ['The file field is required.']]]);

    }
    
    public function test_upload_with_invalid_file() {
        
        $file_name = 'tms.png';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload-result',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(422);
        $this->seeJson(['error_code'=> 422]);
    }

    public function test_upload_when_file_contain_invalid_header() {

        $file_name = 'invalid_header.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload-result',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(500);
        $this->seeJson(['error_code' => 'FHNM']);
    }

    public function test_upload_when_placement_status_is_not_confirmed() {

        $course = factory(App\Models\Course::class)->create();
        $course_run = \App\Models\CourseRun::withTrashed()->where('id', 1)->first();

        if(!$course_run) {
            $course_run = factory(App\Models\CourseRun::class)->create(['course_code' =>  $course->course_code]);
        }

        $course_run->update(['deleted_at'=> null,'current_status' => 'Confirmed']);

        $placement = Placement::create(['course_run_id' => $course_run->id,'personnel_number'=> 99999,'current_status'=>'Draft']);

        $file_name = 'Placement_reports.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload-result',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $course->forceDelete();

    }

    public function test_upload_with_valid_data() {
        
        $course = factory(App\Models\Course::class)->create();
        $course_run = \App\Models\CourseRun::withTrashed()->where('id', 1)->first();

        if(!$course_run) {
            $course_run = factory(App\Models\CourseRun::class)->create(['id'=>1,'course_code' =>  $course->course_code]);
        }

        $course_run->update(['deleted_at'=> null,'current_status' => 'Confirmed']);

        Placement::where(['course_run_id' => $course_run->id,'personnel_number'=> 99999])->forceDelete();
        $placement = Placement::create(['course_run_id' => $course_run->id,'personnel_number'=> 99999,'current_status'=>'Confirmed']);

        $file_name = 'Placement_reports.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload-result',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(200);

    }

    public function test_upload_report_for_modify_result() {

        $course = factory(App\Models\Course::class)->create();
        $course_run = \App\Models\CourseRun::withTrashed()->where('id', 1)->first();

        if(!$course_run) {
            $course_run = factory(App\Models\CourseRun::class)->create(['id'=>1,'course_code' =>  $course->course_code]);
        }

        $course_run->update(['deleted_at'=> null,'current_status' => 'Confirmed']);

        Placement::where(['course_run_id' => $course_run->id,'personnel_number'=> 99999])->forceDelete();

        $placement = Placement::create(['course_run_id' => $course_run->id,'personnel_number'=> 99999,'current_status'=>'Confirmed','result_uploaded'=> 'Yes']);

        $file_name = 'Placement_reports.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload-result',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        
    }
}