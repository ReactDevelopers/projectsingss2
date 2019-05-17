<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

class PlacementUploadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_upload_with_blank_request() {
        

        $response = $this->call('POST','placement/upload', [],[],[], $this->getAuthHeader(true));
        $this->assertResponseStatus(422);
        $this->seeJson(['errors' => ['file'=> ['The file field is required.']]]);

    }
    
    public function test_upload_with_invalid_file() {
        
        $file_name = 'tms.png';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(422);
        $this->seeJson(['error_code'=> 422]);
    }

    public function test_upload_when_file_contain_invalid_header() {

        $file_name = 'invalid_header.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(500);
        $this->seeJson(['error_code' => 'FHNM']);
    }

    public function test_upload_when_course_run_status_is_not_confirmed() {

        $course = factory(App\Models\Course::class)->create();
        $course_run = \App\Models\CourseRun::withTrashed()->where('id', 1)->first();

        if(!$course_run) {
            $course_run = factory(App\Models\CourseRun::class)->create(['course_code' =>  $course->course_code]);
        }

        $course_run->update(['deleted_at'=> null,'current_status' => 'Draft']);

        $file_name = 'Placement.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload',[],[],['file'=> $file], $this->getAuthHeader());
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

        $file_name = 'Placement.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $course->forceDelete();
        $course_run->forceDelete();
    }

    public function test_placement_upload_duplicate_data() {
        
        $course = factory(App\Models\Course::class)->create();
        $course_run = \App\Models\CourseRun::withTrashed()->where('id', 1)->first();

        if(!$course_run) {
            $course_run = factory(App\Models\CourseRun::class)->create(['id'=>1,'course_code' =>  $course->course_code]);
        }

        $course_run->update(['deleted_at'=> null,'current_status' => 'Confirmed']);

        $file_name = 'PlacementDuplicate.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(500);
        $course->forceDelete();
        $course_run->forceDelete();
    }

    public function test_placement_upload_duplicate_data_forcefully() {
        
        $course = factory(App\Models\Course::class)->create();
        $course_run = \App\Models\CourseRun::withTrashed()->where('id', 1)->first();

        if(!$course_run) {
            $course_run = factory(App\Models\CourseRun::class)->create(['id'=>1,'course_code' =>  $course->course_code]);
        }

        $course_run->update(['deleted_at'=> null,'current_status' => 'Confirmed']);

        $file_name = 'PlacementDuplicate.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','placement/upload',['forceUpload'=>'Yes','skippedData'=>[199998]],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $course->forceDelete();
        $course_run->forceDelete();
    }
}