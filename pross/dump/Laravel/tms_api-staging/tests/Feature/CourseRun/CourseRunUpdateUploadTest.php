<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

class CourseRunUpdateUploadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_upload_with_blank_request() {
        

        $response = $this->call('POST','course-run/upload-existed', [],[],[], $this->getAuthHeader(true));
        $this->assertResponseStatus(422);
        $this->seeJson(['errors' => ['file'=> ['The file field is required.']]]);

    }
    
    public function test_upload_with_invalid_file() {
        
        $file_name = 'tms.png';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','course-run/upload-existed',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(422);
        $this->seeJson(['error_code'=> 422]);
    }

    public function test_upload_when_file_contain_invalid_header() {

        $file_name = 'invalid_header.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','course-run/upload-existed',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(500);
        $this->seeJson(['error_code' => 'FHNM']);
    }
    /**
     * @group update_course_upload
     */
    public function test_upload_with_valid_data() {
        
        \App\Models\Course::where('course_code','PCSYS623')->forceDelete();

        $course = factory(App\Models\Course::class)->create(['course_code'=> 'PCSYS623']);
        $courseRun = App\Models\CourseRun::withTrashed()->where('id',1)->first();

        if(!$courseRun) {
            $courseRun = factory(App\Models\CourseRun::class)->create(['id'=> 1, 'course_code'=> $course->course_code]);
        }

        $courseRun->update(['deleted'=> null]);

        $courseRun = App\Models\CourseRun::withTrashed()->where('id',2)->first();

        if(!$courseRun) {
            $courseRun = factory(App\Models\CourseRun::class)->create(['id'=> 2, 'course_code'=> $course->course_code,'current_status'=>'Closed']);
        }

        $courseRun->update(['deleted'=> null,'current_status'=> 'Closed']);

        $file_name = 'Update_course_runs.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','course-run/upload-existed',[],[],['file'=> $file], $this->getAuthHeader());
        //print_r($response); exit;
        //$this->assertResponseStatus(200);


        $courseRun->update(['deleted'=> null,'current_status'=> 'Confirmed']);
        
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','course-run/upload-existed',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $course->forceDelete();
    }
}