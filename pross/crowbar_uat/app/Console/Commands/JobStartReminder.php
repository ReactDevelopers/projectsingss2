<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class JobStartReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'JobStartReminder:jobstartreminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Job start reminder to talent & employer, and after expiry date(if job not started)email notification to admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $two_days_ahead = date('Y-m-d', strtotime('+2 days'));
        $one_days_ago = date('Y-m-d', strtotime('-1 days'));

        $previousdatedata = $this->commonQuery()->where('projects.startdate','>=',$two_days_ahead)
                ->get();

        $currentdatedata = $this->commonQuery()->where('projects.startdate','<=',date('Y-m-d'))
                ->where('projects.enddate','>=',date('Y-m-d'))
                ->get();

        $admindatedata = $this->commonQuery()->where('projects.enddate','=',$one_days_ago)
                ->get();

        
            // dd($currentdatedata,$admindatedata); 
        if($previousdatedata->count()>0){
            $var = $previousdatedata->title.' Job has not been yet started.';
            $isNotified = \Models\Notifications::notify(
                    $previousdatedata->talentid,
                    0,
                    'JOB_START_REMINDER',
                    json_encode([
                        "talent_id"     => (string) $previousdatedata->talentid,
                        "msg"           => $var,
                    ])
                ); 
            $isNotified = \Models\Notifications::notify(
                    $previousdatedata->employerid,
                    0,
                    'JOB_START_REMINDER',
                    json_encode([
                        "employer_id"     => (string) $previousdatedata->employerid,
                    ])
                );   
            foreach ($previousdatedata as $key => $previousdate) {
                // employer
                $email                  = $previousdate->emp_email;
                $emailData              = ___email_settings();
                $emailData['email']     = $email;
                $emailData['name']      = $previousdate->emp_name;
                $emailData['context']      = $previousdate->tal_name.' has not started the Job yet. Ending Date for this job is '.$previousdate->enddate;

                // talent
                $talentemail                  = $previousdate->tal_email;
                $talentemailData              = ___email_settings();
                $talentemailData['email']     = $email;
                $talentemailData['name']      = $previousdate->tal_name;
                $talentemailData['context']      = 'Please Start Your Job before the end date '.$previousdate->enddate.'.';

                $template_name = "job_start_reminder";

                ___mail_sender($email,'',$template_name,$emailData);  
                ___mail_sender($talentemail,'',$template_name,$talentemailData);  
            }
        }
        if($currentdatedata->count()>0){
            foreach ($currentdatedata as $key => $currentdate) {
                $var = $currentdate->title.' Job has not been yet started.';
                $isNotified = \Models\Notifications::notify(
                                    $currentdate->talentid,
                                    0,
                                    'JOB_START_REMINDER',
                                    json_encode([
                                        "talent_id"     => (string) $currentdate->talentid,
                                        "msg"           => $var,
                                    ])
                                ); 
                $isNotified = \Models\Notifications::notify(
                                    $currentdate->employerid,
                                    0,
                                    'JOB_START_REMINDER',
                                    json_encode([
                                        "employer_id"     => (string) $currentdate->employerid,
                                    ])
                                );   
                // employer
                $email                  = $currentdate->emp_email;
                $emailData              = ___email_settings();
                $emailData['email']     = $email;
                $emailData['name']      = $currentdate->emp_name;
                $emailData['context']      = $currentdate->tal_name.' has not started the Job yet. Ending Date for this job is '.$currentdate->enddate;

                // talent
                $talentemail                  = $currentdate->tal_email;
                $talentemailData              = ___email_settings();
                $talentemailData['email']     = $email;
                $talentemailData['name']      = $currentdate->tal_name;
                $talentemailData['context']      = 'Please Start Your Job before the end date '.$currentdate->enddate.'.';

                $template_name = "job_start_reminder";

                ___mail_sender($email,'',$template_name,$emailData);  
                ___mail_sender($talentemail,'',$template_name,$talentemailData);  
            }
        }
        // dd($isNotified);
        if($admindatedata->count()>0){
            foreach ($admindatedata as $key => $currentdate) {
                // employer
                // $email                  = '';
                $admin_email = \DB::table('users')->select('email')->where('type','=','superadmin')->first();
                $email                  = $admin_email->email;
                $emailData              = ___email_settings();
                $emailData['email']     = $email;
                $emailData['name']      = 'Admin';
                $emailData['context']   = 'Job Ending with name '.$currentdate->title.' has not been started by talent '.$currentdate->tal_name.'. Which has been expired on '.$currentdate->enddate.' .';

                $template_name = "job_start_reminder";

                ___mail_sender($email,'',$template_name,$emailData);  
            }
        }

    }

    private function commonQuery()
    {
        return \DB::table('talent_proposals')
                ->leftjoin('projects','projects.id_project','=','talent_proposals.project_id')
                ->leftJoin('users as user_employer',function($leftjoin){
                    $leftjoin->on('user_employer.id_user','=','projects.user_id');
                })
                ->leftJoin('users as user_talent',function($leftjoin){
                    $leftjoin->on('user_talent.id_user','=','talent_proposals.user_id');
                })
                ->select(\DB::raw("DATE_FORMAT(startdate,'%d %b %Y') as startdate"),\DB::raw("DATE_FORMAT(enddate,'%d %b %Y') as enddate"),'projects.user_id as employerid','talent_proposals.user_id as talentid','user_employer.name as emp_name','user_talent.name as tal_name','user_employer.email as emp_email','user_talent.email as tal_email','projects.title')
                ->where('talent_proposals.type','=','proposal')
                ->where('talent_proposals.status','=','accepted')
                ->where('projects.project_status','=','pending');
    }
}
