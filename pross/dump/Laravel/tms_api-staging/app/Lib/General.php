<?php

namespace App\Lib;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Models\AbsentReason as AbsentReason;
//use App\Models\CourseProvider as CourseProvider;
use App\Models\Department as Department;
use App\Models\FailureReason as FailureReason;
use App\Models\ProgrammeCategory as ProgrammeCategory;
use App\Models\ProgrammeType as ProgrammeType;
use App\Models\TrainingLocation as TrainingLocation;
use App\Models\AssessmentType;
use App\Models\DeliveryMethod;
use Auth;

class General 
{


    /**
     * Applying dynamic where condition  
     * @param Request &$query reference to query 
     * @param $filter  applied filters by the user
     * @param $column_in_db 
     */
    public function applyFilters(&$query, $filters, $column_in_db){
        
        if($filters && is_array($filters)) {
            
            foreach ($filters as $index => $key) {
                
                if($key['comparator'] == 'date-range' && isset($key['value']) && $key['value'] ) {
                    
                    $this->applyFilters($query, [
                        $column_in_db[$index][0] => ['value' =>  $key['value']['start_date'],'comparator' => '>=' ],
                        $column_in_db[$index][1] => ['value' =>  $key['value']['end_date'],'comparator' => '<=' ]
                    ], $column_in_db);
                }
                elseif(isset($key['value']) && ($key['value'] || $key['value'] == "0") ) {
                    
                    if(is_array($key['value'])){
                        $query->whereIn($column_in_db[$index], $key['value']);
                    } else {
                        $query->where($column_in_db[$index],$key['comparator'], $this->getComparisonValue($key['comparator'], $key['value']));
                    }
                }
            }
        }
        
    }

    /**
     * Getting the value  
     * @param  $operator can be =, >, <, LIKE
     * @param  $value
     * @return $value
     */
    public function getComparisonValue($operator, $value){
	    if($operator == 'LIKE')
	        return '%'.$value.'%';

	    return $value;
    }


    /**
     * Getting the listing for all the dropdown  
     * @return Json
     */
    public function getList(){
        $dataList  = [];
        $datalist['absent_reason'] = AbsentReason::getCached();
        //$datalist['course_provider'] = CourseProvider::getCached();
       // $datalist['department'] = Department::getCached();
        $datalist['department_staff'] = Department::getStafDeptCached();
        $datalist['delivery_methods'] = DeliveryMethod::getCached();
        $datalist['failure_reason'] = FailureReason::getCached();
        $datalist['programe_category'] = ProgrammeCategory::getCached();
        $datalist['programme_type'] = ProgrammeType::getCached();
        $datalist['training_location'] = TrainingLocation::getCached();
        $datalist['users'] = \App\User::getCached();
        $datalist['departments'] = \App\Models\Department::getCached();
        $datalist['roles'] = \App\Models\Role::getCached();
        $datalist['assessment_types'] = AssessmentType::getCached();
        return $datalist;
    }

    /**
     * This function is used to get the template
     * @param  [type]  1 for Confirmation, 2 for Cancellation
     * @return [id] course_id to fetch course details
     */ 
    public function getTemplate($type, $data){

        //$type = ($type == 'Confirmed') ? '1' : '2';
        //$type = null;
        
        if($type == 'Confirmed') {
            $type = 1;
        }
        else if($type == 'Cancelled') {
           $type = 2; 
        }
        else {
          $type = 3;
        }

        $email_template = \App\Models\EmailTemplate::select('body','subject')->where('type',$type)->first();
       
            // $subjectPatternFind[0] = '/{COURSE_TITLE}/';
            // $subjectPatternFind[1] = '/{START_DATE}/';
            
            // $subjectReplaceFind[0] = $data['title'];
            // $subjectReplaceFind[1] = $data['start_date'] ? date('d/m/Y', strtotime($data['start_date'])) : 'NA';

            // $bodyPatternFind[0] = '/{TITLE}/';
            // $bodyPatternFind[1] = '/{START_DATE}/';
            // $bodyPatternFind[2] = '/{END_DATE}/';
            // $bodyPatternFind[3] = '/{ASSESSMENT_DATE}/';
            // $bodyPatternFind[4] = '/{VENUE}/';
            // $bodyPatternFind[5] = '/{PARTICIPANTS}/';
            // $bodyPatternFind[6] = '/{ADMIN_EMAIL}/';
            // $bodyPatternFind[7] = '/{ADMIN_NAME}/';
            
            // $bodyReplaceFind[0] = $data['title'];
            // $bodyReplaceFind[1] = $data['start_date'] ? date('d/m/Y', strtotime($data['start_date'])) : 'NA';
            // $bodyReplaceFind[2] = $data['end_date'] ? date('d/m/Y', strtotime($data['end_date'])) : 'NA';
            // $bodyReplaceFind[3] = $data['assessment_date'] ? date('d/m/Y', strtotime($data['assessment_date'])) : 'NA';
            // $bodyReplaceFind[4] = $data['venue'];
            // $bodyReplaceFind[5] = $data['participants']; //env('TESTER_EMAIL')
            // $bodyReplaceFind[6] = (Auth::user() ? Auth::user()->email : '');
            // $bodyReplaceFind[7] = (Auth::user() ? Auth::user()->name : '');
       
        $subjectPatternFind[0] = '/{COURSE_TITLE}/';
        $subjectPatternFind[1] = '/{START_DATE}/';
        
        $subjectReplaceFind[0] = $data['title'];
        $subjectReplaceFind[1] = $data['start_date'] ? date('d/m/Y', strtotime($data['start_date'])) : 'NA';

        $bodyPatternFind[0] = '/{TITLE}/';
        $bodyPatternFind[1] = '/{START_DATE}/';
        $bodyPatternFind[2] = '/{END_DATE}/';
        $bodyPatternFind[3] = '/{ASSESSMENT_DATE}/';
        $bodyPatternFind[4] = '/{VENUE}/';
        $bodyPatternFind[5] = '/{PARTICIPANTS}/';
        $bodyPatternFind[6] = '/{ADMIN_EMAIL}/';
        $bodyPatternFind[7] = '/{ADMIN_NAME}/';
        
        $bodyReplaceFind[0] = $data['title'];
        $bodyReplaceFind[1] = $data['start_date'] ? date('d/m/Y', strtotime($data['start_date'])) : 'NA';
        $bodyReplaceFind[2] = $data['end_date'] ? date('d/m/Y', strtotime($data['end_date'])) : 'NA';
        $bodyReplaceFind[3] = $data['assessment_date'] ? date('d/m/Y', strtotime($data['assessment_date'])) : 'NA';
        $bodyReplaceFind[4] = $data['venue'];
        $bodyReplaceFind[5] = $data['participants'];
        $bodyReplaceFind[6] = (Auth::user() ? Auth::user()->email : '');
        $bodyReplaceFind[7] = (Auth::user() ? Auth::user()->name : '');

        $subject = $email_template['subject'];
        $template['subject'] = $this->removeWhiteSpace(preg_replace($subjectPatternFind, $subjectReplaceFind, $subject)); 

        $body    = stripslashes($email_template['body']);
        $template['body']    = $this->removeWhiteSpace(preg_replace($bodyPatternFind, $bodyReplaceFind, $body)); 

        return $template;
    }

