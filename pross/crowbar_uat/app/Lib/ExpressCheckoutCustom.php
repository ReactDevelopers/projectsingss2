<?php

namespace App\Lib;

use Srmklive\PayPal\Services\ExpressCheckout;
use Carbon\Carbon;

class ExpressCheckoutCustom extends ExpressCheckout
{

	
	/**
     * PayPal Processor Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
    	
    	parent::__construct($config);
    }

    public function createMonthlySubscription($token, $amount, $description,$currency='USD', $cycles=0)
    {
        $data = [
            'PROFILESTARTDATE'  => Carbon::now()->toAtomString(),
            'DESC'              => $description,
            'BILLINGPERIOD'     => 'Month',
            'BILLINGFREQUENCY'  => 1,
            'AMT'               => $amount,
            'CURRENCYCODE'      => $currency,
            'TOTALBILLINGCYCLES' => $cycles
        ];

        return $this->createRecurringPaymentsProfile($data, $token);
    }

}