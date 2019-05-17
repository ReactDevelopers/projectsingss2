<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

class CourseRunUploadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_upload_with_blank_request() {
        

        $response = $this->call('POST','course-run/upload-new', [],[],[], $this->getAuthHeader(true));
        $this->assertResponseStatus(422);
        $this->seeJson(['errors' => ['file'=> ['The file field is required.']]]);

    }
    
    public function test_upload_with_invalid_file() {
        
        $file_name = 'tms.png';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','course-run/upload-new',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(422);
        $this->seeJson(['error_code'=> 422]);
    }

    public function test_upload_when_file_contain_invalid_header() {

        $file_name = 'invalid_header.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','course-run/upload-new',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(500);
        $this->seeJson(['error_code' => 'FHNM']);
    }

    public function test_upload_with_valid_data() {
        
        $course = factory(App\Models\Course::class)->create(['course_code'=> 'PCSYS623']);
        $file_name = 'Create_course_runs.xlsx';
        $file = new UploadedFile(base_path('tests/Sample_files/'.$file_name),$file_name,null, null, null,true);        
        $response = $this->call('POST','course-run/upload-new',[],[],['file'=> $file], $this->getAuthHeader());
        $this->assertResponseStatus(200);
        $course->forceDelete();
    }
}