    public function removeWhiteSpace($string) {
        return preg_replace('/[ \t]+/', ' ', preg_replace('/['.PHP_EOL.']+/', "\n", $string));
    }
    
    /**
     * This function is used to send the notification
     * @param  Request $request [description]
     * @return Json 
     */ 
    public function sendEmail($subject, $body, $to, $cc, $attach=''){

        $to  = !is_array($to) ? ($to ? explode(',', $to): []) : $to;
        $cc  = !is_array($cc) ? ($cc ? explode(',', $cc) : []) : $cc;

        # Admin admin Email In CC
        $admins = new \App\User();
        $admins = $admins->getAdmins()->where('id', \Auth::user()->id)->get();
        $adminsEmails = $admins ? $admins->pluck('email')->toArray() : [];
        $cc = array_unique(array_merge($cc, $adminsEmails));

        $sender = [
                    'subject' => $subject,
                    'cc' => $cc,
                    'to' => $to,
                    'email' => env('DEVELOPMENT_MODE') ? env('TESTER_EMAIL') : $to,
                    'name' => '',
                    'from' => ['address' => env('MAIL_USERNAME'),'name' => env('MAIL_FROM_NAME')]
                ];
                
        $recipient_to = is_array($to) ? json_encode($to) : ($to ? $to :'' );
        $recipient_cc = is_array($cc) ? json_encode($cc) : ($cc ? $cc :'' );

        $success = false;

        try {
            $mail = Mail::send('emails.default',  ['body' => $body], function($message) use($sender,$body, $attach) {
                       $message->to(
                           $sender['to']
                       )                       
                       ->subject($sender['subject'])
                       ->from(
                           $sender['from']['address'],
                           $sender['from']['name']
                       )
                       ->replyTo(env('REPLY_TO','PUB-JO-WSU-EP@pub.gov.sg'));

                       if(!empty($sender['cc'])){

                           $message->cc($sender['cc']);
                       }

                        if(is_array($attach)) {

                          foreach($attach as $file){
                              $message->attach($file->getRealPath(), [
                                'as' => $file->getClientOriginalName(), 
                                'mime' => $file->getMimeType()
                              ]);
                          }
                        }
            });

            $success = true;

        } catch (\Exception $ex) {            
           
            $success = false;
            \Log::info($ex->getMessage());
        }


        \App\Models\EmailLog::insert([
          'subject' => $subject, 
          'recipient_to' => $recipient_to, 
          'recipient_cc' => $recipient_cc ,
          'status' => $success ? 'success' : 'failure', 
          'created_at'=> date('Y-m-d H:i:s'),
          'updated_at'=> date('Y-m-d H:i:s')
        ]);

        return $success;
    }
      
    static public function downloadExcel($ins) {

        $environment = app()->environment();
        ($environment == 'testing') ? $ins->store('xlsx', storage_path('app/public/reports/')) : $ins->download('xlsx', ['Access-Control-Allow-Origin' => '*']);
    }

}