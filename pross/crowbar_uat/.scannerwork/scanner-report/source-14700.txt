<?php

namespace Models;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\CreditCard;
use Illuminate\Http\Request;

/*F O R   P A Y M E N T S*/
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\FundingInstrument;
use PayPal\Api\CreditCardToken;
use PayPal\Api\PaymentCard;
use PayPal\Exception\PayPalConfigurationException;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Exception\PayPalInvalidCredentialException;
use PayPal\Exception\PayPalMissingCredentialException;


/*P L A N*/
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency as Currencies;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;

/*A G R E E M E N T*/
use PayPal\Api\Agreement;
use PayPal\Api\ShippingAddress;

/*A C T I V E  P L A N*/
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;

/*R E F U N D*/
use PayPal\Api\Refund;
use PayPal\Api\Sale;

// Used to process plans

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaypalPayment extends Model
{
	protected $table = 'user_card_paypal';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    protected $fillable = ['id_card', 'user_id', 'user_type', 'default', 'type', 'paypal_payer_id', 'masked_number', 'first_name', 'last_name', 'card_token', 'state', 'valid_until', 'create_time', 'update_time', 'links', 'card_status', 'created', 'updated'];

    protected $hidden = [];
    private $apiContext;
    private $mode;
    private $client_id;
    private $secret;

    public function __construct(){
	    $this->client_id = config('paypal.client_id');
	    $this->secret = config('paypal.paypal_secret');
        $this->apiContext = new ApiContext(new OAuthTokenCredential($this->client_id, $this->secret));
        $this->apiContext->setConfig(config('paypal.settings'));
    }

    public static function generate_paypal_payer_id($length = 10){
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $payerId = '';
	    
	    for ($i = 0; $i < $length; $i++) {
	        $payerId .= strtoupper($characters[rand(0, $charactersLength - 1)]);
	    }

	    $isExistPayerId = self::check_paypal_payer_id($payerId);
	    
	    if($isExistPayerId){
	        self::generate_paypal_payer_id();
	    }else{
	    	\DB::table('users')->where(['id_user' => \Auth::user()->id_user])->update([
	    		'paypal_payer_id'=>$payerId,
	    		'updated'=>date('Y-m-d H:i:s')
	    	]);
	        return $payerId;
	    }
	}

  	public static function check_paypal_payer_id($paypal_payer_id){
    	$isExist = \DB::table('users')->where('paypal_payer_id',$paypal_payer_id)->count();
    	if(empty($isExist)) return false;
    	else return true;
  	}	

    public static function create_credit_card($credit_card,$save_card=true){
    	$self 		= new static;
        $prefix     = \DB::getTablePrefix();
    	$payer_id 	= !empty(\Auth::user()->paypal_payer_id) ? \Auth::user()->paypal_payer_id : self::generate_paypal_payer_id();
        $card 		= new CreditCard();
	    
	    $card->setType($credit_card["card_type"])
            ->setNumber($credit_card["number"])
            ->setExpireMonth($credit_card["expiry_month"])
            ->setExpireYear($credit_card["expiry_year"])
            ->setCvv2($credit_card["cvv"])
            ->setFirstName($credit_card["cardholder_name"])
            ->setPayerId($payer_id);

        try {
            $card->create($self->apiContext);
            $data = (array)json_decode(json_encode(json_decode($card), 128));

	        self::paypal_response([
	            'user_id'                   => \Auth::user()->id_user,
	            'response_json'             => json_encode(json_decode($card), 128),
	            'request_type'              => 'add_credit_card',
	            'status'                    => 'true',
	            'created'                   => date('Y-m-d H:i:s')
            ]);

            if(___cache('configuration')['add_card_default_payment'] == 'on'){
                $card_details = [
                    'card_token'      => $data['id'], 
                    'paypal_payer_id' => $data['payer_id'],     
                    'amount'          => ___format(___cache('configuration')['add_card_default_payment_amount'])
                ];

                $result = self::payment_checkout($card_details);

                if($result['status'] == true){
                    $transaction_Data = (array)((array)array_column($result['payment_data']['transactions'], 'related_resources') [0] );

                    $sale_data = [
                        'transaction_user_id'           => \Auth::user()->id_user,
                        'transaction_company_id'        => \Auth::user()->id_user,
                        'transaction_user_type'         => \Auth::user()->type,
                        'transaction_project_id'        => NULL,
                        'transaction_proposal_id'       => NULL,
                        'transaction_total'             => $card_details['amount'],
                        'transaction_subtotal'          => $card_details['amount'],
                        'currency'                      => DEFAULT_CURRENCY,
                        'transaction_source'            => 'paypal',
                        'transaction_reference_id'      => $transaction_Data[0]->sale->id,
                        'transaction_comment'           => 'Payment to validate your card.',
                        'transaction_type'              => 'debit',
                        'transaction_status'            => 'refund-pending',
                        'transaction_date'              => date('Y-m-d',strtotime("+ ".REFUNDABLE_DATE_MARGIN."days")),
                        'transaction_actual_date'       => NULL,
                        'transaction_commission'        => NULL,
                        'transaction_commission_type'   => NULL,
                        'transaction_paypal_commission' => ___calculate_paypal_commission($card_details['amount']),
                        'raise_dispute_commission'      => NULL,
                        'raise_dispute_commission_type' => NULL,
                        'transaction_done_by'           => -1,
                        'updated'                       => date('Y-m-d h:i:s'),
                        'created'                       => date('Y-m-d h:i:s')
                    ];
                    $isPaid = self::save_transaction($sale_data);
                }else{
                    $isPaid = false;
                }
            }else{
                $isPaid = true;
            }



            if($isPaid == true){
                if($save_card == true){
                	$get_user_default_card = self::get_user_default_card(\Auth::user()->id_user);

                    $card_data = [
                        'user_id'           => \Auth::user()->id_user,
                        'user_type'         => \Auth::user()->type,
                        'default'           => !empty($get_user_default_card) ? DEFAULT_NO_VALUE : DEFAULT_YES_VALUE,
                        'type'              => $data['type'],
                        'paypal_payer_id'   => $data['payer_id'],
                        'masked_number'     => $data['number'],
                        'first_name'        => $data['first_name'],
                        'card_token'        => $data['id'],
                        'state'             => $data['state'],
                        'valid_until'       => $data['valid_until'],
                        'create_time'       => $data['create_time'],
                        'update_time'       => $data['update_time'],
                        'links'             => json_encode($data['links']),
                        'card_status'       => 'active',
                        'created'           => date('Y-m-d H:i:s'),
                        'updated'           => date('Y-m-d H:i:s'),
                    ];
                    $isInserted = self::save_credit_card($card_data);
                    if($isInserted){
                        return [
                            'message'   => "M0392",
                            'status'    => true,
                            'card'      => self::get_user_card(\Auth::user()->id_user,$isInserted,'first',['*',
                                \DB::raw("CONCAT('".asset('/')."','',{$prefix}card_type.image) as image_url"),
                                \DB::raw("REPLACE(masked_number,'x', '') as last4")
                            ])
                        ];
                    }else{
                        return [
                            'message'   => "M0356",
                            'status'    => false,
                            'card'      => []
                        ];
                    }            
                }

                return [
                    'message'           => "M0392",
                    'status'            => true,
                    'card'              => [
                    	'payer_id'		=> $data['payer_id'],
                        'card_token'    => $data['id'],
                        'image_url'     => asset('/images/card').'/'.$data['type'].'.png',
                        'masked_number' => $data['number'],
                        'last4'         => str_replace('x','', $data['number'])
                    ]
                ];
            }else{
                return [
                    'status' => false,
                    'message' => 'M0570'
                ];
            }

        }catch (PayPalConnectionException $ex) {
            if($ex->getData() != null){
                $paypal_error=json_decode($ex->getData(),128)['details'];
                $errors = self::make_paypal_errors($paypal_error,'add_card','');
                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                    'request_type'              => 'add_credit_card',
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
                return [
                    'status' => false,
                    'message' => $errors
                ];
            }else{
                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                    'request_type'              => 'add_credit_card',
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);                
                return [
                    'status' => false,
                    'message' => 'M0550'
                ];                
            }
        }catch (PayPalConfigurationException $ex) {
            self::paypal_response([
                'user_id'                   => \Auth::user()->id_user,
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'add_credit_card',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'        => false,
                'message' => 'M0550'
            ];
        }catch (PayPalInvalidCredentialException $ex) {
            self::paypal_response([
                'user_id'                   => \Auth::user()->id_user,
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'add_credit_card',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'        => false,
                'message' => 'M0550'
            ];
        }catch (PayPalMissingCredentialException $ex) {
            self::paypal_response([
                'user_id'                   => \Auth::user()->id_user,
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'add_credit_card',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'        => false,
                'message' => 'M0550'
            ];
        }
    }

    public static function paypal_response($data){
        if(!empty($data)){
            $isInserted = \DB::table('api_paypal_response')->insert($data); 
        }
        return (bool)$isInserted;
    }

    public static function save_credit_card($credit_card){
        if(!empty($credit_card)){
            $isInserted = \DB::table('user_card_paypal')->insertGetId($credit_card);
        }
        if($isInserted){
            return $isInserted;
        }else{
            return false;
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

        $table_user_card = DB::table('user_card_paypal');
        $table_user_card->select($keys)
        ->leftJoin('card_type','card_type.type','=','user_card_paypal.type')
        ->where(['card_status' => 'active'])
        ->where(['user_id' => $user_id])
        ->where(['default' => DEFAULT_YES_VALUE]);
        
        return json_decode(json_encode($table_user_card->get()->first()),true);
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
        $table_user_card = DB::table('user_card_paypal');
        $table_user_card->select($keys)
        ->leftJoin('card_type','card_type.type','=','user_card_paypal.type');
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
        }else if($fetch === 'count'){
            return $table_user_card->count();
        }else{
            return $table_user_card->get();
        }
    }

    /**
     * [This method is used to mark default card ] 
     * @param [Integer]$user_id[Used for user id]
     * @param [Varchar]$card_id[Used for card id]
     * @return Boolean
     */   
    public static function mark_card_default($user_id,$card_id = NULL){
        $isUpdated = DB::table('user_card_paypal')->where('user_id',$user_id)->update(['default' => DEFAULT_NO_VALUE, 'updated' => date('Y-m-d H:i:s')]);

        if(!empty($card_id)){
            return DB::table('user_card_paypal')
            ->where('id_card',$card_id)
            ->where('user_id',$user_id)
            ->update([
                'default' => DEFAULT_YES_VALUE, 
                'updated' => date('Y-m-d H:i:s')
            ]);
        }else{
            $selected = DB::table('user_card_paypal')
            ->select(['id_card'])
            ->where('user_id',$user_id)
            ->where('card_status','active')
            ->orderBy('updated','DESC')
            ->get()
            ->first();

            if(!empty($selected)){
                return DB::table('user_card_paypal')
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
     * [This method is used to mark default card ] 
     * @param [Varchar]$card_token[Used for delete card from paypal]
     * @param [Integer]$card_id[Used to change status from active to trash card from user card table]
     * @return Boolean
     */
    public static function delete_card($card_token,$card_id){
    	$self 		= new static;
	    
	    try{
	        $card           = new CreditCard();
	        $selected_card  = $card->get($card_token,$self->apiContext);
	        $card_status    = $selected_card->delete($self->apiContext);
	        self::paypal_response([
	            'user_id'                   => \Auth::user()->id_user,
	            'response_json'             => $card_status,
	            'request_type'              => 'delete_credit_card',
	            'status'                    => 'true',
	            'created'                   => date('Y-m-d H:i:s')
	        ]);
	    }catch (PayPalConnectionException $ex) {
            self::paypal_response([
                'user_id'                   => \Auth::user()->id_user,
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'delete_credit_card',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status' => false,
                'message' => 'M0550'
            ];
        }catch (PayPalConfigurationException $ex) {
            self::paypal_response([
                'user_id'                   => \Auth::user()->id_user,
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'delete_credit_card',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'        => false,
                'message' => 'M0550'
            ];
        }catch (PayPalInvalidCredentialException $ex) {
            self::paypal_response([
                'user_id'                   => \Auth::user()->id_user,
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'delete_credit_card',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'  => false,
                'message' => 'M0550'
            ];
        } catch (PayPalMissingCredentialException $ex) {
            self::paypal_response([
                'user_id'                   => \Auth::user()->id_user,
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'delete_credit_card',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'        => false,
                'message' => 'M0550'
            ];
        }

        $table_user_card = DB::table('user_card_paypal');
        if($card_id){
            $table_user_card->where('id_card','=',$card_id);
            $isUpdated = $table_user_card->update(['card_status'=>'trashed']);
        }
    	return (bool)$isUpdated;
    }

    public static function payment_checkout($card_details,$is_recurring = false,$repeat_till_month = 0,$device_type = 'web'){
        $self = new static;
        $return_token = '';

        if($is_recurring == true){
            $plan = new Plan();

            $plan->setName('Recurrsive plan')
                 ->setDescription('Recurrsive plan test')
                 ->setType('fixed');

            $paymentDefinition = new PaymentDefinition();

            $paymentDefinition->setName('Regular Payments')
                ->setType('REGULAR')
                ->setFrequency('Month')
                ->setFrequencyInterval("1")
                ->setCycles($repeat_till_month)
                ->setAmount(new Currencies(array('value' =>$card_details['amount'], 'currency' => DEFAULT_CURRENCY)));

            $chargeModel = new ChargeModel();
            $chargeModel->setType('SHIPPING')
                ->setAmount(new Currencies(array('value' => 1, 'currency' => DEFAULT_CURRENCY)));

            $paymentDefinition->setChargeModels(array($chargeModel));

            $merchantPreferences = new MerchantPreferences();
            $baseUrl = url('/');

            if($device_type == "web"){
                $merchantPreferences->setReturnUrl(url(sprintf("%s/payment/paypal-billing-success",EMPLOYER_ROLE_TYPE)))
                ->setCancelUrl(url(sprintf('%s/payment/paypal-billing-cancel',EMPLOYER_ROLE_TYPE)))
                ->setAutoBillAmount("yes")
                ->setInitialFailAmountAction("CONTINUE")
                ->setMaxFailAttempts("0")
                ->setSetupFee(new Currencies(array('value'=>$card_details['amount'],'currency'=>DEFAULT_CURRENCY)));
            }else{
                $merchantPreferences->setReturnUrl(url("/payment/paypal-billing-success"))
                ->setCancelUrl(url('/payment/paypal-billing-cancel'))
                ->setAutoBillAmount("yes")
                ->setInitialFailAmountAction("CONTINUE")
                ->setMaxFailAttempts("0")
                ->setSetupFee(new Currencies(array('value'=>$card_details['amount'],'currency'=>DEFAULT_CURRENCY)));
            }

            $plan->setPaymentDefinitions(array($paymentDefinition));
            $plan->setMerchantPreferences($merchantPreferences);
            $request = clone $plan;

            try {

                $plan->create($self->apiContext);
                $plan_data = (array)json_decode(json_encode(json_decode($plan), 128));
            
                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(json_decode($plan), 128),
                    'request_type'              => 'recurrsive plan payment',
                    'status'                    => 'true',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

            } catch (Exception $ex) {

                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                    'request_type'              => 'recurrsive plan payment',
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

                return [
                    'status' => false,
                    'message' => 'M0550'
                ];
            }

            /*Activate plan start*/
            try {
                $patch = new Patch();

                $value = new PayPalModel('{
                       "state":"ACTIVE"
                     }');

                $patch->setOp('replace')
                    ->setPath('/')
                    ->setValue($value);
                $patchRequest = new PatchRequest();
                $patchRequest->addPatch($patch);

                $plan->update($patchRequest, $self->apiContext);
                $active_plan = Plan::get($plan_data['id'], $self->apiContext);

                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(json_decode($active_plan), 128),
                    'request_type'              => 'Activate Plan PlanID- '.$plan_data['id'],
                    'status'                    => 'true',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

            } catch (Exception $ex) {
                // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                echo '<pre>';print_r(json_decode($pce->getData()));exit;
            }
            /*Activate plan end*/

            /* Create a new instance of Billing Agreement object*/
            $agreement = new Agreement();

            $agreement->setName('Base Agreement')
                                ->setDescription('Basic Agreement')
                                ->setStartDate(date(DATE_ISO8601, strtotime('tomorrow')));

            // Add Plan ID
            $plan = new Plan();
            $plan->setId($plan_data['id']);
            $agreement->setPlan($plan);

            $payer = new Payer();
            $payer->setPaymentMethod('paypal');
            $agreement->setPayer($payer);

            $request = clone $agreement;

            try {

                $agreement->create($self->apiContext);
                $approvalUrl = $agreement->getApprovalLink();

                $saveAgreement = (array)json_decode(json_encode(json_decode($agreement), 128));

                $token_url = explode('token=',$approvalUrl);
                $return_token = $token_url[1];

                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(json_decode($agreement), 128),
                    'request_type'              => 'Recurrsive Plan Aggrement for plan- '.$plan_data['id'],
                    'status'                    => 'true',
                    'created'                   => date('Y-m-d H:i:s')
                ]);

            } catch (Exception $ex) {

                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                    'request_type'              => 'Recurrsive Plan Aggrement for plan- '.$plan_data['id'],
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
            } catch (PayPalConnectionException $pce) {
                // Don't spit out errors or use "exit" like this in production code
                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                    'request_type'              => 'Recurrsive Plan Aggrement for plan- '.$plan_data['id'],
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
                echo '<pre>';print_r(json_decode($pce->getData()));exit;
            }

            $redirectLink = $saveAgreement['links'][0]->href;

            return [
                    'status'           => true,
                    'message'          => 'Recurring with URL',
                    'redirect_link'    => $redirectLink,
                    'transaction_type' => 'recurring'
                ];

        }else{
            $creditCardToken = new CreditCardToken();
    		$creditCardToken->setCreditCardId($card_details['card_token'])
    			->setPayerId($card_details['paypal_payer_id']);

            $fundingInstrument = new FundingInstrument();
            $fundingInstrument->setCreditCardToken($creditCardToken);

    		$payer = new Payer();
    		$payer->setPaymentMethod("credit_card")
    			->setFundingInstruments(array($fundingInstrument));

    		$amount = new Amount();
    		$amount->setCurrency(DEFAULT_CURRENCY)
    		    ->setTotal($card_details['amount']);

    		$transaction = new Transaction();
    		$transaction->setAmount($amount)
    		    ->setDescription("Payment descripti`on")
    		    ->setInvoiceNumber(uniqid());

    		$baseUrl = url('/');
    		$redirectUrls = new RedirectUrls();
    		$redirectUrls->setReturnUrl("$baseUrl/ExecutePayment.php?success=true")
    		    ->setCancelUrl("$baseUrl/ExecutePayment.php?success=false");

    		$payment = new Payment();
    		$payment->setIntent("sale")
    		    ->setPayer($payer)
    		    ->setRedirectUrls($redirectUrls)
    		    ->setTransactions(array($transaction));

    		try {
    		    $payment->create($self->apiContext);
    		    $payment_data = (array)json_decode(json_encode(json_decode($payment), 128));		    
    	        self::paypal_response([
    	            'user_id'                   => \Auth::user()->id_user,
    	            'response_json'             => json_encode(json_decode($payment), 128),
    	            'request_type'              => 'accept proposal payment',
    	            'status'                    => 'true',
    	            'created'                   => date('Y-m-d H:i:s')
    	        ]);
    		}catch (PayPalConnectionException $ex) {
                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                    'request_type'              => 'accept proposal payment',
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
                return [
                    'status' => false,
                    'message' => 'M0550'
                ];
            }catch (PayPalConfigurationException $ex) {
                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                    'request_type'              => 'accept proposal payment',
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
                return [
                    'status'        => false,
                    'message' => 'M0550'
                ];
            }catch (PayPalInvalidCredentialException $ex) {
                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                    'request_type'              => 'accept proposal payment',
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
                return [
                    'status'        => false,
                    'message' => 'M0550'
                ];
            }catch (PayPalMissingCredentialException $ex) {
                self::paypal_response([
                    'user_id'                   => \Auth::user()->id_user,
                    'response_json'             =>json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                    'request_type'              => 'accept proposal payment',
                    'status'                    => 'false',
                    'created'                   => date('Y-m-d H:i:s')
                ]);
                return [
                    'status'        => false,
                    'message' => 'M0550'
                ];
            }

    		return [
                'status'        => true,
                'payment_data'  => $payment_data
            ];
    
        } //end else

	}

    public static function execute_billing_agreement($user_id,$token){

        $self       = new static;

        /*Execute Agreement Start*/
        $execute_agreement = new \PayPal\Api\Agreement();
        try {
            // Execute the agreement by passing in the token
            $execute_agreement->execute($token, $self->apiContext);

            self::paypal_response([
                'user_id'                   => $user_id,
                'response_json'             => json_encode(json_decode($execute_agreement), 128),
                'request_type'              => 'Execute_Billing_Agreement success',
                'status'                    => 'true',
                'created'                   => date('Y-m-d H:i:s')
            ]);

        }catch (PayPalConnectionException $pce) {

            self::paypal_response([
                'user_id'                   => $user_id,
                'response_json'             => json_decode($pce->getData()),
                'request_type'              => 'execute_billing_agreement fail',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);

            return [
                'status'             => false,
                'message'            => 'Recurring plan failed to implement.'
            ];

        } catch (Exception $ex) {

            self::paypal_response([
                'user_id'                   => $user_id,
                'response_json'             => json_decode($ex->getData()),
                'request_type'              => 'execute_billing_agreement fail',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);

            return [
                'status'             => false,
                'message'            => 'Recurring plan failed to implement.'
            ];

        }
        /*Execute Agreement End*/

        return [
            'status'             => true,
            'mode'               => 'payment success execute_agreement',
            'execute_agreement'  => $execute_agreement
        ];

    }

    public static function make_paypal_errors($errors, $validation_error,$getmessage){
        if(!is_array($errors)){
            $errors = (array)$errors;
        }
        if($validation_error == 'add_card'){
            $error_Array = [
                'number'        => [
                    'field' => 'number',
                    'code'  => 'M0551'
                ],
                'first_name'    => [
                    'field' => 'cardholder_name',
                    'code' => 'M0554'
                ],
                'cvv2'          => [
                    'field' => 'cvv',
                    'code' => 'M0552'
                ],
                'expire_month'  => [
                    'field' => 'expiry_month',
                    'code' => 'M0553'
                ],
                'expire_year'   => [
                    'field' => 'expiry_year',
                    'code' => 'M0553'
                ],
                'expire_month, expire_year' =>[
                    'field' => 'expiry_year',
                    'code' => 'M0553'
                ],
                'type, number' =>[
                    'field' => 'number',
                    'code' => 'M0555'
                ],
                'type, cvv2' =>[
                    'field' => 'cvv2',
                    'code' => 'M0552'
                ]
            ];
        }

        $messages = '';
        foreach ($errors as &$item) {
            $temp_array = !empty($error_Array[$item['field']])?$error_Array[$item['field']]:'' ;
            /* $messages[$temp_array['field']] = $temp_array['code']; */
            if(!empty($temp_array)){
                $messages = $temp_array['code'];
            }
            break;
        }
        if(empty($messages)){
            $messages = 'M0550';
        }
        return ($messages);
    }

    public static function refund($refund_array){
        $self   = new static;
        $amt    = new Amount();
        $amt->setTotal($refund_array['refund_amount'])
          ->setCurrency(DEFAULT_CURRENCY);

        $refund = new Refund();
        $refund->setAmount($amt);

        $sale = new Sale();
        $sale->setId($refund_array['sale_id']);

        try {
            $refundedSale   = $sale->refund($refund, $self->apiContext);
            $refundedData = (array)json_decode(json_encode(json_decode($refundedSale), 128));
            \Models\Payments::update_transaction(
                $refund_array['id_transactions'],
                [
                    'transaction_done_by'       => -1,
                    'transaction_reference_id'  => $refundedData['id'], 
                    'transaction_status'        => 'refunded',
                    'updated'                   => date('Y-m-d H:i:s')
                ]
            );
            
            self::paypal_response([
                'user_id'                   => $refund_array['user_id'],
                'response_json'             => json_encode(json_decode($refundedSale), 128),
                'request_type'              => 'refund payment',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);

        }catch (PayPalConnectionException $ex) {
            self::paypal_response([
                'user_id'                   =>$refund_array['user_id'],
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'refund payment',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'        => false,
                'message'       => 'M0550'
            ];
        }catch (PayPalConfigurationException $ex) {
            self::paypal_response([
                'user_id'                   => $refund_array['user_id'],
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'accept proposal payment',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'        => false,
                'message' => 'M0550'
            ];
        }catch (PayPalInvalidCredentialException $ex) {
            self::paypal_response([
                'user_id'                   => $refund_array['user_id'],
                'response_json'             => json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'accept proposal payment',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'        => false,
                'message' => 'M0550'
            ];
        } catch (PayPalMissingCredentialException $ex) {
            self::paypal_response([
                'user_id'                   => $refund_array['user_id'],
                'response_json'             =>json_encode(['code' => json_decode($ex->getCode()),'data' => json_decode($ex->getData())]),
                'request_type'              => 'accept proposal payment',
                'status'                    => 'false',
                'created'                   => date('Y-m-d H:i:s')
            ]);
            return [
                'status'        => false,
                'message' => 'M0550'
            ];
        }
        return [
            'status' => true,
            'result' => $refundedSale   
        ];
    }

    public static function save_transaction($data){
        if(!empty($data)){
            return $isInserted = DB::table('transactions')->insertGetId($data);
        }else{
            return [
                'status'    => false,
                'message'   => 'M0550'
            ];
        }
    }   
}
