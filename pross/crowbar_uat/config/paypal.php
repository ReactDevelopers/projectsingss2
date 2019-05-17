<?php
    return [
    'mode' => getenv('PAYPAL_ENV')?:'sandbox', // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.
    'sandbox' => [
        'username'          => getenv('PAYPAL_USERNAME')?:'santosh_api1.singsys.com',                               // Api Username
        'password'          => getenv('PAYPAL_PASSWORD')?:'VZEQNNPRML6F54CR',                                       // Api Password
        'secret'            => getenv('PAYPAL_SIGN')?:'AFcWxV21C7fd0v3bYYYRCpSSRl31A.t5R0DSvr2VkN.oaimU-BG2UthF',   // This refers to api signature
        'certificate'       => '',                                                                                  // Link to paypals cert file, storage_path('cert_key_pem.txt')
        'app_id'            => getenv('PAYPAL_APPID')?:'APP-80W284485P519543T',
        'endpoint'          => 'https://api-3t.sandbox.paypal.com/nvp',                                             // Can Only Be 'Sale', 'Authorization', 'Order'
        'api_url'          => 'https://api-3t.sandbox.paypal.com/nvp',                                            // Can Only Be 'Sale', 'Authorization', 'Order'
        'auth_token'        => null,
        'auth_signature'    => null,
        'auth_timestamp'    => null,
    ],
    'live' => [
        'username'          => getenv('PAYPAL_USERNAME')?:'',   // Api Username
        'password'          => getenv('PAYPAL_PASSWORD')?:'',   // Api Password
        'secret'            => getenv('PAYPAL_SIGN')?:'',       // This refers to api signature
        'certificate'       => '',                              // Link to paypals cert file, storage_path('cert_key_pem.txt')
        'app_id'            => getenv('PAYPAL_APPID')?:'APP-80W284485P519543T',
        'endpoint'          => 'https://api-3t.paypal.com/nvp', // Can Only Be 'Sale', 'Authorization', 'Order'
        'api_url'          => 'https://api-3t.paypal.com/nvp',
        'auth_token'        => null,
        'auth_signature'    => null,
        'auth_timestamp'    => null,
    ],
    'payment_action'    => 'Sale',  // Can Only Be 'Sale', 'Authorization', 'Order'
    'currency'          => 'USD',
    'notify_url'        => '',      // Change this accordingly for your application.

    /**
     * Set our Sandbox and Live credentials
     */
    'client_id' => env('PAYPAL_CLIENT_ID', ''),
    'paypal_secret' => env('PAYPAL_SECRET', ''),

    
    /**
     * SDK configuration settings
     */
    'settings' => array(

        /** 
         * Payment Mode
         *
         * Available options are 'sandbox' or 'live'
         */
        'mode' => env('PAYPAL_ENV', 'sandbox'),
        
        // Specify the max connection attempt (3000 = 3 seconds)
        'http.ConnectionTimeOut' => 3000,
       
        // Specify whether or not we want to store logs
        'log.LogEnabled' => true,
        
        // Specigy the location for our paypal logs
        'log.FileName' => storage_path() . '/logs/paypal.log',
        
        /** 
         * Log Level
         *
         * Available options: 'DEBUG', 'INFO', 'WARN' or 'ERROR'
         * 
         * Logging is most verbose in the DEBUG level and decreases 
         * as you proceed towards ERROR. WARN or ERROR would be a 
         * recommended option for live environments.
         * 
         */
        'log.LogLevel' => 'DEBUG'
    )
];