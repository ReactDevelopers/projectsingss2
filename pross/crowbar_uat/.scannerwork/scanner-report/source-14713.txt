<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class Payments extends Model{
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];

        public function __construct(){
    	   
        }

        /**
         * [This method is used for braintree response] 
         * @param [Varchar]$data [Used for Data]
         * @return Boolean
         */ 

        public static function  braintree_response($data){
            $table_api_braintree_response = DB::table('api_braintree_response');

            if(!empty($data)){
                $isInserted = $table_api_braintree_response->insert($data); 
            }
            return (bool)$isInserted;            
        }

        /**
         * [This method is used to save credit card] 
         * @param [Integer]$card_card[Used for card card]
         * @return Data Response
         */ 

        public static function save_credit_card($credit_card){
            $table_user_card = DB::table('user_card');
            if(!empty($credit_card)){
                $isInserted = $table_user_card->insertGetId($credit_card); 
            }
            return $isInserted;              
        }

        /**
         * [This method is used to mark default card ] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Varchar]$card_id[Used for card id]
         * @return Boolean
         */ 
        
        public static function mark_card_default($user_id,$card_id = NULL){
            $isUpdated = DB::table('user_card')->where('user_id',$user_id)->update(['default' => DEFAULT_NO_VALUE, 'updated' => date('Y-m-d H:i:s')]);

            if(!empty($card_id)){
                return DB::table('user_card')
                ->where('id_card',$card_id)
                ->where('user_id',$user_id)
                ->update([
                    'default' => DEFAULT_YES_VALUE, 
                    'updated' => date('Y-m-d H:i:s')
                ]);
            }else{
                $selected = DB::table('user_card')
                ->select(['id_card'])
                ->where('user_id',$user_id)
                ->where('card_status','active')
                ->orderBy('updated','DESC')
                ->get()
                ->first();

                if(!empty($selected)){
                    return DB::table('user_card')
                    ->where('user_id',$user_id)
                    ->where('id_card',$selected->id_card)
                    ->update([
                        'default' => DEFAULT_YES_VALUE, 
                        'updated' => date('Y-m-d H:i:s')
                    ]);
                }else{
                    return false;
                }
            }
        }

        /**
         * [This method is used to get user card] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Integer]$card_id[Used for card id]
         * @param [Integer]$fetch[Used for fetching]
         * @param [Varchar]$keys[Used for key]
         * @return Data Response
         */ 
        
        public static function get_user_card($user_id,$card_id="",$fetch = 'array',$keys=['*']){
            $table_user_card = DB::table('user_card');
            $table_user_card->select($keys);
            $table_user_card->where(['card_status' => 'active']);
            if(!empty($user_id)){
                $table_user_card->where(['user_id' => $user_id]);
            }
            if(!empty($card_id)){
            	$table_user_card->where(['id_card' => $card_id]);
            }
            $table_user_card->orderBy('id_card','DESC');
            if($fetch === 'array'){
                return json_decode(
                    json_encode(
                        $table_user_card->get()
                    ),
                    true
                );
            }else if($fetch === 'first'){
                return json_decode(
                    json_encode(
                        $table_user_card->first()
                    ),
                    true
                );
            }else{
                return $table_user_card->get();
            }
        }

        /**
         * [This method is used to get user default card] 
         * @param [Integer]$user_id[Used for user id]
         * @param [Varchar]$keys[Used for key]
         * @return Json Response
         */ 

        public static function get_user_default_card($user_id,$keys=['*']){
            if(empty($user_id)){
                return [];
            }

            $table_user_card = DB::table('user_card');
            $table_user_card->select($keys);
            $table_user_card->where(['card_status' => 'active']);
        
            $table_user_card->where(['user_id' => $user_id]);
            $table_user_card->where(['default' => DEFAULT_YES_VALUE]);
            
            return json_decode(json_encode($table_user_card->get()->first()),true);
        }

        /**
         * [This method is used for card deletion] 
         * @param [Integer]$card_id[Used for card id]
         * @return Boolean
         */ 

        public static function delete_card($card_id){
            $table_user_card = DB::table('user_card');
            if($card_id){
                $table_user_card->where('id_card','=',$card_id);
                $isUpdated = $table_user_card->update(['card_status'=>'trashed']);
            }
            return (bool)$isUpdated;
        }

        /**
         * [This method is used for to save transaction] 
         * @param [type]$transaction_data[Used for transaction data]
         * @return Data Response
         */ 

        public static function save_transaction($transaction_data){
            $table_transaction = DB::table('transactions');

            if(!empty($transaction_data)){
                $isInserted = $table_transaction->insertGetId($transaction_data); 
            }
            return $isInserted;
        }

        /**
         * [This method is used for is_payment_already_escrowed] 
         * @param [Integer]$project_id[Used for project id]
         * @return Data Response
         */ 

        public static function is_payment_already_escrowed($project_id){
            return \DB::table('transactions')
            ->select(['id_transactions'])
            ->where('transaction_project_id',$project_id)
            ->where('transaction_user_type','employer')
            ->where('transaction_type','debit')
            ->whereNotIn('transaction_status',['failed','initiated'])
            ->get()->count();
        }

        /**
         * [This method is used for init_employer_payment] 
         * @param [varchar]$data[Used for data]
         * @param [type]$repeat[Used for repeat]
         * @return Data Response
         */ 
        
        public static function init_employer_payment($data){
            
            $table_transaction = DB::table('transactions');

            if(!empty($data)){
                $data['created'] = date('Y-m-d H:i:s');
                $data['updated'] = date('Y-m-d H:i:s');
                
                unset($data['price_unit']);
                $isInserted = $table_transaction->insertGetId($data);

                if(!empty($isInserted)){
                    $table_project = DB::table('projects');
                    $table_project->where('id_project',$data['transaction_project_id']);
                    
                    $project_detail = $table_project->select(['transaction'])->get()->first();

                    $repeat = 'on-completion';
                    $isUpdated = $table_project->update(['transaction' => $repeat, 'updated' => date('Y-m-d H:i:s')]);

                    if(!empty($isUpdated)){
                        return $table_transaction->where('id_transactions',$isInserted)->get()->first();
                    }else{
                        return false;
                    }
                }else{
                    return false;
                } 
            }else{
                return false;
            }
        }

        /**
         * [This method is used for init_talent_payment] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$transaction_by[Used for transaction_by]
         * @return Data Response
         */ 

        public static function init_talent_payment($project_id,$transaction_by){
            $table_transaction = DB::table('transactions');
            $data = \Models\Payments::get_talent_payout_details($project_id);

            if(!empty($data)){
                $data['created'] = date('Y-m-d H:i:s');
                $data['updated'] = date('Y-m-d H:i:s');
                
                if(!empty($data['accepted_talent_email'])){
                    $employer_project_payment       = \Models\Transactions::where('transaction_project_id',$project_id)->select(['transaction_subtotal','transaction_reference_id'])->get()->first();

                    // Check if this talent has a coupon code which is not expired
                    $talent_user_id = $data['transaction_user_id'];
                    $coupon_result = \Models\Coupon::verifyCoupon($talent_user_id, $project_id);

                    $discounted_commision = 0;
                    if($coupon_result){
                      $calculated_commission = ___talent_commission($data['transaction_subtotal']);
                      $get_discount = \Models\Coupon::getAppliedCouponDiscount($talent_user_id, $project_id);
                      $get_discount = number_format($get_discount['discount'],2);

                      $discounted_commision = ($get_discount/100)*$calculated_commission;
                    }else{
                      $discounted_commision = ___talent_commission($data['transaction_subtotal']);
                      $calculated_commission = 0;
                    }

                    $refundable_amount  = $employer_project_payment->transaction_subtotal - ($data['transaction_subtotal']+$calculated_commission);

                    /*To check saved Pay Commision for this proposal*/
                    $check_pay_commision_percentage = '0.00';
                    $check_pay_commision_percentage = $data['pay_commision_percent'];

                    if($check_pay_commision_percentage != '0.00'){
                      /*Get % specified by admin.*/
                      $check_pay_commision_percentage = number_format($check_pay_commision_percentage,2);
                      /*Subtract the % specified in manual payment system by admin for this proposal*/
                      $discounted_commision = $discounted_commision - (($check_pay_commision_percentage/100)*$discounted_commision) ;
                      $discounted_commision = number_format($discounted_commision,2);
                    }else{
                      $discounted_commision = 0;
                    }


                    /*25 oct by 455 Transafer amount to company owner when talent connected with a company*/
                    if(!empty($data['company_id'])){
                      $companyOwner = \Models\companyConnectedTalent::select('id_user')->with(['user'])->where('id_talent_company',$data['company_id'])->where('user_type','owner')->first();
                      $data['accepted_talent_merchant_id'] = $companyOwner->user->paypal_payer_id;
                    }else{
                      $data['accepted_talent_merchant_id'] = $data['accepted_talent_merchant_id'] ;
                    }

                    /*To check if project payment type is 'System' and talent payout(by PayPal) will happen */
                    if($data['project_payment_type'] == 'system'){
                      $result = \Models\Payments::transfer_payment_talent_payout(
                                  $data['accepted_talent_email'],
                                  $data['accepted_talent_merchant_id'],
                                  $data['transaction_subtotal'],
                                  $discounted_commision,
                                  $data['transaction_user_id'],
                                  $project_id,$data['price_unit']
                                );
                    }else{
                      $result =  array();
                      $result['batch_header'] = 'Test';
                    }

                    unset($data['accepted_talent_merchant_id']);

                    if($refundable_amount > 0){
                        $refundable_data = [
                            'transaction_user_id'       => $data['transaction_company_id'],
                            'transaction_company_id'    => $data['transaction_company_id'],
                            'transaction_user_type'     => 'employer',
                            'transaction_project_id'    => $project_id,
                            'transaction_comment'       => $employer_project_payment->transaction_reference_id,
                            'transaction_proposal_id'   => $data['transaction_proposal_id'],
                            'transaction_total'         => $refundable_amount,
                            'transaction_subtotal'      => $refundable_amount,
                            'transaction_date'          => date('Y-m-d',strtotime("+ ".REFUNDABLE_DATE_MARGIN."days")),
                            'currency'                  => DEFAULT_CURRENCY,
                            'transaction_type'          => 'credit',
                            'transaction_status'        => 'refunded-pending',
                            'transaction_done_by'       => -1,
                            'updated'                   => date('Y-m-d H:i:s'),
                            'created'                   => date('Y-m-d H:i:s'),
                        ];

                        $idRefunded = \Models\Transactions::insertGetId($refundable_data);
                    }

                    if($data['project_payment_type'] == 'system'){
                      $payout_batch_id = $result['batch_header']->payout_batch_id;
                    }else{
                      $payout_batch_id = '-';
                    } 

                    unset($data['accepted_talent_email']);
                    unset($data['price_unit']);
                    unset($data['project_payment_type']);
                    unset($data['accept_escrow']);
                    unset($data['pay_commision_percent']);
                    
                    $data['transaction_reference_id'] = $payout_batch_id; 
                    $data['transaction_done_by'] = $transaction_by;
                    $data['transaction_commission_type'] = ___cache('configuration')['commission_type'];
                    $data['transaction_commission'] = $calculated_commission;

                    if(empty($result['batch_header'])){
                        $data['transaction_status'] = 'failed';
                        $isInserted = $table_transaction->insertGetId($data);
                        return false;
                    }else{
                        // $data['transaction_status'] = 'confirmed';
                        $data['transaction_status'] = 'completed';
                        $data['transaction_subtotal'] = $data['transaction_subtotal'] + $discounted_commision;
                        $data['transaction_total'] = $data['transaction_total'] + $discounted_commision;
                        $isInserted = $table_transaction->insertGetId($data);
                        
                        if(!empty($isInserted)){
                            return $table_transaction->where('id_transactions',$isInserted)->get()->first();
                        }else{
                            return false;
                        }
                    }
                }else{
                    return false;    
                }
            }else{
                return false;
            }
        }

        /**
         * [This method is used for refund transaction] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Varchar]$data[Used for data]         
         * @return Boolean
         */ 

        public static function refund_transaction($project_id, $data){
            
            $table_transaction = DB::table('transactions');

            if(!empty($data)){
                $data['created'] = date('Y-m-d H:i:s');
                $data['updated'] = date('Y-m-d H:i:s');
                $isInserted = $table_transaction->insertGetId($data);

                if(!empty($isInserted)){
                    return true;
                }else{
                    return false;
                } 
            }else{
                return false;
            }
        }

        /**
         * [This method is used for dispute upcoming payment] 
         * @param [Integer]$project_id[Used for project id]     
         * @return Data Response
         */ 

        public static function dispute_upcoming_payment($project_id){

            $isDisputed = DB::table('project_log')
            ->where('project_id',$project_id)
            ->whereNotNull('project_log.enddate')
            ->where('close','pending')
            ->update([
                'close' => 'disputed',
                'updated' => date('Y-m-d')
            ]);
        }

        /**
         * [This method is used to update transaction] 
         * @param [Integer]$transaction_id[Used for user id]
         * @param [Varchar]$data[Used for data]         
         * @return Data Response
         */ 

        public static function update_transaction($transaction_id,$data){

            $table_transaction = DB::table('transactions');

            if(!empty($data)){
                return $table_transaction->where('id_transactions',$transaction_id)->update($data);
            }else{
                return false;
            }
        }

        /**
         * [This method is used to update transaction by transaction id]
         * @return Data Response
         */ 

        public static function update_transactionByIds($job_id,$proposal_id){

            $table_transaction = DB::table('transactions');

            if(!empty($job_id) && !empty($proposal_id)){
                return $table_transaction
                        ->where('transaction_project_id',$job_id)
                        ->where('transaction_proposal_id',$proposal_id)
                        ->update(['transaction_status' => 'confirmed',
                                  'updated' => date('Y-m-d H:i:s')
                                ]);
            }else{
                return false;
            }
        }

        /**
         * [This method is used for summary] 
         * @param [Integer]$user_id[Used for user id]
         * @param [type]$type[Used for type]
         * @param [Integer]$result[Used for result]
         * @return Data Response
         */ 

        public static function summary($user_id,$type,$result = []){
            $total_received         = self::total_received($user_id,$type);
            $total_due              = self::total_due($user_id);
            $total_completed_job    = \Models\Projects::total_completed_job_by_talent($user_id);

            if($type == 'talent'){
                $result = [
                    'total_received'        => $total_received,
                    'total_due'             => $total_due,
                    'total_completed_job'   => $total_completed_job,
                    'payments' => [
                        \Models\Payments::listing($user_id,$type,'all',true),
                        \Models\Payments::listing($user_id,$type,'received',true),
                        \Models\Payments::listing($user_id,$type,'disputed',true)
                    ]
                ];                
            }else if($type == 'employer'){
                $total_balance = self::total_balance($user_id);
                $total_paid = self::total_paid($user_id,$type);
                $total_posted_jobs = \Models\Projects::total_posted_job_by_employer($user_id);
                
                $result = [
                    'total_paid' => $total_paid,
                    'total_balance' => $total_balance,
                    'total_posted_jobs' => $total_posted_jobs,
                    'payments' => [
                        \Models\Payments::listing($user_id,$type,'all',true),
                        \Models\Payments::listing($user_id,$type,'paid',true),
                        \Models\Payments::listing($user_id,$type,'refunded',true)
                    ]
                ];
            }

            return $result;
        }

        /**
         * [This method is used for listing] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$listing[Used for listing]
         * @param [type]$type[Used for type]
         * @param [Integer]$count[Used for Count]
         * @param [Integer]$page[Used for project id]
         * @param [Integer]$sort[Used for sorting]
         * @param [Integer]$search[Used for searching]
         * @param [Integer]$limit[Used for limit]
         * @return Data Response
         */ 

        public static function listing($user_id, $listing = null, $type = null, $count = false, $page = NULL, $sort = NULL, $search = NULL, $limit = DEFAULT_PAGING_LIMIT){
            $prefix = DB::getTablePrefix();
            $type = (!empty($type))?$type:'all';

            $table_transaction = DB::table('transactions');

            if($listing === 'employer'){
                if($type == 'all'){
                    return self::employer_payments_due(\Auth::user()->id_user,NULL,$count);
                }else if($type == 'paid'){
                    $table_transaction->where('transaction_status','confirmed');    
                }else if($type == 'refunded'){
                    $table_transaction->whereIn('transaction_status',['refunded','refunded-pending']);    
                }

                if(!empty($search)){
                    $table_transaction->where('projects.title', 'like', "%$search%");
                }

                if(!empty($sort)){
                    $sorting = ___decodefilter($sort);
                    $table_transaction->orderByRaw($sorting);
                }

                if(!empty($page)){
                    $offset = ($page - 1)*$limit;

                    $table_transaction->offset($offset);
                    $table_transaction->limit($limit);
                }

                $payments = $table_transaction->select([
                    /*TRANSACTIONS*/
                    'transactions.id_transactions',
                    'transactions.transaction_user_id',
                    'transactions.transaction_project_id',
                    'transactions.transaction_proposal_id',
                    #'transactions.transaction_subtotal',
                    \DB::Raw('`CONVERT_PRICE`('.$prefix.'transactions.transaction_subtotal, '.$prefix.'transactions.currency, "'.request()->currency.'") AS transaction_subtotal'),
                    \DB::Raw("'".___cache('currencies')[request()->currency]."' as currency"),

                    'transactions.transaction_reference_id',
                    'transactions.transaction_type',
                    'transactions.transaction_status',
                    'transactions.transaction_date',
                    'transactions.created',
                    /*JOBS*/
                    'projects.title',
                    'projects.employment as transaction',
                    /*EMPLOYERS*/  
                    'employers.company_name', 
                    /*PROPOSALS*/
                    #'proposals.quoted_price',
                    \DB::Raw('`CONVERT_PRICE`('.$prefix.'proposals.quoted_price, '.$prefix.'proposals.price_unit, "'.request()->currency.'") AS quoted_price'),
                ])
                ->leftjoin("projects","projects.id_project", "=", "transactions.transaction_project_id")
                ->leftjoin("talent_proposals as proposals","proposals.id_proposal", "=", "transactions.transaction_proposal_id")
                ->leftjoin("users as employers","employers.id_user", "=", "transactions.transaction_user_id")
                ->where('transaction_user_id',$user_id)
                ->where('transaction_user_type',$listing)
                ->where('projects.employment','!=','fulltime');

                if(empty($count)){
                    return $payments->get();
                }else{
                    return $payments->get()->count();
                }
            }else if($listing === 'talent'){
                if($type == 'all'){
                    return self::talent_upcoming_payment(NULL,$user_id,$count);
                }else if($type == 'received'){
                    $table_transaction->where('transaction_status','confirmed');    
                }else if($type == 'disputed'){
                    return self::talent_disputed_payment(NULL,$user_id,$count);
                }

                if(!empty($search)){
                    $table_transaction->where('projects.title', 'like', "%$search%");
                }

                if(!empty($sort)){
                    $sorting = ___decodefilter($sort);
                    $table_transaction->orderByRaw($sorting);
                }

                if(!empty($page)){
                    $offset = ($page - 1)*$limit;

                    $table_transaction->offset($offset);
                    $table_transaction->limit($limit);
                }

                $payments = $table_transaction->select([
                    /*TRANSACTIONS*/
                    'transactions.id_transactions',
                    'transactions.transaction_user_id',
                    'transactions.transaction_project_id',
                    'transactions.transaction_proposal_id',
                    \DB::Raw('`CONVERT_PRICE`('.$prefix.'transactions.transaction_subtotal, '.$prefix.'transactions.currency, "'.request()->currency.'") AS transaction_subtotal'),
                    \DB::Raw("'".___cache('currencies')[request()->currency]."' as currency"),
                    'transactions.transaction_reference_id',
                    'transactions.transaction_type',
                    'transactions.transaction_status',
                    'transactions.transaction_date',
                    'transactions.created',
                    /*JOBS*/
                    'projects.title',
                    'projects.employment as transaction',
                    /*EMPLOYERS*/  
                    'employers.company_name', 
                    /*PROPOSALS*/
                    'proposals.quoted_price',
                ])
                ->leftjoin("projects","projects.id_project", "=", "transactions.transaction_project_id")
                ->leftjoin("talent_proposals as proposals",function($leftjoin){
                    $leftjoin->on("proposals.id_proposal", "=", "transactions.transaction_proposal_id");
                    $leftjoin->on("proposals.user_id", "=", \DB::raw(\Auth::user()->id_user));
                })
                ->leftjoin("users as employers","employers.id_user", "=", "projects.user_id")
                ->where('transaction_user_id',$user_id);

                if(empty($count)){
                    return $payments->get();
                }else{
                    return $payments->get()->count();
                }
            }else{
                $prefix = DB::getTablePrefix();
            
                \DB::statement(\DB::raw('set @row_number=0'));
                $payments = $table_transaction->select([
                    /*TRANSACTIONS*/
                    \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                    'transactions.id_transactions',
                    'transactions.transaction_user_id',
                    'transactions.transaction_project_id',
                    'transactions.transaction_proposal_id',
                    \DB::Raw('`CONVERT_PRICE`('.$prefix.'transactions.transaction_subtotal, '.$prefix.'transactions.currency, "'.request()->currency.'") AS transaction_subtotal'),
                    \DB::Raw("'".___cache('currencies')[request()->currency]."' as currency"),
                    'transactions.transaction_reference_id',
                    'transactions.transaction_type',
                    'transactions.transaction_status',
                    'transactions.transaction_date',
                    'transactions.created',
                    /*JOBS*/
                    'projects.title',
                    'projects.transaction',
                    /*EMPLOYERS*/  
                    'employers.company_name', 
                    /*PROPOSALS*/
                    'proposals.quoted_price',
                    "users.type as user_type",
                    \DB::Raw("TRIM(CONCAT({$prefix}users.first_name,' ',{$prefix}users.last_name)) as transaction_user_name"),
                ])
                ->leftjoin("projects","projects.id_project", "=", "transactions.transaction_project_id")
                ->leftjoin("talent_proposals as proposals",function($leftjoin){
                    $leftjoin->on("proposals.id_proposal", "=", "transactions.transaction_proposal_id");
                })
                ->leftjoin("users as employers","employers.id_user", "=", "projects.user_id")
                ->leftjoin("users","users.id_user", "=", "transactions.transaction_user_id")
                ->where('transaction_project_id',$user_id);

                if(empty($count)){
                    return $payments->get();
                }else{
                    return $payments->get()->count();
                }
            }
        }

        /**
         * [This method is used for user's upcoming payment] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$count[Used for Count]
         * @return Data Response
         */ 

        public static function talent_upcoming_payment($project_id = NULL,$user_id=NULL,$count = false){
            $prefix             = DB::getTablePrefix();
            $current_date       = date('Y-m-d');
            $commission         = ___cache('configuration')['commission'];
            $commission_type    = ___cache('configuration')['commission_type'];

            \DB::statement(\DB::raw('set @row_number=0'));
            $table_projects = DB::table('projects');
            
            $payments = $table_projects->select([
                \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                'projects.id_project as transaction_project_id',
                'projects.title',
                'projects.enddate',
                'employers.company_name',
                'proposals.payment as transaction_status',
                \DB::Raw("DATE(DATE_ADD({$prefix}projects.enddate, INTERVAL ".(RAISE_DISPUTE_DATE_LIMIT*2)." HOUR)) as transaction_date"),
                'projects.employment as transaction',
                'projects.employment',
                \DB::Raw("'yes' as request_payout"),
                \DB::Raw("
                    `DEDUCT_COMMISSION`(
                        IF(
                            {$prefix}projects.employment = 'hourly',
                            (ABS(HOUR(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00')))+ABS(MINUTE(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00'))/60)+ABS(SECOND(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00'))/3600)) * `CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."'),
                            IF(
                                {$prefix}projects.employment = 'monthly',
                                ((DATEDIFF({$prefix}projects.enddate,{$prefix}projects.startdate)+1)/".MONTH_DAYS.")*`CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."'),
                                `CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."')
                            )
                        ),
                        {$commission},
                        '{$commission_type}'
                    ) as quoted_price
                "),
                \DB::Raw("'".___cache('currencies')[request()->currency]."' as currency"),
                // \DB::Raw("IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}project_log.worktime))),'00:00:00') as working_hours"),
                \DB::Raw("
                    IF(
                        DATE({$prefix}projects.enddate) < DATE('{$current_date}'),
                        'closed',
                        IF(
                            ({$prefix}projects.project_status = 'closed' && {$prefix}projects.closedate IS NULL),
                            'completed',
                            {$prefix}projects.project_status
                        )
                    ) as project_status
                ")
            ]);
            
            $table_projects->leftjoin("users as employers","employers.id_user", "=", "projects.user_id");
            // $table_projects->leftjoin("project_log",function($leftjoin){
            //     $leftjoin->on("project_log.project_id", "=", "projects.id_project");
            // });
            $table_projects->leftjoin("talent_proposals as proposals",function($leftjoin) use($user_id){
                $leftjoin->on("proposals.project_id", "=", "projects.id_project");
                $leftjoin->on("proposals.status", "=", \DB::Raw('"accepted"'));
                
                if(!empty($user_id)){
                    $leftjoin->on("proposals.user_id", "=", \DB::Raw("{$user_id}"));     
                }
            });

            if(!empty($project_id)){
                $table_projects->where('id_project',$project_id);
            }

            $table_projects->where('proposals.payment','pending');
            $table_projects->having('project_status','!=','pending');
            // $table_projects->groupBy(['project_log.project_id']);

            if(empty($count)){
                return $payments->get();
            }else{
                return $payments->get()->count();
            }
        }

        /**
         * [This method is used for employer payment due] 
         * @param [Integer]$employer_id[Used for employer id]
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$count[Used for Count]
         * @return Data Response
         */ 

        public static function employer_payments_due($employer_id, $project_id = NULL, $count = false){
            $prefix = DB::getTablePrefix();

            \DB::statement(\DB::raw('set @row_number=0'));
            
            $payments = \Models\Projects::projectStatus()->addSelect([
                \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                'projects.id_project as transaction_project_id',
                'projects.title',
                'employers.company_name',
                \DB::Raw("'pending' as transaction_status"),
                \DB::Raw("DATE(DATE_ADD({$prefix}projects.enddate, INTERVAL ".(RAISE_DISPUTE_DATE_LIMIT*2)." HOUR)) as transaction_date"),
                'projects.employment as transaction',
                'projects.employment',
                \DB::Raw("'yes' as request_payout"),
                \DB::Raw("
                    IF(
                        {$prefix}projects.employment = 'hourly',
                        (ABS(HOUR(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00')))+ABS(MINUTE(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00'))/60)+ABS(SECOND(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00'))/3600)) * `CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."'),
                        IF(
                            {$prefix}projects.employment = 'monthly',
                            ((DATEDIFF({$prefix}projects.enddate,{$prefix}projects.startdate)+1)/".MONTH_DAYS.")*`CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."'),
                            `CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."')
                        )
                    )
                    as quoted_price
                "),
                \DB::Raw("'".___cache('currencies')[request()->currency]."' as currency"),
                \DB::Raw("IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}project_log.worktime))),'00:00:00') as working_hours"),
            ]);
            
            $payments->leftjoin("users as employers","employers.id_user", "=", "projects.user_id");
            $payments->leftjoin("project_log",function($leftjoin){
                $leftjoin->on("project_log.project_id", "=", "projects.id_project");
            });
            
            $payments->leftjoin("talent_proposals as proposals",function($leftjoin){
                $leftjoin->on("proposals.project_id", "=", "projects.id_project");
                $leftjoin->on("proposals.status", "=", \DB::Raw('"accepted"'));
            });
            
            $payments->leftjoin("transactions",function($leftjoin){
                $leftjoin->on("transactions.transaction_project_id", "=", "projects.id_project");
                $leftjoin->on("transactions.transaction_type", "=", \DB::Raw("'credit'"));
            });

            if(!empty($project_id)){
                $payments->where('id_project',$project_id);
            }

            $payments->where('projects.user_id',$employer_id);
            $payments->where('proposals.payment','pending');

            $payments->whereNull('transactions.id_transactions');
            $payments->having('project_status','=','closed');
            $payments->groupBy(['project_log.project_id']);

            if(empty($count)){
                return $payments->get();
            }else{
                return $payments->get()->count();
            }
        }

        /**
         * [This method is used for user's disputed payment] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Integer]$count[Used for Count]
         * @return Data Response
         */ 

        public static function talent_disputed_payment($project_id = NULL,$user_id = NULL, $count = false){
            $prefix = DB::getTablePrefix();
            $commission         = ___cache('configuration')['commission'];
            $commission_type    = ___cache('configuration')['commission_type'];
   
            \DB::statement(\DB::raw('set @row_number=0'));
            $table_projects = DB::table('projects');
            
            $payments = $table_projects->select([
                \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                'projects.id_project as transaction_project_id',
                'projects.title',
                'employers.company_name',
                'proposals.payment as transaction_status',
                'projects.enddate as transaction_date',
                'transaction',
                'projects.employment as transaction',
                'projects.employment',
                \DB::Raw("'yes' as request_payout"),
                \DB::Raw("
                    `DEDUCT_COMMISSION`(
                        IF(
                            {$prefix}projects.employment = 'hourly',
                            (ABS(HOUR(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00')))+ABS(MINUTE(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00'))/60)+ABS(SECOND(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00'))/3600)) * `CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."'),
                            IF(
                                {$prefix}projects.employment = 'monthly',
                                ((DATEDIFF({$prefix}projects.enddate,{$prefix}projects.startdate)+1)/".MONTH_DAYS.")*`CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."'),
                                `CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."')
                            )
                        ),
                        {$commission},
                        '{$commission_type}'
                    ) as quoted_price
                "),
                \DB::Raw("'".___cache('currencies')[request()->currency]."' as currency"),
                \DB::Raw("IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}project_log.worktime))),'00:00:00') as working_hours"),
            ]);
            
            $table_projects->leftjoin("users as employers","employers.id_user", "=", "projects.user_id");
            $table_projects->leftjoin("project_log","project_log.project_id", "=", "projects.id_project");
            $table_projects->leftjoin("talent_proposals as proposals",function($leftjoin) use($user_id){
                $leftjoin->on("proposals.project_id", "=", "projects.id_project");
                $leftjoin->on("proposals.status", "=", \DB::Raw('"accepted"'));

                if(!empty($user_id)){
                    $leftjoin->on("proposals.user_id", "=", \DB::Raw("{$user_id}"));     
                }
            });

            if(!empty($project_id)){
                $table_projects->where('id_project',$project_id);
            }

            $table_projects->where('proposals.payment','disputed');
            $table_projects->groupBy(['project_log.project_id']);

            if(empty($count)){
                return $payments->get();
            }else{
                return $payments->get()->count();
            }
        }

        /**
         * [This method is used for transfer] 
         * @param [Integer]$project_id[Used for project id]
         * @param [type]$fetch[Used for fetching]
         * @return Data Response
         */ 

        public static function transfers($project_id,$fetch = 'object'){

            $table_transaction = DB::table('transactions');

            $payments = $table_transaction->select([
                /*TRANSACTIONS*/
                'transactions.id_transactions',
                'transactions.transaction_user_id',
                'transactions.transaction_project_id',
                'transactions.transaction_proposal_id',
                'transactions.transaction_subtotal',
                'transactions.transaction_reference_id',
                'transactions.transaction_type',
                'transactions.transaction_status',
                'transactions.transaction_date',
                'transactions.created',
                /*JOBS*/
                'projects.title',
                'projects.transaction',
                /*EMPLOYERS*/  
                'employers.company_name', 
                /*PROPOSALS*/
                'proposals.quoted_price',
            ])
            ->leftjoin("projects","projects.id_project", "=", "transactions.transaction_project_id")
            ->leftjoin("talent_proposals as proposals","proposals.id_proposal", "=", "transactions.transaction_proposal_id")
            ->leftjoin("users as employers","employers.id_user", "=", "transactions.transaction_user_id")
            ->where('transaction_type','credit')
            ->where('transaction_project_id',$project_id);

            if($fetch == 'object'){
                return $payments->get();
            }
        }

        /**
         * [This method is used to get talent payout in details] 
         * @param [Integer]$project_id[Used for project id]
         * @param [Enum]$transaction_status[Used for transaction status]
         * @return Data Response
         */ 

        public static function get_talent_payout_details($project_id,$transaction_status = NULL){
            $prefix                                     = \DB::getTablePrefix();
            $commission         = ___cache('configuration')['commission'];
            $commission_type    = ___cache('configuration')['commission_type'];
            
            if(empty($transaction_status)){
                $transaction_status = 'initiated';
            }

            $project   = \Models\Projects::defaultKeys()->with([
                'proposal' => function($q) use($prefix,$commission,$commission_type){
                    $q->addSelect([
                        'id_proposal',
                        'talent_proposals.project_id',
                        'talent_proposals.user_id',
                        'talent_proposals.working_hours',
                        'talent_proposals.price_unit',
                        'talent_proposals.accept_escrow',
                        'talent_proposals.pay_commision_percent',
                        'projects.payment_type as project_payment_type',
                        \DB::Raw("
                            `DEDUCT_COMMISSION`(
                                IF(
                                    {$prefix}projects.employment = 'hourly',
                                    (ABS(HOUR(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}talent_proposals.working_hours))),'00:00:00')))+ABS(MINUTE(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}talent_proposals.working_hours))),'00:00:00'))/60)+ABS(SECOND(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}talent_proposals.working_hours))),'00:00:00'))/3600)) * `CONVERT_PRICE`({$prefix}talent_proposals.quoted_price, {$prefix}talent_proposals.price_unit, '".DEFAULT_CURRENCY."'),
                                    IF(
                                        {$prefix}projects.employment = 'monthly',
                                        ((DATEDIFF({$prefix}projects.enddate,{$prefix}projects.startdate)+1)/".MONTH_DAYS.")*`CONVERT_PRICE`({$prefix}talent_proposals.quoted_price, {$prefix}talent_proposals.price_unit, '".DEFAULT_CURRENCY."'),
                                        `CONVERT_PRICE`({$prefix}talent_proposals.quoted_price, {$prefix}talent_proposals.price_unit, '".DEFAULT_CURRENCY."')
                                    )
                                ),
                                {$commission},
                                '{$commission_type}'
                            ) as quoted_price
                        "),
                    ])
                    ->where('talent_proposals.status','accepted')
                    ->leftjoin('projects','projects.id_project','=','talent_proposals.project_id')
                    ->leftjoin('project_log','project_log.project_id','=','talent_proposals.project_id')
                    ->with([
                        'talent' => function($q){
                            $q->addSelect(['id_user','paypal_id','paypal_payer_id']);
                        },
                        'connectedCompany'
                    ]);
                }
            ])
            ->where('id_project',$project_id)
            // ->get()
            ->first();

            $get_project = $project->toArray(); 

            $transaction_comment = '';
            if(!empty($get_project['proposal']) && $get_project['proposal']['project_payment_type'] == 'manual'){
              $transaction_comment = sprintf(TRANSACTION_COMMENT_MANUAL,$project->proposal->talent->paypal_id);
            }else{
              $transaction_comment = sprintf(TRANSACTION_COMMENT,$project->proposal->talent->paypal_id,___d(date('Y-m-d H:i:s'))); 
            }

            return [
                'accepted_talent_email'       => $project->proposal->talent->paypal_id,
                'accepted_talent_merchant_id' => $project->proposal->talent->paypal_payer_id,
                'transaction_user_id'         => $project->proposal->talent->id_user,
                'transaction_company_id'      => $project->company_id,
                'transaction_user_type'       => 'talent',
                'transaction_project_id'      => $project->id_project,
                'transaction_proposal_id'     => $project->proposal->id_proposal,
                'transaction_total'           => ___format($project->proposal->quoted_price),
                'transaction_subtotal'        => ___format($project->proposal->quoted_price),
                'transaction_reference_id'    => ___get_transaction_id(),
                'transaction_comment'         => $transaction_comment,
                'transaction_type'            => 'credit',
                'transaction_status'          => $transaction_status,
                'transaction_date'            => date('Y-m-d H:i:s'),
                'price_unit'                  => $project->proposal->price_unit,
                'project_payment_type'        => $project->proposal->project_payment_type,
                'accept_escrow'               => $project->proposal->accept_escrow,
                'pay_commision_percent'       => $project->proposal->pay_commision_percent,
                'company_id'                  => @$project->proposal->connectedCompany->id_talent_company,
            ];
        }

        /**
         * [This method is used to create braintree customer] 
         * @param [Integer]$braintree_id[Used for braintree id]
         * @return Data Response
         */ 

        public static function create_braintree_customer($braintree_id){
            if(empty($braintree_id)){
                $add_customer_result = \Braintree_Customer::create(array(
                    'firstName'         => \Auth::user()->first_name,
                    'email'             => \Auth::user()->email
                ));

                if($add_customer_result->success){
                    \Models\Payments::braintree_response([
                        'user_id'                   => \Auth::user()->id_user,
                        'braintree_response_json'   => json_encode($add_customer_result),
                        'status'                    => 'true',
                        'type'                      => 'card',
                        'created'                   => date('Y-m-d H:i:s')
                    ]);

                    \Models\Employers::change(
                        \Auth::user()->id_user,
                        ['braintree_id' => $add_customer_result->customer->id]
                    ); 

                    return $add_customer_result->customer->id;
                }else{
                    \Models\Payments::braintree_response([
                        'user_id'                   => \Auth::user()->id_user,
                        'braintree_response_json'   => json_encode($add_customer_result->message),
                        'status'                    => 'false',
                        'type'                      => 'card',
                        'created'                   => date('Y-m-d H:i:s')
                    ]);

                    return false;
                }
            }else{
                try {
                    $customer = \Braintree_Customer::find($braintree_id);
                    
                    if(!empty($customer)){
                        return $braintree_id;
                    }else{
                        return false;
                    }
                } catch (\Braintree_Exception_NotFound $e) {
                    $add_customer_result = \Braintree_Customer::create(array(
                        'firstName'     => \Auth::user()->first_name,
                        'email'         => \Auth::user()->email,
                    ));

                    if($add_customer_result->success){
                        \Models\Payments::braintree_response([
                            'user_id'                   => \Auth::user()->id_user,
                            'braintree_response_json'   => json_encode($add_customer_result),
                            'status'                    => 'true',
                            'type'                      => 'card',
                            'created'                   => date('Y-m-d H:i:s')
                        ]);

                        \Models\Employers::change(
                            \Auth::user()->id_user,
                            ['braintree_id' => $add_customer_result->customer->id]
                        ); 

                        return $add_customer_result->customer->id;
                    }else{
                        \Models\Payments::braintree_response([
                            'user_id'                   => \Auth::user()->id_user,
                            'braintree_response_json'   => json_encode($add_customer_result->message),
                            'status'                    => 'false',
                            'type'                      => 'card',
                            'created'                   => date('Y-m-d H:i:s')
                        ]);

                        return false;
                    }
                }

            }
        }

        /**
         * [This method is used to create braintree card] 
         * @param Request
         * @param [Integer]$braintree_id[Used for braintree id]
         * @return Data Response
         */ 
        
        public static function create_braintree_card($request,$braintree_id){
            $get_user_default_card = self::get_user_default_card(\Auth::user()->id_user);
            $result = \Braintree_CreditCard::create(array(
                'cardholderName' => $request->cardholder_name,
                'customerId' => $braintree_id,
                'expirationDate' => (int)$request->expiry_month . '/' . (int)$request->expiry_year,
                'number' => $request->number,
                'cvv' => $request->cvv
            ));

            if($result->success == 1){
                \Models\Payments::braintree_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'braintree_response_json'   => json_encode((array)$result->creditCard),
                    'status'                    => 'true',
                    'type'                      => 'card',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

                $credit_card['default']                     = !empty($get_user_default_card) ? DEFAULT_NO_VALUE : DEFAULT_YES_VALUE;
                $credit_card['user_id']                     = \Auth::user()->id_user;
                $credit_card['type']                        = \Auth::user()->type;
                $credit_card['bin']                         = $result->creditCard->bin;
                $credit_card['expiration_month']            = $result->creditCard->expirationMonth;
                $credit_card['expiration_year']             = $result->creditCard->expirationYear;
                $credit_card['last4']                       = $result->creditCard->last4;
                $credit_card['card_type']                   = $result->creditCard->cardType;
                $credit_card['cardholder_name']             = $result->creditCard->cardholderName;
                $credit_card['commercial']                  = $result->creditCard->commercial;
                $credit_card['country_of_issuance']         = $result->creditCard->countryOfIssuance; 
                $credit_card['created_at']                  = $result->creditCard->createdAt->format('Y-m-d H:i:s');
                $credit_card['customer_id']                 = $result->creditCard->customerId;
                $credit_card['customer_location']           = $result->creditCard->customerLocation;
                $credit_card['debit']                       = $result->creditCard->debit;
                $credit_card['durbin_regulated']            = $result->creditCard->durbinRegulated;
                $credit_card['expired']                     = $result->creditCard->expired;
                $credit_card['healthcare']                  = $result->creditCard->healthcare;
                $credit_card['image_url']                   = $result->creditCard->imageUrl;
                $credit_card['issuing_bank']                = $result->creditCard->issuingBank;
                $credit_card['payroll']                     = $result->creditCard->payroll;
                $credit_card['prepaid']                     = $result->creditCard->prepaid;
                $credit_card['product_id']                  = $result->creditCard->productId;
                $credit_card['subscriptions']               = json_encode($result->creditCard->subscriptions);
                $credit_card['token']                       = $result->creditCard->token;
                $credit_card['unique_number_identifier']    = $result->creditCard->uniqueNumberIdentifier;
                $credit_card['updated_at']                  = $result->creditCard->updatedAt->format('Y-m-d H:i:s');
                $credit_card['venmo_sdk']                   = $result->creditCard->venmoSdk;
                $credit_card['verifications']               = json_encode($result->creditCard->verifications);
                $credit_card['billing_address']             = $result->creditCard->billingAddress;
                $credit_card['expiration_date']             = $result->creditCard->expirationDate;
                $credit_card['masked_number']               = $result->creditCard->maskedNumber;
                $credit_card['card_status']                 = 'active';
                $credit_card['updated']                     = date('Y-m-d H:i:s');
                $credit_card['created']                     = date('Y-m-d H:i:s');
                
                $isInserted = \Models\Payments::save_credit_card($credit_card);
                $session    = \Session::get('payment');
                
                if($isInserted){
                    return [
                        'message'   => "M0392",
                        'status'    => true,
                        'card'      => self::get_user_card(\Auth::user()->id_user,$isInserted,'first')
                    ];
                }else{
                    return [
                        'message'   => "M0356",
                        'status'    => false,
                        'card'      => []
                    ];
                }
            }else{
                \Models\Payments::braintree_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'braintree_response_json'   => json_encode($result->message),
                    'status'                    => 'false',
                    'type'                      => 'card',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

                return [
                    'message'   => $result->errors->deepAll()[0]->code,
                    'status'    => false,
                    'card'      => []
                ];
            } 
        }

        /**
         * [This method is used for one time payment token] 
         * @param Request
         * @param [Integer]$braintree_id[Used for braintree id]
         * @return Data Response
         */ 

        public static function one_time_payment_token($request,$braintree_id){
            $result = \Braintree_CreditCard::create(array(
                'cardholderName'    => $request->cardholder_name,
                'customerId'        => $braintree_id,
                'expirationDate'    => (int)$request->expiry_month . '/' . (int)$request->expiry_year,
                'number'            => $request->number,
                'cvv'               => $request->cvv
            ));

            if($result->success == 1){
                \Models\Payments::braintree_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'braintree_response_json'   => json_encode((array)$result->creditCard),
                    'status'                    => 'true',
                    'type'                      => 'card',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
                return [
                    'message'           => "M0392",
                    'status'            => true,
                    'card'              => [
                        'card_token'    => $result->creditCard->token,
                        'image_url'     => $result->creditCard->imageUrl,
                        'masked_number' => $result->creditCard->maskedNumber,
                        'last4'         => $result->creditCard->last4
                    ]
                ];
            }else{
                \Models\Payments::braintree_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'braintree_response_json'   => json_encode($result->message),
                    'status'                    => 'false',
                    'type'                      => 'card',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

                return [
                    'message'   => $result->errors->deepAll()[0]->code,
                    'status'    => false,
                    'card'      => []
                ];
            } 
        }  

        /**
         * [This method is used for get_payment_checkout_html] 
         * @param [Integer]$employer_id[Used for employer id]
         * @return Data Response
         */       

        public static function get_payment_checkout_html($employer_id){
            $prefix              = \DB::getTablePrefix();
            $default_card_detail = \Models\PaypalPayment::get_user_default_card($employer_id,[
                    \DB::raw("CONCAT('".asset('/')."','',{$prefix}card_type.image) as image_url"),
                    'masked_number',
                    \DB::raw("REPLACE(masked_number,'x', '') as last4")
                ]);
                
            $html = '<span class="plan-main-heading">'.trans('website.W0433').'</span>';
            if(!empty($default_card_detail)){
                $html .= '<span>';
                    $html .= '<a class="manage-cards" data-target="#add-cards" data-request="ajax-modal" data-url="'.url(sprintf('%s/payment/card/manage',EMPLOYER_ROLE_TYPE)).'">'.trans('website.W0431').'</a>';
                    $html .= '<img class="selected-card" src="'.$default_card_detail['image_url'].'" />';
                    $html .= '<strong>';
                        $html .= wordwrap(sprintf("%s%s",str_repeat(".",strlen($default_card_detail['masked_number'])-4),$default_card_detail['last4']),4,' ',true);
                    $html .= '</strong>';
                $html .= '</span>';
            }else{
                $html .= '<span>';
                    $html .= '<a class="manage-cards" style="left: 0;" data-target="#add-cards" data-request="ajax-modal" data-url="'.url(sprintf('%s/payment/card/manage',EMPLOYER_ROLE_TYPE)).'">'.trans('website.W0430').'</a>';
                $html .= '</span>';
            }

            return [
                'html' => $html,
                'is_card_available' => (!empty($default_card_detail))?true:false
            ];
        }

        /**
         * [This method is used for user's payment transfer - payout] 
         * @param [Varchar]$receiver_emails [Used for Receiver email]
         * @param [Integer]$amount[Used for amount]
         * @param [String]$note[Used for note]
         * @param [Integer]$unique_id[Used for unique id]
         * @return Data Response
         */ 

        public static function transfer_payment_talent_payout($receiver_emails,$receiver_MerchID,$amount,$commission,$user_id,$project_id,$price_unit,$note = NULL,$unique_id = NULL){  

          if (!empty($receiver_emails)){

            $currency = !empty($price_unit) ? $price_unit : DEFAULT_CURRENCY;

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

            $get_amt = $amount;
            $amount  = floatval($amount) + $commission; 
            $get_amt = sprintf("%.2f", $amount);

            //cURL for Payout
            $curl = curl_init();
            $data = [
                      'sender_batch_header' => [
                          'email_subject' => "You have a payment for JobID- ".$project_id,
                          'sender_batch_id' => $random_string
                      ],
                      'items' => [
                          [
                            'recipient_type' => "PAYPAL_ID",
                            'amount' => [
                                'value' => $get_amt,
                                'currency' => $currency
                            ],
                            'receiver' => $receiver_MerchID,
                            'note' => 'Payment from Crowbar. Batch Id- '.$random_string,
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

              $receiver_information   = \Models\Talents::defaultKeys()->where('id_user',$user_id)->get()->first();
              $projectlog             = \Models\ProjectLogs::timelog($project_id,$user_id);
              $project_information    = \Models\Projects::defaultKeys()->with([
                  'projectlog' => function($q) use($user_id){
                      $q->select('project_id')
                      ->totalTiming()
                      ->where('talent_id',$user_id)
                      ->groupBy(['project_id']);
                  },
              ])->where('id_project',$project_id)->get()->first();
                    
              // $emailData                  = ___email_settings();
              // $emailData['email']         = $receiver_information->email;
              // $emailData['project_id']    = sprintf("#%'.0".JOBID_PREFIX."d",$project_id);
              // $emailData['name']          = $receiver_information->first_name;
              // $emailData['link']          = url('talent/project/details?job_id='.___encrypt($project_id));
              // $emailData['table']         = view('talent.payment.invoice',['amount' => $amount, 'commission' => $commission, 'project' => $project_information, 'payment' => $response_arr, 'projectlog' => $projectlog])->render();
                    
              // ___mail_sender($receiver_information->email,sprintf("%s %s",$receiver_information->first_name,$receiver_information->last_name),"talent_invoice",$emailData);

            }

            return $response_arr;

          }else{
                return [
                    'status' => false
                ];
          }

        }

        /**
         * [This method is used for user's payment transfer] 
         * @param [Varchar]$receiver_emails [Used for Receiver email]
         * @param [Integer]$amount[Used for amount]
         * @param [String]$note[Used for note]
         * @param [Integer]$unique_id[Used for unique id]
         * @return Data Response
         */ 

        public static function transfer_payment_talent($receiver_emails,$amount,$commission,$user_id,$project_id,$note = NULL,$unique_id = NULL){
            $email_subject  = urlencode(MASSPAY_EMAIL_SUBJECT);
            $receiver_type  = urlencode('EmailAddress');
            $currency       = urlencode(MASSPAY_CURRENCY);
            $note           = urlencode("ID")."#".urlencode(sprintf("%'.0".JOBID_PREFIX."d",$project_id));
            $nvpstr         = NULL;

            if (!empty($receiver_emails)){
                $receiverEmail = urlencode($receiver_emails);
                $amount = urlencode($amount);
                $unique_id = urlencode($unique_id);
                $note = urlencode($note);
                $nvpstr.="&L_EMAIL0=$receiverEmail&L_Amt0=$amount&L_UNIQUEID0=$unique_id&L_NOTE0=$note";

                $nvpstr.="&EMAILSUBJECT=$email_subject&RECEIVERTYPE=$receiver_type&CURRENCYCODE=$currency" ;
                
                $payment = \App\Helper\Masspay::hash_call("MassPay",$nvpstr,$email_subject);

                if(!empty($payment['status'])){
                    $receiver_information   = \Models\Talents::defaultKeys()->where('id_user',$user_id)->get()->first();
                    $projectlog             = \Models\ProjectLogs::timelog($project_id,$user_id);
                    $project_information    = \Models\Projects::defaultKeys()->with([
                        'projectlog' => function($q) use($user_id){
                            $q->select('project_id')
                            ->totalTiming()
                            ->where('talent_id',$user_id)
                            ->groupBy(['project_id']);
                        },
                    ])->where('id_project',$project_id)->get()->first();
                    
                    $emailData                  = ___email_settings();
                    $emailData['email']         = $receiver_information->email;
                    $emailData['project_id']    = sprintf("#%'.0".JOBID_PREFIX."d",$project_id);
                    $emailData['name']          = $receiver_information->first_name;
                    $emailData['link']          = url('talent/project/details?job_id='.___encrypt($project_id));
                    $emailData['table']         = view('talent.payment.invoice',['amount' => $amount, 'commission' => $commission, 'project' => $project_information, 'payment' => $payment['data'], 'projectlog' => $projectlog])->render();
                    
                    ___mail_sender($receiver_information->email,sprintf("%s %s",$receiver_information->first_name,$receiver_information->last_name),"talent_invoice",$emailData);
                }

                return $payment;
            }else{
                return [
                    'status' => false
                ];
            }
        }

        /**
         * [This method is used for employer refund in detail] 
         * @param [Integer]$project_id [Used for project id]
         * @return Data Response
         */ 

        public static function employer_refund_detail($project_id){
            $prefix = DB::getTablePrefix();
            
            $amount_agreed = \DB::table("transactions")->select([
                'transactions.transaction_subtotal as amount',
                'transactions.transaction_reference_id',
                'transactions.transaction_user_id',
                'transactions.transaction_user_type',
            ])
            ->where('transactions.transaction_project_id','=',$project_id)
            ->where('transactions.transaction_type','=','debit')
            ->where('transactions.transaction_status','=','confirmed')
            ->get()
            ->first();

            $amount_paid = DB::table("transactions")
            ->select(
                \DB::Raw("SUM({$prefix}transactions.transaction_subtotal) amount")
            )
            ->where('transactions.transaction_project_id','=',$project_id)
            ->where('transactions.transaction_type','=','credit')
            ->where('transactions.transaction_status','=','confirmed')
            ->get()
            ->first();

            $payment = self::admin_payment_details($project_id);

            return [
                'refundable_amount'         => ___format($payment['employer_refundable_amount']),
                'refundable_transaction_id' => $amount_agreed->transaction_reference_id,
                'refundable_user_id'        => $amount_agreed->transaction_user_id,
                'refundable_user_type'      => $amount_agreed->transaction_user_type,
                'refundable_proposal_id'    => \Models\Proposals::accepted_proposal_id($project_id)
            ];
        }

        /**
         * [This method is used for talent payble in detail] 
         * @param [Integer]$project_id [Used for project id]
         * @return Float
         */ 

        public static function talent_payble_detail($project_id){
            $prefix = DB::getTablePrefix();

            $table_projects = DB::table('projects');
            
            $payments = $table_projects->select([
                \DB::Raw("
                    IF(
                        ({$prefix}projects.employment = 'hourly'),
                        (
                            (
                                SUM(
                                    TIMESTAMPDIFF(
                                        Second,
                                        {$prefix}project_log.startdate,
                                        {$prefix}project_log.enddate
                                    )
                                )/3600
                            )*{$prefix}proposals.quoted_price
                        ),
                        {$prefix}proposals.quoted_price
                    ) as amount
                "),
            ]);
            
            $table_projects->leftjoin("project_log","project_log.project_id", "=", "projects.id_project");
            $table_projects->leftjoin("talent_proposals as proposals",function($leftjoin){
                $leftjoin->on("proposals.project_id", "=", "projects.id_project");
                $leftjoin->on("proposals.status", "=", \DB::Raw('"accepted"'));
            });

            if(!empty($project_id)){
                $table_projects->where('id_project',$project_id);
            }

            $table_projects->whereNotNull('project_log.enddate');
            $table_projects->where('close','pending');
            $table_projects->groupBy(['projects.id_project']);

            $result = $payments->get()->first();

            if(!empty($result)){
                return ___format((float)$result->amount);
            }else{
                return ___format((float)0);
            }
        }

        /**
         * [This method is used for remove] 
         * @param [String]$data[Used for data]
         * @return Boolean
         */ 

        public static function subscriptionResponse($data){
            return DB::table('user_subscription')
            ->insert($data);
        }

        /**
         * [This method is used for payout Listing] 
         * @param [Integer]$project_id [Used for project id]
         * @return Data Response
         */ 

        public static function payoutList($project_id = NULL){
            $prefix = DB::getTablePrefix();

            \DB::statement(\DB::raw('set @row_number=0'));
            $table_projects = DB::table('projects');

            $payments = $table_projects->select([
                \DB::raw('@row_number  := @row_number  + 1 AS row_number'),
                'projects.id_project as transaction_project_id',
                'projects.title',
                'employers.company_name',
                'project_log.close as transaction_status',
                'project_log.enddate as transaction_date',
                'transaction',
                'projects.transaction',
                'projects.employment',
                'project_log.startdate',
                'project_log.enddate',
                'project_log.start',
                'project_log.close',
                'proposals.quoted_price',
                \DB::Raw("(SUM(TIMESTAMPDIFF(Second,{$prefix}project_log.startdate,{$prefix}project_log.enddate))/3600) as working_hours"),
                \DB::Raw("TRIM(CONCAT(".$prefix."employers.first_name,' ',".$prefix."employers.last_name)) as employer_name"),

                \DB::Raw("TRIM(CONCAT(".$prefix."talent.first_name,' ',".$prefix."talent.last_name)) as talent_name"),
            ]);

            $table_projects->leftjoin("users as employers","employers.id_user", "=", "projects.user_id");

            $table_projects->leftjoin("project_log","project_log.project_id", "=", "projects.id_project");
            $table_projects->leftjoin("talent_proposals as proposals",function($leftjoin){
                $leftjoin->on("proposals.project_id", "=", "projects.id_project");
                $leftjoin->on("proposals.status", "=", \DB::Raw('"accepted"'));
            });
            $table_projects->leftjoin("users as talent","talent.id_user", "=", "project_log.talent_id");

            $table_projects->whereNotNull('project_log.enddate');
            $table_projects->where('close','pending');
            $table_projects->where('request_payout','yes');
            $table_projects->groupBy(['id_log']);

            return $payments->get();
        }

        /**
         * [This method is used for sum of total balance] 
         * @param [Integer]$user_id
         * @return [Integer]
         */ 

        public static function total_balance($user_id){
            $balance = \Models\Transactions::balance()->where('transaction_company_id',$user_id)
            ->whereIn('transaction_status',['confirmed'])
            ->where('transaction_type','credit')
            ->groupBy(['transaction_project_id'])
            ->get();

            $balance = json_decode(json_encode($balance),true);

            return ___cache('currencies')[request()->currency].___format(array_sum(array_column($balance, 'balance')),true,false);
        }


        /**
         * [This method is used for sum of total paid] 
         * @param [Integer]$user_id
         * @param [String]$type
         * @return [Integer]
         */ 

        public static function total_paid($user_id,$type){
            $prefix = \DB::getTablePrefix();
            
            $total_paid = \Models\Transactions::select([
                \DB::Raw('
                    `CONVERT_PRICE`(
                        transaction_subtotal, 
                        '.$prefix.'transactions.currency, 
                        "'.request()->currency.'"
                    ) AS transaction_subtotal
                ')
            ])
            ->where('transaction_user_id',$user_id)
            ->where('transaction_user_type',$type)
            ->where('transaction_status','confirmed')
            ->where('transaction_type','debit')
            ->get()
            ->sum('transaction_subtotal');

            return ___cache('currencies')[request()->currency].___format($total_paid,true,false);

        }

        /**
         * [This method is used for sum of total received] 
         * @param [Integer]$user_id
         * @param [String]$type
         * @return [Integer]
         */ 

        public static function total_received($user_id,$type){
            $prefix = \DB::getTablePrefix();
            
            $total_received = \Models\Transactions::select([
                \DB::Raw('
                    `CONVERT_PRICE`(
                        transaction_subtotal, 
                        '.$prefix.'transactions.currency, 
                        "'.request()->currency.'"
                    ) AS transaction_subtotal
                ')
            ])
            ->where('transaction_user_id',$user_id)
            ->where('transaction_user_type',$type)
            ->where('transaction_status','confirmed')
            ->where('transaction_type','credit')
            ->get()
            ->sum('transaction_subtotal');

            return ___cache('currencies')[request()->currency].___format($total_received,true,false);
        }

        /**
         * [This method is used for sum of total due] 
         * @param [Integer]$user_id
         * @return [Integer]
         */ 

        public static function total_due($user_id){
            $prefix = DB::getTablePrefix();
            $commission         = ___cache('configuration')['commission'];
            $commission_type    = ___cache('configuration')['commission_type'];

            $payments = \Models\Projects::select([
                \DB::Raw("
                    `DEDUCT_COMMISSION`(
                        IF(
                            {$prefix}projects.employment = 'hourly',
                            (ABS(HOUR(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00')))+ABS(MINUTE(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00'))/60)+ABS(SECOND(IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC({$prefix}proposals.working_hours))),'00:00:00'))/3600)) * `CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."'),
                            IF(
                                {$prefix}projects.employment = 'monthly',
                                ((DATEDIFF({$prefix}projects.enddate,{$prefix}projects.startdate)+1)/".MONTH_DAYS.")*`CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."'),
                                `CONVERT_PRICE`({$prefix}proposals.quoted_price, {$prefix}proposals.price_unit, '".request()->currency."')
                            )
                        ),
                        {$commission},
                        '{$commission_type}'
                    ) as total_due
                ")
            ])
            ->leftjoin("users as employers","employers.id_user", "=", "projects.user_id")
            ->leftjoin("project_log",function($leftjoin){
                $leftjoin->on("project_log.project_id", "=", "projects.id_project");
            })
            ->leftjoin("talent_proposals as proposals",function($leftjoin) use($user_id){
                $leftjoin->on("proposals.project_id", "=", "projects.id_project");
                $leftjoin->on("proposals.status", "=", \DB::Raw('"accepted"'));
                $leftjoin->on("proposals.user_id", "=", \DB::Raw("{$user_id}"));
            })
            ->where('proposals.payment','pending')
            ->groupBy(['projects.id_project'])
            ->get();

            $payments = json_decode(json_encode($payments));

            return ___cache('currencies')[request()->currency].___format(array_sum(array_column($payments,'total_due')),true,false);
        }

        /**
         * [This method is used for admin payment] 
         * @param [Integer]$project_id[Used for project id]
         * @return Data Response
         */ 

        public static function admin_payment_details($project_id){
            $data = \Models\Payments::get_talent_payout_details($project_id);

            if(!empty($data)){
                $employer_project_payment       = \Models\Transactions::where('transaction_project_id',$project_id)->where('transaction_status','confirmed')->select(['transaction_subtotal','transaction_reference_id'])->first();

                $calculated_commission          = ___employer_commission($employer_project_payment->transaction_subtotal); 
                $refundable_amount              = $employer_project_payment->transaction_subtotal - $calculated_commission;

                return [
                    'employer_refundable_amount'    => $refundable_amount,
                    'talent_payment'                => $data['transaction_subtotal'],
                    'calculated_commission'         => $calculated_commission,
                ];
            }else{
                return [
                    'employer_refundable_amount'    => 0,
                    'talent_payment'                => 0,
                    'calculated_commission'         => 0,
                ];
            }
        }

        /**
         * [This method is used for cancel refund] 
         * @param [Integer]$project_id[Used for project id]
         * @return Data Response
         */ 

        public static function cancel_refund($project_id,$company_id,$proposal_id){
            $employer_project_payment           = \Models\Transactions::where('transaction_project_id',$project_id)->select(['transaction_subtotal','transaction_reference_id'])->get()->first();
            $commission                         = ___cache('configuration')['cancellation_commission'];
            $commission_type                    = ___cache('configuration')['cancellation_commission_type'];

            if($commission_type == 'per'){
                $calculated_commission          = ___format(round(((($employer_project_payment->transaction_subtotal*$commission)/100)),2));
            }else{
                $calculated_commission          = ___format(round(($commission),2));
            }

            $refundable_amount                  = $employer_project_payment->transaction_subtotal - $calculated_commission;

            if($refundable_amount > 0){
                $refundable_data = [
                    'transaction_user_id'       => $company_id,
                    'transaction_company_id'    => $company_id,
                    'transaction_user_type'     => 'employer',
                    'transaction_project_id'    => $project_id,
                    'transaction_comment'       => $employer_project_payment->transaction_reference_id,
                    'transaction_proposal_id'   => $proposal_id,
                    'transaction_total'         => $refundable_amount,
                    'transaction_subtotal'      => $refundable_amount,
                    'transaction_date'          => date('Y-m-d',strtotime("+ ".REFUNDABLE_DATE_MARGIN."days")),
                    'currency'                  => DEFAULT_CURRENCY,
                    'transaction_type'          => 'credit',
                    'transaction_status'        => 'refunded-pending',
                    'transaction_done_by'       => -1,
                    'is_cancelled'              => DEFAULT_YES_VALUE,
                    'updated'                   => date('Y-m-d H:i:s'),
                    'created'                   => date('Y-m-d H:i:s'),
                ];

                return \Models\Transactions::insertGetId($refundable_data);
            }else{
                return false;
            }
        }


        public static function getCheckoutData($payment,$recurring = false){
            $data = [];

            if ($recurring === true) {
                $data['items'] = [
                    [
                        'name'  => 'Monthly Subscription '."Order".' #'.$payment['transaction_project_id'],
                        'price' => round($payment['transaction_total'],2),
                        'qty'   => 1,
                    ]
                ];
                $data['subscription_desc'] = 'Monthly Subscription  #'.$payment['transaction_project_id'];
                $data['return_url'] = url(sprintf("%s/payment/paypal-success?mode=recurring",EMPLOYER_ROLE_TYPE));
            } else {
                $data['items'] = [
                    [
                        'name'  => 'Product 1',
                        'price' => round($payment['transaction_total'],2),
                        'qty'   => 1,
                    ]
                ];

                $data['return_url'] = url(sprintf("%s/payment/paypal-success",EMPLOYER_ROLE_TYPE));
            }

            $data['invoice_id'] = "Project".'_'.$payment['transaction_project_id'];
            $data['invoice_description'] = 'Project Id : '.$payment['transaction_project_id'].' Proposal Id : '.$payment['transaction_proposal_id'];
            $data['cancel_url'] = url(sprintf('%s/payment/paypal-cancel',EMPLOYER_ROLE_TYPE));

            $total = 0;
            foreach ($data['items'] as $item) {
                $total += $item['price'] * $item['qty'];
            }

            $data['total'] = $total;

            return $data;
        }


        /**
         * Create invoice.
         *
         * @param array  $cart
         * @param string $status
         *
         * @return \App\Invoice
         */
        protected function createInvoice($cart, $status)
        {
            echo "hello";exit;
            // $invoice = new Invoice();
            // $invoice->title = $cart['invoice_description'];
            // $invoice->price = $cart['total'];
            // if (!strcasecmp($status, 'Completed') || !strcasecmp($status, 'Processed')) {
            //     $invoice->paid = 1;
            // } else {
            //     $invoice->paid = 0;
            // }
            // $invoice->save();

            // collect($cart['items'])->each(function ($product) use ($invoice) {
            //     $item = new Item();
            //     $item->invoice_id = $invoice->id;
            //     $item->item_name = $product['name'];
            //     $item->item_price = $product['price'];
            //     $item->item_qty = $product['qty'];

            //     $item->save();
            // });

            // return $invoice;
        }
    }
