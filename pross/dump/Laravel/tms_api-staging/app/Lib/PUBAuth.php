<?php

namespace App\Lib;
use Illuminate\Support\Facades\Mail;

class PUBAuth {

	public $status = false;
	public $message = '';
	public $user= null;

	public $serviceDeskEmail = '';	
	# This message will display, when application is in development mode, and anyone try to login with pubnet id, which is not listed under the 'APP_ALLOWED_USERS' key in .env file.
	public $messageWhenAppInDevelopmentInvalidPubnetId = '';
	
	# This message will display, when user LDAP credentials are valid but the system does not get the pubnet id from the LDAP connection.
	public $messageLdapAuthSuccessNotGetPubnetId = '';

	# This message will display, when user LDAP credentials are not valid.
	public $messageWhenLdapAuthFail = '';

	# This message will display when the User credentials are valid and user does not exist in application database or user is not authorized to access this application.
	public $messageWhenUserNotExist = '';

	# Credentials are valid, but you don't have access rights to this application.
	public $messageNotHaveAccessrights =  '';

	# This message will display after successful login
	public $messageLoginSuccess = 'Login Success';

	function __construct () {


		$errorMessage = new GetAuthErrorMessage();
		$this->messageWhenLdapAuthFail = $errorMessage->getCase(0);
		$this->messageLdapAuthSuccessNotGetPubnetId = $errorMessage->getCase(1);
		$this->messageWhenUserNotExist = $errorMessage->getCase(2);
		$this->messageNotHaveAccessrights = $errorMessage->getCase(3);
		$this->messageWhenAppInDevelopmentInvalidPubnetId = $errorMessage->getCase(4);
		$this->serviceDeskEmail = $errorMessage->getServiceDeskEmail();
	}
	
	/**
	 * checking for valid user
	 **/
	public function check($username, $password) {

		$pubnet_id = $this->isDevelopmentMode()  ? $this->checkValidPubnetIdWhenDevelopment($username) : $this->checkLdapAuthentication($username, $password);

		return $pubnet_id ? $this->getUserByPubnetId($pubnet_id) :  false;
	}
	/**
	 * Check the Given personnel number is allow to access the application when Development Mode is On.
	 */
	protected function checkValidPubnetIdWhenDevelopment($username) {

		$pubnet_id = null;

		if(in_array($username, $this->whoCanLoginInDevMode()) ){
			$this->status = true;        		
    		$pubnet_id = $username;

    	}else{

    		$this->status = false;
    		$this->message = $this->messageWhenAppInDevelopmentInvalidPubnetId;
    	}

    	return $pubnet_id;
	}


	/**
	 * Checking the development mode is Enable or Not
	 * @return boolean
	 */
	public function isDevelopmentMode() {

		return env('DEVELOPMENT_MODE', true);
	}

	/**
	 * Getting the allow user, who can login, when app is development mode.
	 * @return Array
	 */
	public function whoCanLoginInDevMode() {

		$allowed_users = env('APP_ALLOWED_USERS', "");
		$allowed_users = explode(',', $allowed_users);
		return $allowed_users;
	}


	protected function checkLdapAuthentication($username, $password) {
		
		try {

			$adServer = "ldap://PUBDC01.pubnet.sg";
			$ldap = ldap_connect($adServer);
			$ldapr = 'pubnet' . "\\" . $username;
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
			$bind = @ldap_bind($ldap, $ldapr, $password);	

			$filter = "(sAMAccountName=$username)";

			$result = @ldap_search($ldap, "dc=pubnet,dc=sg", $filter);
			$info = @ldap_get_entries($ldap, $result);

			//$info = [['info'=>[2324242]]];
			//$info = null;
			//$info = [['mail'=>['sanjeet@singsys.com'] ]];

			$pubnet_ID = isset($info[0]['info'][0]) ? $info[0]['info'][0] :null;

			if($info && !$pubnet_ID ) {

	  			$this->status = false;
	  			$this->message = $this->messageLdapAuthSuccessNotGetPubnetId;
	  			//$this->sendEmail($username, $info);

			} elseif (!$info) {

				$this->status = false;
	  			$this->message = $this->messageWhenLdapAuthFail;

			}else{

				$this->status = true;
	  			$this->message = 'Login Success';
			}

			return $pubnet_ID;

		} catch(\Exception $e) {

			$this->status = false;
			$this->message = $e->getMessage();
			return false;			
		}
	}

	/**
	 * getting the user information from the database through the PUBNET ID
	 * @return boolean | Number
	 */

	public function getUserByPubnetId($pid) {

		$this->user = $user = \App\User::where('personnel_number', $pid)->first();

    	if(!$user) {

    		$this->status = false;
    		$this->message = $this->messageWhenUserNotExist;
    		return false;

    	}elseif($user && !$user->role_id){

    		$this->status = false;
    		$this->message = $this->messageNotHaveAccessrights;
    		return false;

    	}else{

    		$user->update(['num_success_login'=> \DB::raw('num_success_login +1'),'last_success_login_attemp'=>date('Y-m-d H:i:s')]);
    		$this->status = true;
  			$this->message = $this->messageLoginSuccess;
    		return $user->id;
    	}
	}

	/** 
	 * This function is using to send the email only.
	 */
	public function sendEmail($username, $info) {

		$user_email = isset($info[0]['mail'][0]) ? $info[0]['mail'][0] : null;
		$cc_email_text = $user_email ? '<br><br>CC. '.$user_email : '';

		$service_desk_email = $this->serviceDeskEmail;

		try{

			$content = 'Hi, Help Desk Team,<br><br>We would like to inform you that user <b>"'.$username.'"</b> is unable to log in TMS Application because he system is unable to find their personal number from LDAP. <br><br> Please check from your side and update user personal number on the LDAP server & inform the user. '.$cc_email_text.' <br/><br> Thanks <br/> TMS Team';

			Mail::send([], [], function($m) use ($content, $user_email, $service_desk_email) {
	                    
	            $m->setBody($content, 'text/html');
	            $m->to($service_desk_email);
	            $m->from('no-reply@tms.inhousedev.pub.gov.sg');
	            $m->subject('Regarding failed login for the user in TMS');           
	            if($user_email) {

	            	$m->cc($user_email);
	            }
	        });

		}catch(\Exception $e){

			\Log::info($content);
			\Log::info($e->getMessage());
		}
	}
}