<?php

namespace App\Console\Commands;
use DB;
use Illuminate\Console\Command;

class CompletingProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'completingprofile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification for completing profile.';

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
    public function handle(){
        $prefix                                     = DB::getTablePrefix();
        $completing_profile_interval                = \Cache::get('configuration')['completing_profile'];
        $completing_profile_notification_counter    = \Cache::get('configuration')['completing_profile_notification_counter'];
        $today_date                                 = date('Y-m-d');
        if($completing_profile_interval != 'off'){
            $uncompleted_records = DB::table('users')
            ->select([
                'users.first_name AS name',
                'users.id_user',
                'users.email',
                'devices.device_token',
                'completing_profile_notification_counter',
                DB::raw("(IFNULL('users.percentage_step_one',0) + IFNULL('users.percentage_step_two',0) + IFNULL('users.percentage_step_three',0) + IFNULL('users.percentage_step_four',0) + IFNULL('users.percentage_step_five',0)) as percentage_count")
            ])
            ->leftJoin('devices',function($leftJoin){
                $leftJoin->on('devices.user_id','=','users.id_user');
                $leftJoin->where('devices.is_current_device','=','yes');
            })
            ->where('users.status','=','active')
            ->whereIn('users.type',['talent','employer'])
            ->where('completing_profile_notification_counter','<=',"{$completing_profile_notification_counter}")
            ->whereRaw("(completing_profile IS NULL OR datediff('{$today_date}',`completing_profile`) >= {$completing_profile_interval})")
            ->having('percentage_count','<', 100)->limit(10)->get();
            
            $uncompleted_records = json_decode(json_encode($uncompleted_records),true);
            $emailData              = ___email_settings();
            $data_packets           = [];
            if(!empty($uncompleted_records)){
                foreach ($uncompleted_records as $key => $value) {
                    $emailData['email']     = $value['email'];
                    $emailData['name']      = $value['name'];
                    $isUpdated = \Models\Users::change($value['id_user'],[
                        'completing_profile'                        => date('Y-m-d'), 
                        'completing_profile_notification_counter'   => $value['completing_profile_notification_counter']+1
                    ]);

                    $isNotified = \Models\Notifications::notify(
                        $value['id_user'],
                        SUPPORT_CHAT_USER_ID,
                        'NOTIFICATION_COMPLETING_PROFILE',
                        json_encode([
                            "user_id" => $value['id_user']
                        ])
                    );                    
                    ___mail_sender($value['email'],$value['name'],"completing_profile",$emailData);
                }                
            }
        }
    }
}
