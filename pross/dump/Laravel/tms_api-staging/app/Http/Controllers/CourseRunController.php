<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Models\ProgrammeCategory;
use App\Lib\DataVerify\CreateCourseRunDataVerify as DataVerify;
use App\Lib\DataVerify\UpdateCourseRunDataVerify as UpdateDataVerify;
use App\Lib\DataVerify\SummaryDataVerify;
use App\Models\CourseRun;
use App\Models\Course;
use App\Models\Placement;
use DB;
use App\Lib\General;

use App\Lib\DataVerify\FilesHeaderVerify;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory as ExcelReader;

class CourseRunController extends Controller
{   
    /**
     * Fetching the course run base on the filder and sorting
     */
    public function index(Request $request) {

        $course_run = self::getCourseRunList($request);

        if($request->get('export')) {
            
            $data = $course_run->get();
            $environment = app()->environment();

            General::downloadExcel(\Excel::create('Course Run', function($excel) use ($data) {
                $excel->sheet('Course Run DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID');});
                    // $sheet->cell('B1', function($cell) {$cell->setValue('Programme Type');});
                    // $sheet->cell('C1', function($cell) {$cell->setValue('Course Code');});
                    // $sheet->cell('D1', function($cell) {$cell->setValue('Course Title');});
                    //$sheet->cell('D1', function($cell) {$cell->setValue('Duration');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Course Start Date');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Course End Date');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Assessment Start Date');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Assessment End Date');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('No. of Trainees');});
                    // $sheet->cell('J1', function($cell) {$cell->setValue('Number Of Enrolled');});
                    // $sheet->cell('K1', function($cell) {$cell->setValue('Number Of Absantee');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Deconflict');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Remarks');});

                    $sheet->cell('A1:H1', function($cell) {
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->setBorder('A1:H1', 'thin');                    

                    $sheet->freezeFirstRowAndColumn();

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->setColumnFormat([
                            'B:E' => 'dd/mm/yyyy'
                        ]);

                        $sheet->cell('A'.$key, $row->id);
                        $sheet->cell('B'.$key, $row->start_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->start_date)) : '');
                        $sheet->cell('C'.$key, $row->end_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->end_date)) : '');
                        $sheet->cell('D'.$key, $row->assessment_start_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->assessment_start_date)) : '');

                        $sheet->cell('E'.$key, $row->assessment_end_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->assessment_end_date)) : '');

                        $sheet->cell('F'.$key, $row->no_of_trainee);

                        $sheet->cell('G'.$key, $row->should_check_deconflict);
                        $sheet->cell('H'.$key, $row->remarks);

                        $sheet->getStyle('A'.$key.':H'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));

        } else {
            
                $this->data = $course_run->paginate($this->sizePerPage);
                return $this->response();
        }
    }

    /**
     * Getting the course run information by course_id 
     * @param  Request $request [description]
     * @param  [type]  $course_id [description]
     * @return Json
     */
    public function get(Request $request, $course_id)
    {    
        $course_run = CourseRun::select(
            'course_runs.id as id',
            'course_runs.remarks as remarks',
            'pc.prog_category_name',
            'courses.course_code',
            'courses.title',
            'courses.duration_in_days',
            'course_runs.start_date',
            'course_runs.end_date',
            'course_runs.assessment_start_date',
            'course_runs.assessment_end_date',
            'course_runs.class_size',
            'course_runs.no_of_trainees as no_of_trainee',
            'users.name as created_by',
            'course_runs.should_check_deconflict',
            'slots.enrolled',
            'slots.no_of_absantee as no_of_absentees',
            'courses.title as course_title',
            'courses.duration_in_days',
            'courses.programme_category_id',
            'courses.programme_type_id',
            'courses.department_id',
            'courses.assessment_type_id',
            'courses.mandatory',
            'courses.training_location_id',
            'courses.delivery_method_id',
            'courses.cost_per_pax',
            'courses.subsidy',
            'courses.subsidy_value',
            'courses.vendor_email',
            'pc.prog_category_name',
            'py.prog_type_name',
            'dpt.dept_name',
            'loc.location as training_location_name',
            'courses.course_provider as course_provider_name',
            'at.assessment_type_name'
        )
        ->join('courses as courses','courses.course_code','=', 'course_runs.course_code')
        ->join('programme_categories as pc','pc.id','=', 'courses.programme_category_id')
        ->leftJoin('departments as dpt','dpt.id','=', 'courses.programme_category_id')
        ->leftJoin('training_locations as loc','loc.id','=', 'courses.training_location_id')
        //->leftJoin('course_providers as cp','cp.id','=', 'courses.course_provider_id')
        ->leftJoin('assessment_types as at', 'at.id', '=', 'courses.assessment_type_id')
        ->join('programme_types as py','py.id','=', 'courses.programme_type_id')
        ->leftJoin('users as users','users.id','=', 'course_runs.creator_id')
        ->leftJoin(\DB::raw(
            '(SELECT COUNT(id) as enrolled, course_run_id,
            IFNULL(SUM(IF(attendance = "Absent", 1, 0) ),0) as no_of_absantee
            FROM placements
            WHERE deleted_at is NULL GROUP BY course_run_id) as slots'),'slots.course_run_id','=','course_runs.id'
        )
        ->where('course_runs.id', $course_id)->get()->first();

        $this->data = $course_run;
        return $this->response($course_run ? 200: 404);
    }


    /**
     * This function uses to update the event status as deleted
     * @param  Request $request [description]
     * @param  [type]  $course_id [description]
     * @return [type]           [description]
     */ 
    public function delete(Request $request,$course_id)
    {
        $this->message = 'Course Run has been deleted successfully.';
        CourseRun::find($course_id)->delete();
        return $this->response();
    }
    
    /**
     * Handling the Course Create Request.
     */
    public function uploadNew(Request $request) {

        $this->validate($request,[
            'file' => 'required|ext_in:xlsx,xls,ods'
        ],[
           'file.ext_in' => 'System allows only xls, xlsx and ods extension to upload the data.' 
        ]);

        
        //print_r(\Carbon\Carbon::parse(43324.041666667)->format('Y-m-d')); exit
        # Read The Excel file data
        $path = $request->file('file')->getRealPath();

        $data = \App\Lib\ReadExcelFile::getCollection($path,'H');
        //$data = Excel::load($path)->first();
        
        # Verifing the File Header
        $header = $data->first() ? $data->first()->keys()->toArray() : [];
        $verfiy_header =  new FilesHeaderVerify($header);
        $verfiy_header->createCourseRun();
        # End

        $course_codes = $data->pluck('course_code')->unique()->toArray();
        $available_course_code = Course::withTrashed()->whereIn('course_code',$course_codes)->get(['course_code'])->pluck('course_code')->toArray();
        
        $insertData = [];
        $total = 0;
        $inserted = 0;
        
        foreach($data as $key=> $row) {
            
            $course_run_data = $row->toArray();
            
            if(!implode('', $course_run_data)) {
                continue;
            }

            $total++;
            $check =  new DataVerify($course_run_data, $available_course_code);
            $result =  $check->run();
            
            if($result['status']) {
                $inserted++;
                $insertData[] = $result['data']['translate'];

            } else {
                $this->errors[] = [
                    'data' => $result['data']['original'],
                    'errors' => $result['errors'],
                    'row_no' => ($key+1)
                ];
            }
        }

        if(count($insertData)) {

            CourseRun::batchInsertIgnore($insertData);
        }

        $this->data['updated'] = 0;
        $this->data['inserted'] = $inserted;
        $this->data['total'] = $total;
        $this->data['skipped'] = count($this->errors);
        return $this->response();

    }

    /**
     * Handling the request to Update the course run data
     */
    public function uploadExisted(Request $request) {

        $this->validate($request,[
            'file' => 'required|ext_in:xlsx,xls,ods'
        ],[
           'file.ext_in' => 'System allows only xls, xlsx and ods extension to upload the data.' 
        ]);

        # Read The Excel file data
        $path = $request->file('file')->getRealPath();
        //$data = Excel::load($path)->first();
        $data = \App\Lib\ReadExcelFile::getCollection($path,'H');

        # Verifing the File Header
        $header = $data->first() ? $data->first()->keys()->toArray() : [];
        $verfiy_header =  new FilesHeaderVerify($header);
        $verfiy_header->updateCourseRun();
        # End

        $course_run_ids = $data->pluck('course_run_id')->unique()->toArray();
        $course_runs = CourseRun::withTrashed()->whereIn('id',$course_run_ids)->get(['id','current_status'])->groupBy('id')->toArray();

        
        $insertData = [];
        $total = 0;
        $updated = 0;
        //$course_run_data = $data
        foreach($data as $key=> $row) {
            
            $course_run_data = $row->toArray();

            if(!implode('', $course_run_data)) {
                continue;
            }

            $total++;

            $check =  new UpdateDataVerify($course_run_data, $course_runs);
            $result =  $check->run();
            
            if($result['status']) {
                $updated++;
                $insertData[] = $result['data']['translate'];

            } else {
                $this->errors[] = [
                    'data' => $result['data']['original'],
                    'errors' => $result['errors'],
                    'row_no' => ($key+1)
                ];
            }
        }

        if(count($insertData)) {

            CourseRun::batchInsertUpdate($insertData, [
                'start_date',
                'end_date',
                'assessment_start_date',
                'assessment_end_date',
                //'no_of_attendees',
                //'no_of_absentees',
                'no_of_trainees',
                'should_check_deconflict',
                'deleted_at',
                'updated_at',
                'updater_id',
                'remarks'
            ]);
        }

        $this->data['updated'] = $updated;
        $this->data['inserted'] = 0;
        $this->data['total'] = $total;
        $this->data['skipped'] = count($this->errors);
        return $this->response();
    }
    
    /**
     * To Delete the Selected Summary Records
     */
    public function deleteBulkSummary(Request $request) {

        $this->validate($request,[
            'ids' => 'required|array'
        ]);

        CourseRun::whereIn('id', $request->get('ids'))
            ->update([
                'summary_uploaded' => 'No'
            ]);
        
        $this->message = 'Data has been Deleted';
        
        return $this->response();
    }
    /**
     * To Delete the Only single Summary Records
     */
    public function deleteSummary(Request $request, $id) {
        
        $courseRun = CourseRun::findorFail($id);
        $courseRun->where('id', $id)
            ->update([
                'summary_uploaded' => 'No'
            ]);
        
        $this->message = 'Data has been Deleted';        
        return $this->response();
    }

    /**
     * To Delete the Bulk Course
     */
    public function deleteBulkCourse(Request $request) {

        $this->validate($request,[
            'ids' => 'required|array'
        ]);

        CourseRun::whereIn('id', $request->get('ids'))->delete();
        
        $this->message = 'Data has been Deleted';
        
        return $this->response();
    }

    /**
     * Handling the Request to Upload the Course Run Summary data
     */
    public function uploadSummary(Request $request) {

        $this->validate($request,[
            'file' => 'required|ext_in:xlsx,xls,ods'
        ],[
           'file.ext_in' => 'System allows only xls, xlsx and ods extension to upload the data.' 
        ]);

        # Read The Excel file data
        $path = $request->file('file')->getRealPath();
        //$data = Excel::load($path)->first();
        $data = \App\Lib\ReadExcelFile::getCollection($path,'H');
        
        # Verifing the File Header
        $header = $data->first() ? $data->first()->keys()->toArray() : [];
        $verfiy_header =  new FilesHeaderVerify($header);
        $verfiy_header->courseRunSummary();
        # End

        $course_run_ids = $data->pluck('course_run_id')->unique()->toArray();
        $available_course_run = CourseRun::whereIn('id',$course_run_ids)->get(['id', 'summary_uploaded','current_status']);
        
        $available_course_run = $available_course_run->groupBy('id')->toArray();

        //$available_course_run_id_summary = $available_course_run->pluck('summary_uploaded', 'id')->toArray();       
        
        $insertData = [];
        $total = 0;
        $updated = 0;
        $inserted = 0;
        //$course_run_data = $data
        foreach($data as $key=> $row) {
            
            $course_run_data = $row->toArray();
            
            if(!implode('', $course_run_data)) {
                continue;
            }

            $total++;
            $check =  new SummaryDataVerify($course_run_data, $available_course_run);
            $result =  $check->run();
            
            if($result['status']) {
                
                $translate = $result['data']['translate'];

                if($available_course_run[$translate['id']][0]['summary_uploaded'] == 'No'){

                    $inserted++;

                } else {

                    $updated++;
                }


                $insertData[] = $translate;

            } else {
                $this->errors[] = [
                    'data' => $result['data']['original'],
                    'errors' => $result['errors'],
                    'row_no' => ($key+1)
                ];
            }
        }

        if(count($insertData)) {

            CourseRun::batchInsertUpdate($insertData, [
                'summary_uploaded',
                'overall',
                'trainer_delivery',
                'content_relevance',
                'site_visits',
                'facilities',
                'admin',
                'response_rate',
                'updated_at',
                'updater_id',
                'current_status'
            ]);
        }

        $this->data['updated'] = $updated;
        $this->data['inserted'] = $inserted;
        $this->data['total'] = $total;
        $this->data['skipped'] = count($this->errors);
        return $this->response();
    }

    /**
     * This function uses to fetch the course run list
     * @param  Request $request [description]
     * @return list
     */ 
    private function getCourseRunList(Request $request){
        $course_run = CourseRun::select(
            'course_runs.id as id',
            'course_runs.remarks',
            'pc.prog_category_name',
            'py.prog_type_name',
            'courses.course_code',
            'courses.title',
            'courses.duration_in_days',
            'course_runs.start_date',
            'course_runs.end_date',
            'course_runs.assessment_start_date',
            'course_runs.assessment_end_date',
            'course_runs.current_status',
            'course_runs.class_size',
            'users.name as created_by',
            'py.prog_type_name',
            'course_runs.no_of_trainees as no_of_trainee',
            'slots.no_of_presentee as no_of_attendee',
            'course_runs.overall',
            'course_runs.trainer_delivery',
            'course_runs.content_relevance',
            'course_runs.site_visits',
            'course_runs.facilities',
            'course_runs.admin',
            'course_runs.response_rate',
            'course_runs.should_check_deconflict',
            'slots.no_of_confirmed as enrolled',
            'slots.no_of_failure',
            'slots.no_of_penalties',
            'u1.name as supervisor_name',
            'slots.no_of_presentee',
            'slots.no_of_confirmed',
            'slots.no_of_absantee',
            DB::raw("DATE_FORMAT(course_runs.start_date, '%b %Y') as month_year"),
            DB::raw("DATE_FORMAT(course_runs.start_date, '%d/%m/%Y') as formatted_start_date"),
            DB::raw("DATE_FORMAT(course_runs.end_date, '%d/%m/%Y') as formatted_end_date"),
            DB::raw("DATE_FORMAT(course_runs.assessment_start_date, '%d/%m/%Y') as formatted_assessment_start_date"),
            DB::raw("DATE_FORMAT(course_runs.assessment_end_date, '%d/%m/%Y') as formatted_assessment_end_date")
        )
        ->join('courses as courses','courses.course_code','=', 'course_runs.course_code')
        ->join('programme_categories as pc','pc.id','=', 'courses.programme_category_id')
        ->join('programme_types as py','py.id','=', 'courses.programme_type_id')
        ->leftJoin('users as users','users.id','=', 'course_runs.creator_id')
        ->leftJoin('users as u1', 'users.supervisor_personnel_number','=','u1.personnel_number')
        ->leftJoin(\DB::raw(
            '(SELECT COUNT(id) as enrolled, course_run_id, id,
            SUM(IF(assessment_results = "Fail", 1, 0)) as no_of_failure,
            SUM(IF(absent_reason_id = 8, 1,0)) as no_of_penalties,
            IFNULL(SUM(IF(attendance = "Absent", 1, 0) ),0) as no_of_absantee,
            IFNULL(SUM(IF(current_status = "Confirmed", 1, 0) ),0) as no_of_confirmed,
            IFNULL(SUM(IF(attendance = "Present", 1, 0) ),0) as no_of_presentee
            FROM placements
            where deleted_at is null 
            GROUP BY course_run_id) as slots'),'slots.course_run_id','=','course_runs.id'
        );

        //SUM(IF(attendance ="Absent", 1,0)) as no_of_absantee
        /** start custom filters **/
        if($request->has('customFilters')) {
            $customFilters  = $request->customFilters;

            $general = new General();

            $keys = [
                'prog_category_name' => 'pc.id',
                'remarks' => 'course_runs.remarks',
                'prog_type_name' => 'py.id',
                'id' => 'course_runs.id',
                'course_code' => 'courses.course_code',
                'course_title' =>   'courses.title',
                'title' =>   'courses.title',
                'duration_in_days' => 'courses.duration_in_days',
                'start_date' => 'course_runs.start_date',
                'end_date' => 'course_runs.end_date',
                'assessment_start_date' => 'course_runs.assessment_start_date',
                'assessment_end_date' => 'course_runs.assessment_end_date',
                //'class_size' => 'course_runs.class_size',
                'created_by' => 'users.id',
                'no_of_trainee' =>'course_runs.no_of_trainees',
                'summary_uploaded'=>'course_runs.summary_uploaded',  
                'date_range' => ['start_date','end_date'],
                'test_date_range' => ['assessment_start_date','assessment_end_date'],
                'enrolled' => 'slots.enrolled',
                'current_status'=>'course_runs.current_status',
                'admin'=>'course_runs.admin',
                'trainer_delivery'=>'course_runs.trainer_delivery',
                'content_relevance'=>'course_runs.content_relevance',
                'site_visits'=>'course_runs.site_visits',
                'no_of_failure'=> 'slots.no_of_failure',
                'facilities'=>'course_runs.facilities',
                'no_of_absantee' => 'slots.no_of_absantee'            
            ];

            // $t = $request->get('customFilters');

            // if(isset($t['no_of_trainee'])){

            //     \Log::info($t);
            // }

            $general->applyFilters($course_run, $request->customFilters, $keys);
        }
        
        /** end custom filters **/

        /** if selected record **/
        if($request->has('selected') && $request->get('export')):
            $course_run->whereIn('course_runs.id', $request->get('selected'));
        endif;
        /** selected record **/

        /** start sorting **/
        if($request->has('sortName')):
            switch ($request->sortName) {
                case 'prog_category_name':
                    $course_run->orderBy('pc.prog_category_name',$request->sortOrder); 
                    break;
                case 'id':
                    $course_run->orderBy('course_runs.id',$request->sortOrder); 
                    break;    
                case 'course_code':
                    $course_run->orderBy('courses.course_code',$request->sortOrder);
                    break; 
                case 'course_title':
                    $course_run->orderBy('courses.title',$request->sortOrder)->orderBy('course_runs.id','DESC');
                    break; 
                case 'title':
                    $course_run->orderBy('courses.title',$request->sortOrder)->orderBy('course_runs.id','DESC');
                    break;     
                case 'duration':
                    $course_run->orderBy('courses.duration_in_days',$request->sortOrder);   
                    break;
                case 'date_range':
                    $course_run->orderBy('course_runs.start_date',$request->sortOrder); 
                    break;
                case 'test_date_range':
                    $course_run->orderBy('course_runs.assessment_start_date',$request->sortOrder);   
                    break;
                // case 'class_size':
                //     $course_run->orderBy('course_runs.class_size',$request->sortOrder); 
                //     break;
                case 'created_by':
                    $course_run->orderBy('users.name', $request->sortOrder); 
                    break;
                case 'enrolled':
                    $course_run->orderBy('slots.enrolled', $request->sortOrder); 
                    break;
                case 'no_of_trainee':
                    $course_run->orderBy('course_runs.no_of_trainees', $request->sortOrder); 
                    break;
                case 'prog_type_name':
                    $course_run->orderBy('py.prog_type_name', $request->sortOrder); 
                    break;
                case 'trainer_delivery':
                    $course_run->orderBy('course_runs.trainer_delivery', $request->sortOrder); 
                    break;
                case 'content_relevance':
                    $course_run->orderBy('course_runs.content_relevance', $request->sortOrder); 
                    break;
                case 'site_visits':
                    $course_run->orderBy('course_runs.site_visits', $request->sortOrder); 
                    break;
                case 'facilities':
                    $course_run->orderBy('course_runs.facilities', $request->sortOrder); 
                    break;
                case 'admin':
                    $course_run->orderBy('course_runs.admin', $request->sortOrder); 
                    break;
                case 'current_status':
                    $course_run->orderBy(\DB::raw('CAST(course_runs.current_status AS CHAR)'),$request->sortOrder);
                    break;
                case 'no_of_failure':
                    $course_run->orderBy('slots.no_of_failure',$request->sortOrder);
                    break;                
                    //
                case 'no_of_absantee':
                    $course_run->orderBy('slots.no_of_absantee', $request->sortOrder); 
                    break;
                default:
                    # code...
                    break;
            }
        endif;
        /** end sorting **/

        return $course_run->groupBy('course_runs.id');
    }
    

    /**
     * This function uses to export the report of course run
     * @param  Request $request [description]
     */ 
    public function getReport(Request $request){
        $course_run = self::getCourseRunList($request);

        if($request->get('export')) {
            $data = $course_run->get();
            General::downloadExcel(Excel::create('Report - Course Run', function($excel) use ($data) {
                $excel->sheet('Report - Course Run DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID')->setValignment('center');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Course Code')->setValignment('center');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Course Name')->setValignment('center');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Programme Category')->setValignment('center');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Programme Type')->setValignment('center');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('Start Date')->setValignment('center');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('End Date')->setValignment('center');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Month/Year')->setValignment('center');});
                    $sheet->cell('I1', function($cell) {$cell->setValue('Training Duration (Days)')->setValignment('center');});
                    $sheet->cell('J1', function($cell) {$cell->setValue('Max Class Size')->setValignment('center');});
                    $sheet->cell('K1', function($cell) {$cell->setValue('No of Confirmed')->setValignment('center');});
                    $sheet->cell('L1', function($cell) {$cell->setValue('No. of Attendee(s)')->setValignment('center');});
                    $sheet->cell('M1', function($cell) {$cell->setValue('No. of Absentee(s)')->setValignment('center');});
                    $sheet->cell('N1', function($cell) {$cell->setValue('No. of Penalty(ies)')->setValignment('center');});
                    $sheet->cell('O1', function($cell) {$cell->setValue('No. of Failure(s)')->setValignment('center');});
                    $sheet->cell('P1', function($cell) {$cell->setValue('Overall Average (%)')->setValignment('center');});
                    $sheet->cell('Q1', function($cell) {$cell->setValue("Trainer's Delivery (%)")->setValignment('center');});
                    $sheet->cell('R1', function($cell) {$cell->setValue('Content Relevance (%)')->setValignment('center');});
                    $sheet->cell('S1', function($cell) {$cell->setValue('Site Visits (%)')->setValignment('center');});
                    $sheet->cell('T1', function($cell) {$cell->setValue('Facilities (%)')->setValignment('center');});
                    $sheet->cell('U1', function($cell) {$cell->setValue('Admin (%)')->setValignment('center');});
                    $sheet->cell('V1', function($cell) {$cell->setValue('Response Rate (%)')->setValignment('center');});

                    foreach(['A1','B1','C1','D1','E1','F1','G1','H1','I1','J1','K1','L1','M1','N1','O1','P1','Q1','R1','S1','T1', 'U1','V1'] as $c ) {

                        $sheet->setSize($c, ($c =='C1' ? 50: 15), 35);
                    }
                    

                    $sheet->cell('A1:U1', function($cell) {
                           $cell->setFontWeight('bold');
                    });

                    $sheet->cell('A1', function($cell) {
                           $cell->setBackground('#00b0f0');
                    });

                    $sheet->cell('B1:M1', function($cell) {
                           $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->cell('N1:V1', function($cell) {
                           $cell->setBackground('#00b0f0');
                    });

                    // $sheet->setColumnFormat(array(
                    //     'J:N' => '00'
                    //  ));

                    $sheet->setBorder('A1:V1', 'thin');

                    $sheet->setFreeze('D1');
                    $sheet->setColumnFormat(array(
                        'F:G' => 'dd/mm/yyyy'
                    ));

                    $sheet->setColumnFormat(array(
                        'O:U' => '0.00%'
                    ));

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->id);                 

                        $sheet->cell('B'.$key, $row->course_code);
                        $sheet->cell('C'.$key, $row->title);
                        $sheet->cell('D'.$key, $row->prog_category_name);
                        $sheet->cell('E'.$key, $row->prog_type_name);

                        $sheet->cell('F'.$key, $row->formatted_start_date);
                        $sheet->cell('G'.$key, $row->formatted_end_date);

                        $sheet->cell('H'.$key, $row->month_year);
                        $sheet->setCellValue('I'.$key, $row->duration_in_days);
                        $sheet->setCellValue('J'.$key, $row->no_of_trainee ? $row->no_of_trainee : 0);
                        $sheet->setCellValue('K'.$key, $row->no_of_confirmed ? $row->no_of_confirmed : 0);
                        $sheet->setCellValue('L'.$key, $row->no_of_presentee ? $row->no_of_presentee : 0);
                        $sheet->setCellValue('M'.$key, $row->no_of_absantee ? $row->no_of_absantee : 0);

                        if( $row->no_of_absantee > 0 ) {
                            
                            $sheet->cell('M'.$key, function($cell) {
                                $cell->setBackground('#ffc7ce');
                            });
                        }
                        //
                        $sheet->setCellValue('N'.$key, $row->no_of_penalties ? $row->no_of_penalties : 0); // No of Penalty(s)

                        if( $row->no_of_penalties > 0 ){

                            $sheet->cell('N'.$key, function($cell) {
                                $cell->setBackground('#ffc7ce');
                            });
                        }

                        $sheet->setCellValue('O'.$key, $row->no_of_failure ? $row->no_of_failure : 0); // No of Failure(s)

                        if( $row->no_of_failure > 0 ){
                            $sheet->cell('O'.$key, function($cell) {
                                $cell->setBackground('#ffc7ce');
                            });
                        }

                        $sheet->cell('P'.$key, $row->overall ? $row->overall/100 : '');
                        $sheet->cell('Q'.$key, $row->trainer_delivery ? $row->trainer_delivery/100 : '');
                        $sheet->cell('R'.$key, $row->content_relevance ? $row->content_relevance/100 : '');
                        $sheet->cell('S'.$key, $row->site_visits ? $row->site_visits/100 : '');
                        $sheet->cell('T'.$key, $row->facilities ? $row->facilities/100 : '');
                        $sheet->cell('U'.$key, $row->admin ? $row->admin/100 : '');
                        $sheet->cell('V'.$key, $row->response_rate ? $row->response_rate/100 : '');

                        $sheet->getStyle('A'.$key.':V'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));

        } else {

            $this->data = $course_run->paginate($this->sizePerPage);
            return $this->response();
        }
    }


    /** Viewer Section **/
    /**
     * This function uses to export the report for viewer
     * @param  Request $request [description]
     */ 
    public function getActiveCourse(Request $request){
        $course_run = self::getCourseRunList($request);
        /** get active course**/
        $course_run->where('course_runs.current_status', 'Confirmed');
        $course_run->where(function($q) {
            $q->orWhere('start_date' ,'>=', date('Y-m-d'));
            $q->orWhere('end_date' ,'>=', date('Y-m-d'));
        });

        if($request->get('export')) {
            $data = $course_run->get();

            General::downloadExcel(\Excel::create('Course Run', function($excel) use ($data) {
                $excel->sheet('Course Run DATA', function($sheet) use ($data)
                {
                    
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Programme Type');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Course Code');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Course Title');});
                    //$sheet->cell('D1', function($cell) {$cell->setValue('Duration');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Start Date');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('End Date');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Assessment Start Date');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Assessment End Date');});
                    $sheet->cell('I1', function($cell) {$cell->setValue('Number of Trainee');});
                    $sheet->cell('J1', function($cell) {$cell->setValue('Number Of Enrolled');});
                    $sheet->cell('K1', function($cell) {$cell->setValue('Number Of Absantee');});
                    $sheet->cell('L1', function($cell) {$cell->setValue('Exclude From Deconflict');});

                    $sheet->cell('A1:L1', function($cell) {
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->setBorder('A1:L1', 'thin');

                    $sheet->freezeFirstRowAndColumn();
                    $sheet->setColumnFormat(array(
                        'E:H' => 'dd/mm/yyyy'
                    ));

                    foreach ($data as $index => $row) {

                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->id);
                        $sheet->cell('B'.$key, $row->prog_type_name);
                        $sheet->cell('C'.$key, $row->course_code);
                        $sheet->cell('D'.$key, $row->title);
                        //$sheet->cell('E'.$key, $row->duration_in_days);
                        $sheet->cell('E'.$key, $row->start_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->start_date)) : '');
                        $sheet->cell('F'.$key, $row->end_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->end_date)) : '');
                        $sheet->cell('G'.$key, $row->assessment_start_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->assessment_start_date)) : '');

                        $sheet->cell('H'.$key, $row->assessment_end_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->assessment_end_date)) : '');

                        $sheet->cell('I'.$key, $row->no_of_trainee);
                        $sheet->cell('J'.$key, $row->enrolled);
                        $sheet->cell('K'.$key, $row->no_of_absantee);                       

                        
                        $sheet->cell('L'.$key, $row->created_by);                             
                        $sheet->getStyle('A'.$key.':L'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));
            
        } else{
            $this->data = $course_run->paginate($this->sizePerPage);
            return $this->response();
        }
    }

    /**
     * To Change the status of Course run
     */
    public function changeStatus(Request $request, $course_run_id) {

        $this->validate($request, [
            'status' => 'required|in:Draft,Confirmed,Completed,Closed'
        ]);

        $course_run  = CourseRun::findorFail($course_run_id);
        $end_date = Carbon::parse($course_run->end_date);
        $test_end_date = Carbon::parse($course_run->assessment_end_date);
        
        $status = $request->get('status');
        
        if($status  == 'Completed' && $course_run->current_status != 'Confirmed') {

            $this->message = 'Course run status can be Completed if the current status is Confirmed';
            $this->errors = ['status' => [$this->message]];
            $this->data = $course_run;
            return $this->response(422);

        }else if($status  == 'Completed' && $course_run->current_status == 'Confirmed') {
            
            if($end_date->isFuture()){

                $this->message = 'Course run status can be Completed if the end date is not in future.';
                $this->errors = ['status' => [$this->message]];
                $this->data = $course_run;
                return $this->response(422);

            }else if($test_end_date->isFuture()) {

                $this->message = 'Course run status can be Completed if the assessment end date is not in future.';
                $this->errors = ['status' => [$this->message]];
                $this->data = $course_run;
                return $this->response(422);
            }
        }
        
        $course_run->update(['current_status' => $status]);
        $this->message = 'Course Status has been updated.';
        $this->data = $course_run;
        return $this->response();
    }

    /**
     * This function uses to export the edit course run data
     * @param  Request $request [description]
     */ 
    public function editStatus(Request $request){
        $course_run = self::getCourseRunList($request);

        if($request->get('export')) {
            $data = $course_run->get();

            General::downloadExcel(Excel::create('Edit Course Run', function($excel) use ($data) {
                $excel->sheet('Edit Course Run DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Programme Type');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Course Code');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Course Title');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Duration (No Of Days)');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('Course Start Date');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Course End Date');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Assessment Start Date');});
                    $sheet->cell('I1', function($cell) {$cell->setValue('Assessment End Date');});
                    $sheet->cell('J1', function($cell) {$cell->setValue('Number Of Trainee(s)');});
                    $sheet->cell('K1', function($cell) {$cell->setValue('Number of Enrolled');});                    
                    $sheet->cell('L1', function($cell) {$cell->setValue('Created By');});
                    $sheet->cell('M1', function($cell) {$cell->setValue('Status');});

                    $sheet->cell('A1:M1', function($cell) {
                           $cell->setFontWeight('bold');
                           $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->setColumnFormat([
                        'F:I' => 'dd/mm/yyyy'
                    ]);

                    $sheet->setBorder('A1:M1', 'thin');

                    $sheet->freezeFirstRowAndColumn();

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->id);
                        $sheet->cell('B'.$key, $row->prog_type_name);
                        $sheet->cell('C'.$key, $row->course_code);
                        $sheet->cell('D'.$key, $row->title);
                        $sheet->cell('E'.$key, $row->duration_in_days);

                        $sheet->cell('F'.$key, $row->start_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->start_date)) : '');
                        $sheet->cell('G'.$key, $row->end_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->end_date)) : '');
                        $sheet->cell('H'.$key, $row->assessment_start_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->assessment_start_date)) : '');

                        $sheet->cell('I'.$key, $row->assessment_end_date ? \PHPExcel_Shared_Date::PHPToExcel(strtotime($row->assessment_end_date)) : '');

                        $sheet->cell('J'.$key, $row->class_size);
                        $sheet->cell('K'.$key, $row->enrolled);                        
                        $sheet->cell('L'.$key, $row->created_by); 
                        $sheet->cell('M'.$key, $row->current_status);

                        $sheet->getStyle('A'.$key.':M'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));

        } else {
            $this->data = $course_run->paginate($this->sizePerPage);
            return $this->response();
        }

    }

     /**
     * This function uses to fetch and export post course run
     * @param  Request $request [description]
     */ 
    public function postSummary(Request $request){
        $course_run = self::getCourseRunList($request);

        if($request->get('export')) {
            $data = $course_run->get();

            General::downloadExcel(\Excel::create('Post Course Summary', function($excel) use ($data) {
                $excel->sheet('Post Course Summary DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID');});
                    //$sheet->cell('B1', function($cell) {$cell->setValue('Course Title');});
                    $sheet->cell('B1', function($cell) {$cell->setValue("Overall (%)");});
                    $sheet->cell('C1', function($cell) {$cell->setValue("Trainer's Delivery (%)");});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Content Relevance (%)');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Site Visits (%)');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('Facilities (%)');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Admin (%)');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Response Rate (%)');});
                    // $sheet->cell('J1', function($cell) {$cell->setValue('Status');});
                    // $sheet->cell('K1', function($cell) {$cell->setValue('Created By');});

                    $sheet->cell('A1:H1', function($cell) {
                           $cell->setFontWeight('bold');
                           $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->setBorder('A1:H1', 'thin');

                    $sheet->freezeFirstRowAndColumn();

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->id);
                        $sheet->cell('B'.$key, $row->overall ? $row->overall : 0);
                        $sheet->cell('C'.$key, $row->trainer_delivery ? $row->trainer_delivery : 0);
                        $sheet->cell('D'.$key, $row->content_relevance ? $row->content_relevance : 0);
                        $sheet->cell('E'.$key, $row->site_visits ? $row->site_visits : 0);
                        $sheet->cell('F'.$key, $row->facilities ? $row->facilities : 0);
                        $sheet->cell('G'.$key, $row->admin ? $row->admin : 0);
                        $sheet->cell('H'.$key, $row->response_rate ? $row->response_rate : 0);
                        // $sheet->cell('J'.$key, $row->current_status);
                        // $sheet->cell('K'.$key, $row->created_by); 

                        $sheet->getStyle('A'.$key.':H'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));

        } else {
            $this->data = $course_run->paginate($this->sizePerPage);
            return $this->response();
        }

    }

    public function changeDeconflictStatus(Request $request, $course_run_id) {

        $this->validate($request,[
            'status'=>'required|in:Yes,No'
        ]);

        $course_run = CourseRun::findorFail($course_run_id);
        
        $course_run->update(['should_check_deconflict'=> $request->get('status')]);

        $this->message = 'Status has been Changed.';
        return $this->response();
    }
}