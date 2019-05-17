<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use DownloadSample\CourseSample;
use DownloadSample\CourseRunSample;
use DownloadSample\CourseRunSummarySample;
use DownloadSample\CourseRunUpdateSample;
use DownloadSample\PlacementSample;
use DownloadSample\PlacementResultSample;
use DownloadSample\SupervisorSample;

$router->get('/test-email', function () use ($router) {
    
    return Mail::send('emails.default',['body'=> 'testEmail'], function($message) {
        $message->to('hitesh@singsys.com');
    });
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});


/**
 * To Allow the cross origin request.
 */
$router->options(
    '/{any:.*}', 
    [
        'middleware' => ['cros'], 
        function (){ 
            return response(['status' => 'success']); 
        }
    ]
);

$router->group(['middleware' => ['cros']], function () use ($router) {
    $router->post('/login-action','AuthController@loginAction');
    $router->get('/logout','ProfileController@logout');
});

$router->group(['middleware' => ['cros','auth:api']], function () use ($router) {

    $router->get('/me','ProfileController@myProfile');
    $router->get('/get-list','CourseController@getList');
    $router->post('/get-email-template', 'PlacementController@emailTemplate');
    $router->post('/send-email-and-change-status', 'PlacementController@sendEmail');

    /**
     * Email Template Routes
    */
    $router->get('email-template/{id}','EmailController@get');
    $router->put('email-template/{id}','EmailController@update');
    
    /**
     * Course Route
     */
    $router->group(['prefix'=>'course'], function ()  use ($router) {
        
        $router->get('/', 'CourseController@index');
        $router->post('upload', 'CourseController@upload'); 
        $router->get('/{id}', 'CourseController@get');  
        $router->delete('/{id}','CourseController@delete');
        $router->delete('/', 'CourseController@deleteBulkCourse');     
    });

    $router->group(['prefix'=>'course-run'], function ()  use ($router) {
        
        $router->get('/', 'CourseRunController@index');
        $router->get('/edit-status-list', 'CourseRunController@editStatus');
        $router->get('/post-summary-list', 'CourseRunController@postSummary');
        $router->get('/report-list', 'CourseRunController@getReport');
        $router->post('/upload-new', 'CourseRunController@uploadNew');
        $router->post('/upload-existed', 'CourseRunController@uploadExisted');       
        $router->post('/upload-summary', 'CourseRunController@uploadSummary');
        $router->delete('/summary', 'CourseRunController@deleteBulkSummary');
        $router->delete('/summary/{id}', 'CourseRunController@deleteSummary');
        $router->get('/{id}', 'CourseRunController@get');  
        $router->delete('/{id}','CourseRunController@delete');
        $router->put('/change-status/{id}','CourseRunController@changeStatus');
        $router->put('/change-de-conflict-status/{id}','CourseRunController@changeDeconflictStatus');
        $router->delete('/', 'CourseRunController@deleteBulkCourse');    
          
    });

    $router->group(['prefix'=>'placement'], function ()  use ($router) {

        $router->delete('/','PlacementController@delete');
        $router->get('/report-list', 'PlacementController@index');
        $router->post('/update-status/{id}', 'PlacementController@updateStatus');
        $router->post('/upload', 'PlacementController@uploadPlacement');
        $router->post('/upload-result', 'PlacementController@uploadPlacementResult');
        $router->post('/check-conflict','PlacementController@checkConflict');
        $router->put('/make-status-confirmed','PlacementController@changeStatusToConfirm');        
        $router->delete('/result','PlacementController@deleteResult');
        
        $router->get('/post-course-list', 'PlacementController@postCourse');
        $router->get('/maintain-list', 'PlacementController@maintainList');
        $router->get('/maintain-list/{course_run_id}', 'PlacementController@maintainListOfCourseRun');

    });

    $router->group(['prefix'=>'user'], function ()  use ($router) {

        $router->get('/', 'UserController@index');
        $router->put('/change-role/{id}', 'UserController@changeRole');
        $router->post('/upload', 'UserController@upload');   
        // $router->delete('/{id}','UserController@delete');
        //$router->delete('/bulk/{id}', 'UserController@deleteBulkUser');         
    });

    $router->group(['prefix'=>'viewer'], function ()  use ($router) {
        $router->get('/course', 'CourseController@index');
        $router->get('/course-run', 'CourseRunController@getActiveCourse');
        $router->get('/placement', 'PlacementController@getUserPlacement');
        $router->get('/subordinate-placement', 'PlacementController@getSubordinatePlacement');         
    });
     
});

$router->get('phpinfo', function () {
    return phpinfo();
});

$router->group(['prefix'=>'download-sample'], function ()  use ($router) {

        $router->get('/', function () {
            
            return view('download-sample');
        });

        $router->get('/course', function () {
            $c = new CourseSample();
            $c->download();
        });

        $router->get('/course-run', function () {
            $c = new CourseRunSample();
            $c->download();
        });

        $router->get('/course-run-summary', function () {
            $c = new CourseRunSummarySample();
            $c->download();
        });

        $router->get('/course-run-update', function () {
            $c = new CourseRunUpdateSample();
            $c->download();
        });

        $router->get('/placement', function () {
            $c = new PlacementSample();
            $c->download();
        });

        $router->get('/placement-result', function () {
            $c = new PlacementResultSample();
            $c->download();
        });

        $router->get('/supervisor', function () {
            $c = new SupervisorSample();
            $c->download();
        });

        $router->get('/course-run-confirm', function () {
            return Artisan::call('course_run:confirm');
        });

        $router->get('/placement-confirm', function () {

            return Artisan::call('placement:confirm');
        });

        $router->get('create-demo-user', function () {
            $i = 0;
            while($i < 10000) {

                factory(\App\User::class)->create();
                $i++;
            }
            
            Artisan::call('cache:clear');
        });
});

$router->get('cache-clear', function () {
    
    return Artisan::call('cache:clear');
});


//


$router->get('migrate', function () {
    return Artisan::call('migrate');
});

