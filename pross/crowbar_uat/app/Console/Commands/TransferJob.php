<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TransferJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TransferJob:transferjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Talent Disconnect | Firm will receive the email notifications on a daily basis if the firm doesnt transfer the jobs to another connected talent.';

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
        $users = \DB::table('users')
                        ->leftjoin('company_connected_talent','company_connected_talent.id_user','=','users.id_user')
                        ->leftjoin('talent_proposals','talent_proposals.user_id','=','company_connected_talent.id_user')
                        ->leftjoin('projects','projects.id_project','=','talent_proposals.project_id')
                        ->select([
                            'users.id_user',
                            'users.email',
                            'users.name',
                            'company_connected_talent.id_talent_company',
                            \DB::raw("DATE_FORMAT(notice_expired,'%d %b %Y') as notice_expired"),
                            'talent_proposals.status',
                            'projects.title'
                        ])
                        // ->where('user_type','owner')
                        ->where('users.is_notice_period','=','Y')
                        ->where('users.notice_expired','>=',date('Y-m-d'))
                        ->where('talent_proposals.status','=','accepted')
                        ->orWhere('talent_proposals.status','=','applied')
                        ->where('projects.project_status','=','initiated')
                        // ->orWhere('projects.project_status','=','pending')
                        ->groupBy('users.id_user')
                        ->get();

        // dd($users);

        foreach ($users as $key => $value) {
            $owner = \Models\companyConnectedTalent::with(['user'])->where('id_talent_company',$value->id_talent_company)->where('user_type','owner')->first(); 
            $email                  = $owner['user']->email;
            $emailData              = ___email_settings();
            $emailData['email']     = $email;
            $emailData['name']      = $owner['user']->name;
            $emailData['context']   = 'Please transfer the job of '.$value->name.' to another connected talent  before the ending date of his notice period '.$value->notice_expired;

            $template_name = "transfer_job";

            ___mail_sender($email,'',$template_name,$emailData); 
        }

        // $refundusers = \DB::table('users')
        //                 ->leftjoin('company_connected_talent','company_connected_talent.id_user','=','users.id_user')
        //                 ->leftjoin('talent_proposals','talent_proposals.user_id','=','company_connected_talent.id_user')
        //                 ->leftjoin('projects','projects.id_project','=','talent_proposals.project_id')
        //                 ->select([
        //                     'users.id_user',
        //                     'users.email',
        //                     'users.name',
        //                     'company_connected_talent.id_talent_company',
        //                     \DB::raw("DATE_FORMAT(notice_expired,'%d %b %Y') as notice_expired"),
        //                     'talent_proposals.status',
        //                     'projects.id_project',
        //                     'projects.title',
        //                     'projects.user_id as emp_id' 
        //                 ])
        //                 // ->where('user_type','owner')
        //                 ->where('users.is_notice_period','=','Y')
        //                 ->where('users.notice_expired','<',date('Y-m-d'))
        //                 ->where('talent_proposals.status','=','accepted')
        //                 ->where('projects.project_status','=','initiated')
        //                 ->groupBy('users.id_user')
        //                 ->get();


        // dd($refundusers);

        // foreach ($refundusers as $key => $value) {
        //     // $updatejob = \DB::table('projects')->where('id_project','=',$value->id_project)->update(['status'=>'trashed']);
        //     $emp_users = \DB::table('users')->select('name','email')->where('id_user','=',$value->emp_id)->first();
        //     $email                  = $value->email;
        //     $emailData              = ___email_settings();
        //     $emailData['email']     = $email;
        //     $emailData['name']      = $value->name;
        //     $emailData['context']   = 'You Notice Period time has been expired & you have been disconnected & you can connect with the new talent.';

        //     $template_name = "transfer_job";

        //     // ___mail_sender($email,'',$template_name,$emailData); 
        //     // dd($emp_users,$value);
        //     // $delete = \Models\companyConnectedTalent::where('id_user','=',$value->id_user)->delete();
        //     /*$refund_transaction = \Models\Transactions::where(['transaction_status' => 'refunded-pending'])
        //     ->whereRaw('DATE(transaction_date) >= "'.date('Y-m-d').'"')
        //     ->whereNull('transaction_reference_id')->limit(10)->get();
            
        //     if(!empty($refund_transaction->count())){
        //         foreach ($refund_transaction as $key) {
        //             $result = \Models\PaypalPayment::refund([
        //                 'id_transactions'   => $key['id_transactions'],
        //                 'user_id'           => $key['transaction_user_id'],
        //                 'sale_id'           => $key['transaction_comment'], 
        //                 'refund_amount'     => $key['transaction_total']
        //             ]);
        //         }
        //     }*/
        // }

        $refundjob = \DB::table('users')
                        ->leftjoin('company_connected_talent','company_connected_talent.id_user','=','users.id_user')
                        ->leftjoin('talent_proposals','talent_proposals.user_id','=','company_connected_talent.id_user')
                        ->leftjoin('projects','projects.id_project','=','talent_proposals.project_id')
                        ->select([
                            'users.id_user',
                            'users.email',
                            'users.name',
                            'company_connected_talent.id_talent_company',
                            \DB::raw("DATE_FORMAT(notice_expired,'%d %b %Y') as notice_expired"),
                            'talent_proposals.status',
                            'projects.id_project',
                            'projects.title',
                            'projects.user_id as emp_id',
                            'projects.price_unit', 
                            'projects.price', 
                            'users.paypal_payer_id', 
                            // 'users.price', 
                        ])
                        // ->where('user_type','owner')
                        ->where('users.is_notice_period','=','Y')
                        ->where('users.notice_expired','<',date('Y-m-d'))
                        ->where('talent_proposals.status','=','accepted')
                        ->where('projects.project_status','=','initiated')
                        ->get();


        /*For Job Cancel & Refund to Employer*/
        #Fetch Job List, 
        if(count($refundjob) > 0){
            #Start Foreach For Job
            foreach ($refundjob as $key => $value) {
                $empdata = \DB::table('users')->select('users.id_user as empids','users.paypal_payer_id','users.email','users.name')->where('id_user',$value->emp_id)->first();
                // dd($empdata->paypal_payer_id);
                $currency = $value->price_unit;
                #Job Currency;
                $amount = $value->price;
                #Job Price;
                $receiver_MerchID = $empdata->paypal_payer_id;

                //Check PayPal mode, and change PayPal url according for Sandbox or Live.
                $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
                if(env('PAYPAL_ENV') == 'sandbox'){
                  $PayPal_BASE_URL = PayPal_BASE_URL_SANDBOX;
                }else{
                  $PayPal_BASE_URL = PayPal_BASE_URL_LIVE;
                }

                //cURL to generate access token
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $PayPal_BASE_URL . 'oauth2/token');
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_USERPWD, env('PAYPAL_CLIENT_ID') . ":" . env('PAYPAL_SECRET'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
                $result = curl_exec($ch);            
                $json = json_decode($result);
                $accessToken = $json->access_token;

                $random_string = __random_string();

                //cURL for Payout
                $curl = curl_init();
                $data = [
                          'sender_batch_header' => [
                              'email_subject' => "You have a refund for JobID- ".$value->id_project,
                              'sender_batch_id' => $random_string
                          ],
                          'items' => [
                              [
                                'recipient_type' => "PAYPAL_ID",
                                'amount' => [
                                    'value' => $amount,
                                    'currency' => $currency
                                ],
                                'receiver' => $receiver_MerchID,
                                'note' => 'Refund from Crowbar. Batch Id- '.$random_string,
                                'sender_item_id' => __random_string()
                              ],
                          ],
                        ];

                curl_setopt($curl, CURLOPT_URL, $PayPal_BASE_URL.'payments/payouts');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); 
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                  "Content-Type: application/json",
                  "Authorization: Bearer ".$accessToken) 
                );

                $response2 = curl_exec($curl);
                $response_arr = (array)json_decode(json_encode(json_decode($response2), 128));

                if(!empty($response_arr)){

                    #Send email to employer for jobcancellation and payment refund.
                    #PayPal Log

                    $email                  = $empdata->email;
                    $emailData              = ___email_settings();
                    $emailData['email']     = $email;
                    $emailData['name']      = $empdata->name;
                    $emailData['context']   = 'Please Cancel the job '.$value->title.' and the payment has been refunded to your account.';

                    $template_name = "transfer_job";

                     ___mail_sender($email,'',$template_name,$emailData);
                        
                }

                $sale_data = [
                    'transaction_user_id'           => $empdata->empids,
                    'transaction_company_id'        => $empdata->empids,
                    'transaction_user_type'         => 'employer',
                    'transaction_project_id'        => $value->id_project,
                    'transaction_proposal_id'       => NULL,
                    'transaction_total'             => $amount,
                    'transaction_subtotal'          => $amount,
                    'currency'                      => $currency,
                    'transaction_source'            => 'paypal',
                    'transaction_reference_id'      => $receiver_MerchID,
                    'transaction_comment'           => 'Payment to validate your card.',
                    'transaction_type'              => 'credit',
                    'transaction_status'            => 'refunded',
                    'transaction_date'              => date('Y-m-d',strtotime("+ ".REFUNDABLE_DATE_MARGIN."days")),
                    'transaction_actual_date'       => NULL,
                    'transaction_commission'        => NULL,
                    'transaction_commission_type'   => NULL,
                    'transaction_paypal_commission' => 0,
                    'raise_dispute_commission'      => NULL,
                    'raise_dispute_commission_type' => NULL,
                    'transaction_done_by'           => -1,
                    'updated'                       => date('Y-m-d h:i:s'),
                    'created'                       => date('Y-m-d h:i:s')
                ];
                $isPaid = \Models\PaypalPayment::save_transaction($sale_data);
                
                $updatejob = \DB::table('projects')->where('id_project','=',$value->id_project)->update(['status'=>'trashed']);
                $delete = \Models\companyConnectedTalent::where('id_user','=',$value->id_user)->delete();
                $updateUser = \DB::table('users')->where('id_user',$value->id_user)->update(['is_notice_period'=>'N','notice_expired'=>'']);

                return $response_arr;
            }
        }else{
            print_r("No user found");exit;
        }
    }
}
