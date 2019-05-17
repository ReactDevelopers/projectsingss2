<?php

use Aws\Laravel\AwsServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration options set in this file will be passed directly to the
    | `Aws\Sdk` object, from which all client objects are created. The minimum
    | required options are declared here, but the full set of possible options
    | are documented at:
    | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    |
    */

    'credentials' => [
        'key'    => 'AKIAI5OMEKPREEFMP7TA',
        'secret' => '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',
    ],
    'region' => 'ap-singapore',//'us-west-2',
    'version' => 'latest',
    'user' => 'prj_mummy_fique',
    'bucket_name' => 'proj-mummy-fique',

    // You can override settings for specific services
    'Ses' => [
        'region' => 'us-east-1',
    ],
    'ua_append' => [
        'L5MOD/' . AwsServiceProvider::VERSION,
    ],
];
