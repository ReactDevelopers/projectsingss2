<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Models\Placement;
use App\Models\CourseRun;
use App\Lib\DataVerify\PlacementDataVerify;
use App\Lib\DataVerify\PlacementResultDataVerify;
use App\Models\Course;
use App\Lib\General;
use DB;
use App\Lib\DataVerify\FilesHeaderVerify;

class PlacementController extends Controller
{ 
     /**
     * Fetching the placement based on the filder and sorting
     */
    public function index(Request $request) {

        $placements = self::getPlacementList($request);

        if($request->get('export')) {

            $data = $placements->where('placements.result_uploaded','Yes')->get();

            General::downloadExcel(Excel::create('Placement', function($excel) use ($data) {
                $excel->sheet('Placement DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID')->setValignment('center');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Course Code')->setValignment('center');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Course Title')->setValignment('center');});                    
                    $sheet->cell('D1', function($cell) {$cell->setValue('PER ID')->setValignment('center');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Participants')->setValignment('center');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('Department')->setValignment('center');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Division')->setValignment('center');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Branch')->setValignment('center');});
                    $sheet->cell('I1', function($cell) {$cell->setValue('Start Date')->setValignment('center');});
                    $sheet->cell('J1', function($cell) {$cell->setValue('End Date')->setValignment('center');});
                    $sheet->cell('K1', function($cell) {$cell->setValue('Attendance')->setValignment('center');});
                    $sheet->cell('L1', function($cell) {$cell->setValue('Result')->setValignment('center');});
                    $sheet->cell('M1', function($cell) {$cell->setValue('Absent Reason')->setValignment('center');});
                    $sheet->cell('N1', function($cell) {$cell->setValue('Failure Reason')->setValignment('center');});

                   

                    foreach(['A1','B1','C1','D1','E1','F1','G1','H1','I1','J1','K1','L1','M1','N1'] as $c ) {

                        $sheet->setSize($c, ($c =='C1' ? 50: 15), 35);
                    }

                    $sheet->cell('A1:B1', function($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#00b0f0');
                    });

                    $sheet->cell('C1', function($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->cell('D1', function($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#00b0f0');
                    });
                    $sheet->cell('E1:H1', function($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#ffffcc'); 
                    });
                    $sheet->cell('I1:N1', function($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#00b0f0');
                    });

                    $sheet->setBorder('A1:N1', 'thin');


                    $sheet->setFreeze('D1');

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->course_run_id);
                        $sheet->cell('B'.$key, $row->course_code);
                        $sheet->cell('C'.$key, $row->title);                        
                        $sheet->cell('D'.$key, $row->personnel_number);
                        $sheet->cell('E'.$key, $row->percipient_name);
                        $sheet->cell('F'.$key, $row->user_dept_name);
                        $sheet->cell('G'.$key, $row->division);
                        $sheet->cell('H'.$key, $row->branch);
                        $sheet->cell('I'.$key, $row->formatted_start_date);
                        $sheet->cell('J'.$key, $row->formatted_end_date);
                        $sheet->cell('K'.$key, $row->attendance);
                        $sheet->cell('L'.$key, $row->assessment_results);
                        $sheet->cell('M'.$key, $row->absent_reason); 
                        $sheet->cell('N'.$key, $row->failure_reason); 

                        $sheet->getStyle('A'.$key.':N'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

                    }

                    $sheet->setAutoFilter();
                });

            }));

        } else {

            $this->data = $placements->paginate($this->sizePerPage);
            return $this->response();
        }
    }

    
    /**
     * Handling the request to upload the placement data
     */
    public function uploadPlacement(Request $request) {
        $this->validate($request,[
            'file' => 'required|ext_in:xlsx,xls,ods'
        ],[
           'file.ext_in' => 'System allows only xls, xlsx and ods extension to upload the data.' 
        ]);

        # Read The Excel file data
        $path = $request->file('file')->getRealPath();
        //$data = Excel::load($path)->first();
        $data = \App\Lib\ReadExcelFile::getCollection($path,'B');

        # Verifing the File Header
        $header = $data->first() ? $data->first()->keys()->toArray() : [];
        $verfiy_header =  new FilesHeaderVerify($header);
        $verfiy_header->placement();
        # End
        
        if(!$request->has('forceUpload')){          

            $duplicate_data = $data->map(function ($v) {
                $v['course_run_id_per_id'] = $v['course_run_id'].$v['per_id'];
                return $v;
            })->groupBy('course_run_id_per_id')
            ->filter(function ($v1) {
                return $v1->count() > 1 ? true : false;
            });

            # Has duplicate records
            if($duplicate_data->count()){

                $this->error_code = 'duplicate';
                $this->data = $duplicate_data;
                $this->status = false;

                return $this->response(500);
            }
        }


        //print_r($duplicate_data); exit;

        $course_run_ids = $data->pluck('course_run_id')->unique()->toArray();
        $per_ids  = $data->pluck('per_id')->unique()->toArray();
        
        $available_course_run = CourseRun::whereIn('id',$course_run_ids)->get(['id', 'summary_uploaded','current_status']);

        //$available_course_run_ids = $available_course_run->pluck('id')->toArray();

        $exists_users = \App\User::whereIn('personnel_number', $per_ids)->get(['personnel_number','id']);
        $exists_per_ids = $exists_users->pluck('personnel_number')->toArray();
        
        $course_run_data  = $available_course_run->groupBy('id')->toArray();
        $skippedData = $request->has('skippedData') ? $request->get('skippedData'): [];
        $insertData = [];
        $total = 0;
        //$course_run_data = $data
        foreach($data as $key=> $row) {
            
            $placement_data = $row->toArray();
            
            if(!implode('', $placement_data)) {
                continue;
            }

            $total++;
            $check =  new PlacementDataVerify($placement_data, $course_run_data, $exists_per_ids, $skippedData);
            $result =  $check->run();
            
            if($result['status']) {
                
                $translate = $result['data']['translate'];
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

            $response = Placement::batchInsertUpdate($insertData, [
                'deleted_at',
                'updated_at',
                'updater_id',
                'current_status'
            ]);
        }

        $this->data['updated'] = isset($response) ? $response['updated'] : 0;
        $this->data['inserted'] = isset($response) ? $response['inserted'] : 0;
        $this->data['total'] = $total;
        $this->data['skipped'] = count($this->errors);
        return $this->response();
    }

    /**
     * Handling the request to upload the placement data
     */
    public function uploadPlacementResult(Request $request) {

        $this->validate($request,[
            'file' => 'required|ext_in:xlsx,xls,ods'
        ],[
           'file.ext_in' => 'System allows only xls, xlsx and ods extension to upload the data.' 
        ]);

        # Read The Excel file data
        $path = $request->file('file')->getRealPath();
        //$data = Excel::load($path)->first();
        $data = \App\Lib\ReadExcelFile::getCollection($path,'G');

        # Verifing the File Header
        $header = $data->first() ? $data->first()->keys()->toArray() : [];
        $verfiy_header =  new FilesHeaderVerify($header);
        $verfiy_header->placementResult();
        # End
        
        // $course_run_id_per_ids = $data->map(function($v) {

        //     $v['course_run_id_per_id'] = $v['course_run_id'].'-'.$v['per_id'];
        //     return $v;

        // })->pluck('course_run_id_per_id')->unique()->toArray();

        $course_run_ids = $data->pluck('course_run_id')->unique()->toArray();
        $per_ids  = $data->pluck('per_id')->unique()->toArray();
        
        $placements = Placement::whereIn('personnel_number', $per_ids)
            ->whereIn('course_run_id', $course_run_ids)
            ->get(['result_uploaded','current_status','course_run_id','personnel_number']);  

        $placements = $placements->map(function($v) {

            $v->course_run_id_per_id = $v->course_run_id.'-'.$v->personnel_number;
            return $v;

        })->groupBy('course_run_id_per_id')->toArray(); 

        
        $insertData = [];
        $total = 0;
        $updated = 0;
        $inserted = 0;
        //$course_run_data = $data
        foreach($data as $key=> $row) {
            
            $placement_data = $row->toArray();

            if(!implode('', $placement_data)) {
                continue;
            }

            $total++;
            $check =  new PlacementResultDataVerify($placement_data, $placements);
            $result =  $check->run();
            
            if($result['status']) {

                $translate = $result['data']['translate'];
                $original = $result['data']['original'];

                $course_run_id_per_id = $original['course_run_id'].'-'.$original['per_id'];

                $single_pr = $placements[$course_run_id_per_id][0];

                if($single_pr['result_uploaded'] == 'No'){
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

            Placement::batchInsertUpdate($insertData, [
                'attendance',
                'assessment_results',
                'result_uploaded',
                'absent_reason_id',
                'failure_reason_id',
                'action',
                'deleted_at',
                'updated_at',
                'updater_id'
            ]);
        }

        $this->data['updated'] = $updated;
        $this->data['inserted'] = $inserted;
        $this->data['total'] = $total;
        $this->data['skipped'] = count($this->errors);
        return $this->response();
    }

    /**
     * This function uses to update the status only ['Draft,Cancelled'] of Placement
     * @param  Request $request [description]
     * @return JSON
     */ 
    public function updateStatus(Request $request,$placement_id){

        $this->validate($request,[
            'status'=>'required|in:Draft,Cancelled'
        ]);
                
        $this->message = 'Placement status has been Updated successfully.';
        Placement::find($placement_id)->update(['current_status'=>$request->status]);

        // send email if the status is Cancelled
        // if($request->status == 'Cancelled'){
        //         $this->findTemplateAndSendEmail([$placement_id], $request->status);
        // }
        return $this->response();
    }

    /**
     * This function uses to send the email with attachments
     * @param  Request $request [description]
     * @return JSON
     */ 
    public function sendEmail(Request $request){

        $validator =  \Validator::make($request->all(),[
                'placement_id'=>'required|array',
                'subject'=>'required',
                'to'=>'required|emails',
                'cc'=>'required|emails',
                'body'=>'required',
                'status'=>'required|in:Confirmed,Cancelled,Reminder'
        ],[
            'to.emails' => 'Emails are not valid or may not be join by comma.',
            'cc.emails' => 'Emails are not valid or may not be join by comma.'
        ]);

        if($validator->fails()) {
            $this->status = false;
            $this->message = 'Please enter the valid details.';
            $this->errors = $validator->errors();
            $code = 422;

        } else {
                
                $this->message = 'Placement status has been Updated successfully.';
                $attachments = $request->file('attachments');
                $placement_id = $request->get('placement_id');

                if($request->status != 'Reminder') {

                    if($request->status == 'Confirmed'){

                        $placements = $this->makeConfirmStatus($placement_id)->pluck('p.id')->all();
                    }
                    else{
                        Placement::whereIn('id', $placement_id)
                                    ->update(['placements.current_status' => $request->status]);
                        $placements = $placement_id;
                    }
                    
                } else {

                    $placements = $placement_id;
                }

                //if(in_array($request->status, array('Confirmed','Cancelled'))):
               
               $general = new General();
               $is_send = $general->sendEmail($request->subject, $request->body, $request->to, $request->cc, $attachments);

               $is_send ? Placement::whereIn('id', $placements)
                            ->update(['is_email_send' => 'Yes', 'last_email_sent'=> date('Y-m-d H:i:s')]) : null;

                //endif;
                
                $code = 200;
        }
        return $this->response($code);
    }

    /**
    * To get the participants
    * @param $participants
    * @return 
    */
    public function getParticipantsWithToAndCC($course_participants){
            $participants = '<table border="1" cellspacing="0" cellpadding="5" width="100%"><thead><tr><th>Name</th><th>Dept</th><th>Division</th><th>Email</th><th>Participants Supervisor</th></tr></thead><tbody>';
            $email_content = $to = $cc = [];

            foreach ((array) $course_participants as $key => $value) {
                $participants .= '<tr>
                            <td>'.$value['name'].'</td>
                            <td>'.$value['dept_name'].'</td>
                            <td>'.$value['division'].'</td>
                            <td>'.$value['email'].'</td>
                            <td>'.$value['supervisor_name'].'</td>
                        </tr>';

                $to[] = $value['email'];
                
                if($value['supervisor_email']){
                    $cc[] = $value['supervisor_email'];
                }
            }
            $participants .= '</tbody></table>';

            # Admin admin Email In CC
            //$admins = new \App\User();
            //$admins = $admins->getAdmins()->where('id', \Auth::user()->id)->get();
            $adminsEmails = \Auth::user() ? [\Auth::user()->email] : [];
            $cc = array_unique(array_merge($cc, $adminsEmails));
            $email_content['participants'] = $participants;
            $email_content['to'] = $to;
            $email_content['cc'] = $cc;
            
            return $email_content;
    }

    /**
    * to send the email
    * @param $placement_id, $status
    */
    /*protected function findTemplateAndSendEmail(Array $placement_id, $status){
            
            $general = new General();
            
            $courses = CourseRun::select(
                       'courses.title',
                       'course_runs.start_date',
                       'course_runs.end_date',
                       'course_runs.assessment_start_date',
                       'training_locations.location'
            )
            ->join('placements', function ($q) {

                $q->on('placements.course_run_id','=','course_runs.id')
                    ->whereNull('placements.deleted_at');
            })
            ->leftJoin('courses','courses.course_code','=','course_runs.course_code')
            ->leftJoin('training_locations','training_locations.id','=','courses.training_location_id')
            ->whereIn('placements.id', $placement_id)->first();

            if(!$courses || empty($placement_id)) {
                $this->message = 'Status is in already confirmed status.';
                $this->error_code = 'INVALID_PLACE'; 
                return true;
            }
            $data['title'] = $courses->title;
            $data['start_date'] = $courses->start_date;
            $data['end_date'] = $courses->end_date;
            $data['assessment_date'] = $courses->assessment_start_date;
            $data['venue'] = $courses->location;
            
            $course_participants = Placement::select(
                        'users.email as email',
                        'users.name as name',
                        'users.division as division',
                        'departments.dept_name',
                        'u1.email as supervisor_email',
                        'u1.name as supervisor_name'
            )
            ->join('users','users.personnel_number','=','placements.personnel_number')
            ->leftJoin('departments','departments.id','users.department_id')
            ->leftJoin('users as u1', 'users.supervisor_personnel_number','=','u1.personnel_number')
            ->whereIn('placements.id', $placement_id)->get()->toArray();

            $email_template = $this->getParticipantsWithToAndCC($course_participants);


            $data['participants'] = $email_template['participants'];
            $to = $email_template['to'];
            $cc = $email_template['cc'];

            $email_template = $general->getTemplate($status, $data);

            $is_send =  $general->sendEmail($email_template['subject'], $email_template['body'], $to, $cc);
             Placement::whereIn('id', $placement_id)
                            ->update(['is_email_send' => 'Yes', 'last_email_sent'=> date('Y-m-d H:i:s')]);

            return true;
    }*/

    /**
     * This function uses to update the status of Placement
     * @param  Request $request [description]
     * @return JSON
     */ 
    public function emailTemplate(Request $request){

        $validator =  \Validator::make(
            $request->all(),[
                'placement_id' => 'required|array',
                'status'=>'required|in:Cancelled,Confirmed,Reminder'
            ]
        );

        $template = '';
        if($validator->fails()) {

            $this->status = false;
            $this->message = 'Please enter the valid details.';
            $this->errors = $validator->errors();
            $code = 422;
            
        } else {
                
                $general = new General();

                $courses = CourseRun::select(
                           'courses.title',
                           'course_runs.start_date',
                           'course_runs.end_date',
                           'course_runs.assessment_start_date',
                           'training_locations.location'
                )
                ->leftJoin('placements','placements.course_run_id','=','course_runs.id')
                ->leftJoin('courses','courses.course_code','=','course_runs.course_code')
                ->leftJoin('training_locations','training_locations.id','=','courses.training_location_id')
                ->whereIn('placements.id', $request->get('placement_id'))->first();

                $data['title'] = $courses ? $courses->title : '';
                $data['start_date'] = $courses ? $courses->start_date : '';
                $data['end_date'] = $courses ? $courses->end_date : '';
                $data['assessment_date'] = $courses ? $courses->assessment_start_date : '';
                $data['venue'] = $courses ? $courses->location : '';

                $placement_id = $request->get('placement_id');

                if($request->status == 'Confirmed') {

                    $placements = $this->confictInstance($placement_id);

                    $class_ful_course_run = $this->classFullCourseRun($placement_id)->pluck('course_run_id')->toArray();

                    if(!empty($class_ful_course_run)) {

                        $placements->whereNotIn('course_runs.id', $class_ful_course_run);
                    }

                    $placement_id = $placements
                    ->whereRaw('(p.id is NULl OR p.type = "subordinate" OR p.should_check_deconflict = "No" OR course_runs.should_check_deconflict = "No" OR placements.course_run_id = p.course_run_id )')
                    ->groupBy('placements.id')->get(['placements.id'])
                    ->pluck('id')->toArray();
                }
                
                $course_participants = Placement::select(
                    'users.email as email',
                    'users.name as name',
                    'users.division as division',
                    'departments.dept_name',
                    'u1.email as supervisor_email',
                    'u1.name as supervisor_name'
                )
                ->leftJoin('users','users.personnel_number','=','placements.personnel_number')
                ->leftJoin('departments','departments.id','users.department_id')
                ->leftJoin('users as u1', 'users.supervisor_personnel_number','=','u1.personnel_number')
                ->whereIn('placements.id', $placement_id)->get()->toArray();

                if(empty($course_participants)){

                    $this->message = 'Invalid Placement';
                    $this->data = $placement_id;
                    $this->status = false;
                    $this->error_code = 'INVALID_PLACE';
                    return $this->response();
                }

                $email_template = $this->getParticipantsWithToAndCC($course_participants);

                $data['participants'] = $email_template['participants'];
                $template = [];
                $email_body = $general->getTemplate($request->status, $data);

                $template['to'] = implode(',',$email_template['to']);
                $template['cc'] = implode(',',$email_template['cc']);
                $template['body']  = $email_body['body'];
                $template['subject']  = $email_body['subject'];

                $code = 200;
        }
        $this->data = $template;
        return $this->response($code);
    }
    
    /**
     * To Change the Given placement status in conformed
     */
    public function changeStatusToConfirm(Request $request) {

        $this->validate($request,[
            'placement_id' => 'required|array'
        ]);
        
        $this->data = $this->makeConfirmStatus($request->get('placement_id'));

        $placements = $this->data->pluck('id')->all();
        $this->data = $placements;
        $this->error_code = !count($placements) ? 'INVALID_PLACE' : '';

        //$this->findTemplateAndSendEmail($placements, 'Confirmed');

        return $this->response();
    }

    /**
     * Check before confoirmed the placement , if the Course Run' class limit is being over
     */
    private function classFullCourseRun(Array $placement_ids) {

        return Placement::whereIn('placements.id', $placement_ids)
            ->leftJoin('placements as confirmed_placement', function($q) {
                
                $q->on('confirmed_placement.course_run_id', '=', 'placements.course_run_id');
                $q->whereNull('confirmed_placement.deleted_at');
                $q->where('confirmed_placement.current_status', '=', 'Confirmed');
            })
            ->join('course_runs', function ($q){
                $q->on('course_runs.id','=','placements.course_run_id');
            })
            ->groupBy('placements.course_run_id')
            ->havingRaw('(no_of_being_confirmed + no_of_confirmed_placement) > no_of_trainees OR deleted_at is not null')
            ->get([
                'course_runs.id as course_run_id',
                DB::raw('IFNULL(course_runs.no_of_trainees,0) as no_of_trainees'),
                'course_runs.deleted_at',
                 DB::raw('COUNT(distinct placements.id) as no_of_being_confirmed'),
                 DB::raw('COUNT(distinct confirmed_placement.id) as no_of_confirmed_placement'),
                 //DB::raw('COUNT( distinct IF ( confirmed_placement.id in ('.implode(',', $placement_ids).'), confirmed_placement.id , 0) ) as no_of_confirmed_in_selected'),
                'placements.id'

            ]);
    }

    /**
     * Prepare the error message if class size has been Overed.
     */
    private function prepareClassFullMessage(Array $placement_ids) {

        $class_ful_course_run = $this->classFullCourseRun($placement_ids);
        //\Log::info($class_ful_course_run->toArray());
        //$no_of_already_confirmed = $class_ful_course_run->

        $errors = [];

        foreach ($class_ful_course_run as  $placement) {
            
            $course_run_id = $placement['course_run_id'];
            $class_size = $placement['no_of_trainees'];
            $a_confirmed = $placement['no_of_confirmed_placement'];
            $no_of_being_confirmed = $placement['no_of_being_confirmed'];
            //$no_of_confirmed_in_selected = $placement['no_of_confirmed_in_selected'];
           $no_of_confirmed_in_selected = Placement::whereIn('placements.id', $placement_ids)
                ->join('course_runs', function ($q){
                    $q->on('course_runs.id','=','placements.course_run_id');
                })->where('placements.current_status', 'Confirmed')->count();

            if($placement['deleted_at']) {

                $errors[$course_run_id][] = 'Course Run has been deleted.';

            }else {
                
               $ac = $no_of_confirmed_in_selected ?  ' and '.$no_of_confirmed_in_selected .' selected placement(s) are already in confirmed status. Please select only placement(s) those are not confirmed.' : '';

                $errors[$course_run_id][] = 'class size is '. $class_size .'. and already confirmed placements are '. $a_confirmed .' and you are trying to confirm the '. $no_of_being_confirmed .' Placement(s) '.$ac;
            }
        }

        return $errors;
    }

    /**
     * This function uses to check the conflicts of the placement
     * @param  Request $request [description]
     * @return JSON
     */ 
    public function checkConflict(Request $request){

        $this->validate($request,[
            'placement_id' => 'required|array'
        ]);

        $placement_ids = $request->get('placement_id');
        
        # check if class size over
        $errors = $this->prepareClassFullMessage($placement_ids);

        if(count($errors)) {

            $this->status = false;
            $this->error_code = 'CSO';
            $this->errors = $errors;
            $this->message = 'Class Size is Over. Please verify the selected placement.';
            return $this->response();
        }

        # List of Conflict placements 
        $conflicts = $this->confictInstance($placement_ids)
                ->whereNotNull('p.id')
                ->groupBy('p.id')
                ->with(['participants','subordinates','courseRun.creator','conflictInCourseRun.creator'])
                ->get([
                    'placements.id as placement_id',
                    'placements.personnel_number',
                    'course_runs.id as course_run_id',
                    'course_runs.course_code',
                    'course_runs.start_date',
                    'course_runs.end_date',
                    'course_runs.assessment_start_date',
                    'course_runs.assessment_end_date',
                    'p.id as conflict_in_placement_id',
                    'course_runs.should_check_deconflict as cr_should_check_deconflict',
                    'p.should_check_deconflict as another_should_check_deconflict',
                    'p.type',
                    \DB::raw('IF ( p.placement_per_id <> placements.personnel_number, p.placement_per_id, null) as subordinate_per_id '),
                    'p.course_run_id as conflict_in_course_run_id',
                    'p.personnel_number as conflict_in_personnel_number',
                    'p.course_code as conflict_in_course_code',
                    'p.start_date as conflic_in_start_date',
                    'p.end_date as conflic_in_end_date',
                    'p.assessment_start_date as conflic_in_assessment_start_date',
                    'p.assessment_end_date as conflic_in_assessment_end_date',
                ]);

        $this->errors = $conflicts->groupBy('placement_id')->toArray();
        $this->status = (count($this->errors) === 0);
        $this->data = [];
        return $this->response();
    } 
    
    /**
     * TO Change Placement Status
     */
    private  function makeConfirmStatus(Array $placement_id) {
        
        $class_ful_course_run = $this->classFullCourseRun($placement_id)->pluck('course_run_id')->toArray();

        $date = date('Y-m-d H:i:s');

        # Update the Placement , those do not have Conflict
        $update = $this->confictInstance($placement_id)

            ->whereRaw('(p.id is NULl OR p.type = "subordinate" OR p.should_check_deconflict = "No" OR course_runs.should_check_deconflict = "No" OR placements.course_run_id = p.course_run_id )')                  
            ->groupBy('placements.id');

        # Ignore the Placement of the course runs , those's class size has been already full.
        if(!empty($class_ful_course_run)) {

            $update->whereNotIn('course_runs.id', $class_ful_course_run);
        }

        $update->update(['placements.current_status' => 'Confirmed','placements.updated_at' => $date]);
        
        # Getting the placement, which has been updated 
        $placements = Placement::whereIn('id', $placement_id)
                        ->where('updated_at', $date)
                        ->get();
                        
        # Make the Course Run status confirmed those placements are confirming during this process.
        if($placements->isNotEmpty()) {
                    
            $course_runs = new CourseRun();
            $course_runs->timestamps = false;
            $course_runs->join('placements','placements.course_run_id','=','course_runs.id')
                ->whereIn('placements.id', $placements->pluck('id'))
                ->update(['course_runs.current_status'=> 'Confirmed']);
        }

        return $placements;
    }

    /**
     * Instance to Check the conflict 
     * If you will add ->whereRaw('(p.id is NUll') that mean you will get the placements, those do not have conflict
     * If you will add ->(p.id IS NOT NUll)  that mean you will get the placements, those have conflict
     */
    private function confictInstance(Array $placement_id) {
       
        $placement = new Placement();
        $placement->timestamps = false;

        return $placement->whereIn('placements.id', $placement_id)
            ->join('course_runs', function ($q){
                $q->on('course_runs.id','=','placements.course_run_id');
                $q->whereNull('course_runs.deleted_at');
            })
            ->where('placements.current_status','<>' ,'Confirmed')
            ->leftJoin('my_and_subordinate_placement as p', 
                function($q) {
                    $q->on('p.personnel_number','=','placements.personnel_number');
                    $q->whereRaw('(p.id  <> placements.id)');
                    //placements.course_run_id <> p.course_run_id
                    $q->whereRaw('(p.type = "my" OR (p.type = "subordinate" ) )');
                    $q->whereRaw('(
                    (
                        (course_runs.start_date <=  p.start_date AND course_runs.end_date >= p.start_date )
                        OR 
                        (  course_runs.start_date <=  p.end_date AND course_runs.end_date >= p.end_date )
                        OR 
                        ( p.start_date <= course_runs.start_date AND p.end_date >= course_runs.start_date )
                        OR 
                        (p.start_date <= course_runs.end_date AND  p.end_date >= course_runs.end_date )
                    )
                    OR
                    (
                        p.assessment_start_date IS NOT NULL 
                        AND p.assessment_end_date IS NOT NULL 
                        AND course_runs.assessment_start_date IS NOT NULL 
                        AND course_runs.assessment_end_date IS NOT NULL 
                        AND 
                        (
                            (course_runs.assessment_start_date <=  p.start_date AND course_runs.assessment_end_date >= p.start_date )
                                OR 
                            (  course_runs.assessment_start_date <=  p.end_date AND course_runs.assessment_end_date >= p.end_date )
                                OR 
                            ( p.start_date <= course_runs.assessment_start_date AND p.end_date >= course_runs.assessment_start_date )
                                OR 
                            (p.start_date <= course_runs.assessment_end_date AND  p.end_date >= course_runs.assessment_end_date )
                        )
                    )
                    OR 
                    (
                        (course_runs.start_date <=  p.assessment_start_date AND course_runs.end_date >= p.assessment_start_date )
                        OR 
                        (  course_runs.start_date <=  p.assessment_end_date AND course_runs.end_date >= p.assessment_end_date )
                        OR 
                        ( p.assessment_start_date <= course_runs.start_date AND p.assessment_end_date >= course_runs.start_date )
                        OR 
                        (p.assessment_start_date <= course_runs.end_date AND  p.assessment_end_date >= course_runs.end_date )
                    )
                    OR
                    (
                        p.assessment_start_date IS NOT NULL 
                        AND p.assessment_end_date IS NOT NULL 
                        AND course_runs.assessment_start_date IS NOT NULL 
                        AND course_runs.assessment_end_date IS NOT NULL 
                        AND 
                        (
                            (course_runs.assessment_start_date <=   p.assessment_start_date AND course_runs.assessment_end_date >=  p.assessment_start_date )
                                OR 
                            (  course_runs.assessment_start_date <=  p.assessment_end_date AND course_runs.assessment_end_date >= p.assessment_end_date )
                                OR 
                            (  p.assessment_start_date <= course_runs.assessment_start_date AND p.assessment_end_date >= course_runs.assessment_start_date )
                                OR 
                            ( p.assessment_start_date <= course_runs.assessment_end_date AND  p.assessment_end_date >= course_runs.assessment_end_date )
                        )
                    )
                    
                    )');   
            });

            //->whereRaw('p.course_run_id <> placements.course_run_id');
    }

    /**
     * This function uses to fetch the placement list
     * @param  Request $request [description]
     * @return list
     */ 
    private function getPlacementList(Request $request, $type = ''){
        $placements = Placement::select(
          'placements.id as id',
          'placements.result_uploaded',
          'placements.action',
          'pc.prog_category_name',
          'py.prog_type_name',
          'courses.title',
          'courses.course_code',
          'users.name as created_by',
          'percipient.name as percipient_name',
          'percipient.personnel_number',
          'placements.attendance',
          'placements.current_status',
          'placements.assessment_results',
          'course_runs.start_date',
          'course_runs.end_date',
          'failure_reasons.failure_reason',
          'absent_reasons.absent_reason',
          'course_runs.id as course_run_id',
          'departments.dept_name as user_dept_name',
          'cdpt.dept_name as dept_name',
          'percipient.division',
        'course_runs.assessment_start_date',
        'course_runs.assessment_end_date',
        'percipient.supervisor_personnel_number',
          'percipient.branch',
          'u1.name as supervisor_name',
           DB::raw("DATE_FORMAT(course_runs.start_date, '%d/%m/%Y') as formatted_start_date"),
           DB::raw("DATE_FORMAT(course_runs.end_date, '%d/%m/%Y') as formatted_end_date"),
           DB::raw("DATE_FORMAT(course_runs.assessment_start_date, '%d/%m/%Y') as formatted_assessment_start_date"),
           DB::raw("DATE_FORMAT(course_runs.assessment_end_date, '%d/%m/%Y') as formatted_assessment_end_date")
        )
        ->join('course_runs','course_runs.id','=','placements.course_run_id')                    
        ->join('courses','courses.course_code','=','course_runs.course_code')
        ->leftJoin('failure_reasons','placements.failure_reason_id','=','failure_reasons.id')
        ->leftJoin('absent_reasons','placements.absent_reason_id','=','absent_reasons.id')
        ->join('programme_categories as pc','pc.id','=', 'courses.programme_category_id')
        ->join('programme_types as py','py.id','=', 'courses.programme_type_id')
        ->leftJoin('users as users','users.id','=', 'placements.creator_id')
        ->join('users as percipient','percipient.personnel_number','=', 'placements.personnel_number')
        ->leftJoin('users as u1', 'percipient.supervisor_personnel_number','=','u1.personnel_number')
        ->leftJoin('departments','departments.id','percipient.department_id')
        ->leftJoin('departments as cdpt','cdpt.id','courses.department_id');


        /** start custom filters **/
        if($request->has('customFilters')) {            

            $general = new General();
            $keys = [
                'course_code' => 'courses.course_code',
                'title' => 'courses.title',
                'prog_category_name' => 'pc.id',
                'created_by' => 'users.id',
                'percipient_name' => 'percipient.id',
                'supervisor_name' =>'u1.name',
                'personnel_number'=>'percipient.personnel_number',
                'start_date' => 'course_runs.start_date',
                'end_date' => 'course_runs.end_date',
                'user_dept_name'=> 'departments.id',
                'current_status' => 'placements.current_status',
                'result_uploaded' => 'placements.result_uploaded',
                'course_run_id' => 'course_runs.id',
                'failure_reason' => 'failure_reasons.id',
                'absent_reason' => 'absent_reasons.id',
                'prog_type_name' => 'py.id',
                'attendance'=> 'placements.attendance',
                'action'=> 'placements.action',
                'assessment_results'=>'placements.assessment_results',
                'assessment_start_date' => 'course_runs.assessment_start_date',
                'assessment_end_date' => 'course_runs.assessment_end_date',
                'test_date_range' => ['assessment_start_date','assessment_end_date'],
                'date_range' => ['start_date','end_date']
            ];

            $general->applyFilters($placements, $request->customFilters, $keys);
        }

        /** start sorting **/
        if($request->has('sortName')):
        switch ($request->sortName) {
            case 'course_run_id':
                $placements->orderBy('course_runs.id',$request->sortOrder); 
                break;

            case 'course_title':
                $placements->orderBy('courses.title',$request->sortOrder)->orderBy('percipient.id','DESC');
                break; 
            case 'title':
                $placements->orderBy('courses.title',$request->sortOrder)->orderBy('percipient.id','DESC');
                break; 
            
            case 'prog_type_name':
                $placements->orderBy('py.prog_type_name',$request->sortOrder);
                break; 
            
            case 'attendance':
                $placements->orderBy(\DB::raw('CAST(placements.attendance AS CHAR)'),$request->sortOrder);
                break; 
            //assessment_results    

            case 'assessment_results':
                $placements->orderBy(\DB::raw('CAST(placements.assessment_results AS CHAR)'),$request->sortOrder);
                break; 

            case 'prog_category_name':
                $placements->orderBy('pc.prog_category_name',$request->sortOrder);
                break; 
            case 'personnel_number':
                $placements->orderBy('percipient.personnel_number',$request->sortOrder);
                break; 
            case 'percipient_name':
                $placements->orderBy('percipient.name',$request->sortOrder);   
                break;
            case 'supervisor_name':
                $placements->orderBy('u1.name',$request->sortOrder);   
                break;
            case 'user_dept_name':
                $placements->orderBy('departments.dept_name',$request->sortOrder); 
                break;
            case 'division':
                $placements->orderBy('percipient.division',$request->sortOrder);   
                break;
            case 'branch':
                $placements->orderBy('percipient.branch',$request->sortOrder); 
                break;
            case 'date_range':
                $placements->orderBy('course_runs.start_date',$request->sortOrder); 
                break;
            case 'test_date_range':
                $placements->orderBy('course_runs.assessment_start_date',$request->sortOrder);   
                break;
            
            case 'failure_reason':
                $placements->orderBy('failure_reasons.failure_reason',$request->sortOrder); 
                break;
            case 'absent_reason':
                $placements->orderBy('absent_reasons.absent_reason',$request->sortOrder); 
                break;
            
            default:
                # code...
                break;
        }
        endif;
        /** end sorting **/

        return $placements->groupBy('placements.id');
    }

    /** Viewer Section **/
    /**
     * This function uses to export the excel for viewer
     * @param  Request $request [description]
     */ 
    public function getUserPlacement(Request $request){
        $placements = self::getPlacementList($request);
        $placements->where('placements.personnel_number', \Auth::user()->personnel_number);

        if($request->get('export')) {

            $data = $placements->get();

            General::downloadExcel(\Excel::create('Placement', function($excel) use ($data) {
                $excel->sheet('Placement DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Course Title');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Personnel Number');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Participants');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Department');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('Division');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Branch');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Start Date');});
                    $sheet->cell('I1', function($cell) {$cell->setValue('End Date');});
                    $sheet->cell('J1', function($cell) {$cell->setValue('Attendance');});
                    $sheet->cell('K1', function($cell) {$cell->setValue('Result');});
                    $sheet->cell('L1', function($cell) {$cell->setValue('Absent Reason');});
                    $sheet->cell('M1', function($cell) {$cell->setValue('Failure Reason');});

                    $sheet->cell('A1:M1', function($cell) {
                           $cell->setFontWeight('bold');
                           $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->setBorder('A1:L1', 'thin');

                    $sheet->freezeFirstRowAndColumn();

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->course_run_id);
                        $sheet->cell('B'.$key, $row->title);
                        $sheet->cell('C'.$key, $row->personnel_number);
                        $sheet->cell('D'.$key, $row->percipient_name);
                        $sheet->cell('E'.$key, $row->dept_name);
                        $sheet->cell('F'.$key, $row->division);
                        $sheet->cell('G'.$key, $row->branch);
                        $sheet->cell('H'.$key, $row->formatted_start_date);
                        $sheet->cell('I'.$key, $row->formatted_end_date);                      
                        
                        $sheet->cell('J'.$key, $row->attendance);
                        $sheet->cell('K'.$key, $row->assessment_results);
                        $sheet->cell('L'.$key, $row->absent_reason); 
                        $sheet->cell('M'.$key, $row->failure_reason); 

                        $sheet->getStyle('A'.$key.':L'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));

        } else {

            $this->data = $placements->paginate($this->sizePerPage);
            return $this->response();
        }

    }


    /**
     * This function uses to export the excel for viewer
     * @param  Request $request [description]
     */ 
    public function getSubordinatePlacement(Request $request){
        
        $placements = self::getPlacementList($request);

        $placements->where('percipient.supervisor_personnel_number', \Auth::user()->personnel_number);

        if($request->get('export')) {

            $data = $placements->get();
            
            General::downloadExcel(\Excel::create('Placement', function($excel) use ($data) {
                $excel->sheet('Placement DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Course Title');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Personnel Number');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Participants');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Department');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('Division');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Branch');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Start Date');});
                    $sheet->cell('I1', function($cell) {$cell->setValue('End Date');});
                    $sheet->cell('J1', function($cell) {$cell->setValue('Attendance');});
                    $sheet->cell('K1', function($cell) {$cell->setValue('Result');});
                    $sheet->cell('L1', function($cell) {$cell->setValue('Absent Reason');});
                    $sheet->cell('M1', function($cell) {$cell->setValue('Failure Reason');});

                    $sheet->cell('A1:M1', function($cell) {
                           $cell->setFontWeight('bold');
                           $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->setBorder('A1:L1', 'thin');

                    $sheet->freezeFirstRowAndColumn();

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->course_run_id);
                        $sheet->cell('B'.$key, $row->title);
                        $sheet->cell('C'.$key, $row->personnel_number);
                        $sheet->cell('D'.$key, $row->percipient_name);
                        $sheet->cell('E'.$key, $row->dept_name);
                        $sheet->cell('F'.$key, $row->division);
                        $sheet->cell('G'.$key, $row->branch);
                        $sheet->cell('H'.$key, $row->formatted_start_date);
                        $sheet->cell('I'.$key, $row->formatted_end_date);
                        $sheet->cell('J'.$key, $row->attendance);
                        $sheet->cell('K'.$key, $row->assessment_results);
                        $sheet->cell('L'.$key, $row->absent_reason); 
                        $sheet->cell('M'.$key, $row->failure_reason); 

                        $sheet->getStyle('A'.$key.':L'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));
            
        } else {

            $this->data = $placements->paginate($this->sizePerPage);
            return $this->response();
        }
    }

    /**
     * This function uses to update the placement status as deleted
     * @param  Request $request [description]
     * @param  [type]  $placement_id [description]
     * @return [type]           [description]
     */ 
    public function deleteResult(Request $request)
    {

        $this->validate($request,[
            'ids' => 'required'
        ]);

        Placement::whereIn('id', $request->get('ids'))->update(['result_uploaded' => 'No']);
        
        $this->message = 'Data has been Deleted';
        
        return $this->response();
    }

    /**
     * This function uses to delete the Placement Records
     * @param  Request $request [description]
     * @param  [type]  $placement_id [description]
     * @return [type]           [description]
     */ 
    public function delete(Request $request)
    {

        $this->validate($request,[
            'ids' => 'required'
        ]);

        Placement::whereIn('id', $request->get('ids'))->delete();
        
        $this->message = 'Data has been Deleted';
        
        return $this->response();
    }

    /**
     * This function uses to fetch and export post course run
     * @param  Request $request [description]
     */ 
    public function postCourse(Request $request){

        $course_run = self::getPlacementList($request);
        $course_run->where('result_uploaded','Yes');

        if($request->get('export')) {

            /** if selected record **/
                if($request->has('selected')):
                    $course_run->whereIn('placements.id', $request->get('selected'));
                endif;
            /** selected record **/

            $data = $course_run->get();

            General::downloadExcel(\Excel::create('Post Course Run', function($excel) use ($data) {
                $excel->sheet('Post Course Run DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Per ID');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Attendance');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Assessment Results');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Absent Reason');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('Action');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Failure Reason');});
                    // $sheet->cell('B1', function($cell) {$cell->setValue("Programme");});                    
                    // $sheet->cell('C1', function($cell) {$cell->setValue('Course Title');});
                    // $sheet->cell('D1', function($cell) {$cell->setValue("Participants");});
                    // $sheet->cell('E1', function($cell) {$cell->setValue('Attendance');});
                    // $sheet->cell('F1', function($cell) {$cell->setValue('Assessment Result');});
                    // $sheet->cell('G1', function($cell) {$cell->setValue('Absent Reason');});
                    // $sheet->cell('H1', function($cell) {$cell->setValue('Failure Reason');});
                    // $sheet->cell('I1', function($cell) {$cell->setValue('Supervisor Name');});

                    $sheet->cell('A1:G1', function($cell) {
                           $cell->setFontWeight('bold');
                           $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->setBorder('A1:G1', 'thin');

                    $sheet->freezeFirstRowAndColumn();

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->course_run_id);
                        $sheet->cell('B'.$key, $row->personnel_number);                        
                        $sheet->cell('C'.$key, $row->attendance);
                        $sheet->cell('D'.$key, $row->assessment_results); 
                        $sheet->cell('E'.$key, $row->absent_reason);
                        $sheet->cell('F'.$key, $row->action);
                        $sheet->cell('G'.$key, $row->failure_reason);
                        

                        $sheet->getStyle('A'.$key.':G'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));

        } else {
            $this->data = $course_run->paginate($this->sizePerPage);
            return $this->response();
        }

    }

    public function maintainListOfCourseRun(Request $request, $course_run_id) {

        $placements = self::getPlacementList($request);
        $this->data = $placements->where('course_runs.id', $course_run_id)->paginate($this->sizePerPage);
        return $this->response();
    }

    /**
     * This function uses to export the maintain course run excel
     * @param  Request $request [description]
     */ 
    public function maintainList(Request $request){
        $course_run = self::getPlacementList($request);

        if($request->get('export')) {
            $data = $course_run->get();

            General::downloadExcel(Excel::create('Maintain Course Run', function($excel) use ($data) {
                $excel->sheet('Maintain Course Run DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Run ID');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Program Type');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Course Title');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Participants');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Start Date');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('End Date');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Assessment Start Date');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Assessment End Date');});
                    $sheet->cell('I1', function($cell) {$cell->setValue('Status');});
                    $sheet->cell('J1', function($cell) {$cell->setValue('Supervisor');});
                    

                    $sheet->cell('A1:J1', function($cell) {
                           $cell->setFontWeight('bold');
                           $cell->setBackground('#ffffcc'); 
                    });

                    $sheet->setBorder('A1:J1', 'thin');

                    $sheet->freezeFirstRowAndColumn();

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->course_code);
                        $sheet->cell('B'.$key, $row->prog_type_name);
                        $sheet->cell('C'.$key, $row->title);
                        $sheet->cell('D'.$key, $row->percipient_name);
                        $sheet->cell('E'.$key, $row->formatted_start_date);
                        $sheet->cell('F'.$key, $row->formatted_end_date);
                        $sheet->cell('G'.$key, $row->formatted_assessment_start_date);
                        $sheet->cell('H'.$key, $row->formatted_assessment_end_date);
                        $sheet->cell('I'.$key, $row->current_status);
                        $sheet->cell('J'.$key, $row->supervisor_name); 

                        $sheet->getStyle('A'.$key.':J'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));

        } else {
            $this->data = $course_run->paginate($this->sizePerPage);
            return $this->response();
        }

    }
}