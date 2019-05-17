<?php
namespace App\Lib;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;

use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;


class PayPalPlan {
	
	private $apiContext;
	private $mode;
	private $client_id;
	private $secret;

	function __construct(){

		$this->client_id = config('paypal.client_id');
	    $this->secret = config('paypal.paypal_secret');
        $this->apiContext = new ApiContext(new OAuthTokenCredential($this->client_id, $this->secret));
        $this->apiContext->setConfig(config('paypal.settings'));
	}

	public function create($plan_name, $amount, $cycles, $plan_description, $frequency_interval){

		$plan = new Plan();
		$plan->setName($plan_name)
		->setDescription($plan_description)
		->setType('fixed');


	}
}