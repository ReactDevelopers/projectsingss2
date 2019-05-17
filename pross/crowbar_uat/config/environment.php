<?php
    return [
        /*
        |--------------------------------------------------------------------------
        | User Defined Variables
        |--------------------------------------------------------------------------
        |
        | This is a set of variables that are made specific to this application
        | that are better placed here rather than in .env file.
        | Use config('your_key') to get the values.
        |
        */
        'PUSH_NOTIFICATION' => 
            [
                'local' => [
                    'ANDROID_GOOGLE_API_KEY'    => 'AAAAWHl6XLU:APA91bGGKN0TS15w2AnfWCu2n3EzwSEl57i_ffa249ltNyyRiA-44QmVXbmuqLYt1zbtowb9FsZ6UAsLaJQ9sxGEe5I0TgsAicg17LM5RpWI3v7rkb6m8U-27gahV_aj_pSVw6S0oAKu',
                    'IOS_APPLE_CERTIFICATE'     => app_path('certificate/local/apns-dev.pem'),
                    'IOS_APPLE_PASSWORD'        => 'Lucknow1',
                ],
                'development' => [
                    'ANDROID_GOOGLE_API_KEY'    => 'AAAAWHl6XLU:APA91bGGKN0TS15w2AnfWCu2n3EzwSEl57i_ffa249ltNyyRiA-44QmVXbmuqLYt1zbtowb9FsZ6UAsLaJQ9sxGEe5I0TgsAicg17LM5RpWI3v7rkb6m8U-27gahV_aj_pSVw6S0oAKu',
                    'IOS_APPLE_CERTIFICATE'     => app_path('certificate/development/apns-dev.pem'),
                    'IOS_APPLE_PASSWORD'        => 'Lucknow1',
                ],
                'staging' => [
                    'ANDROID_GOOGLE_API_KEY'    => 'AAAAWHl6XLU:APA91bGGKN0TS15w2AnfWCu2n3EzwSEl57i_ffa249ltNyyRiA-44QmVXbmuqLYt1zbtowb9FsZ6UAsLaJQ9sxGEe5I0TgsAicg17LM5RpWI3v7rkb6m8U-27gahV_aj_pSVw6S0oAKu',
                    'IOS_APPLE_CERTIFICATE'     => app_path('certificate/staging/apns-live.pem'),
                    'IOS_APPLE_PASSWORD'        => 'Lucknow1',
                ],
                'preproduction' => [
                    'ANDROID_GOOGLE_API_KEY'    => 'AAAAWHl6XLU:APA91bGGKN0TS15w2AnfWCu2n3EzwSEl57i_ffa249ltNyyRiA-44QmVXbmuqLYt1zbtowb9FsZ6UAsLaJQ9sxGEe5I0TgsAicg17LM5RpWI3v7rkb6m8U-27gahV_aj_pSVw6S0oAKu',
                    'IOS_APPLE_CERTIFICATE'     => app_path('certificate/preproduction/apns-live.pem'),
                    'IOS_APPLE_PASSWORD'        => 'Lucknow1',
                ],
                'production' => [
                    'ANDROID_GOOGLE_API_KEY'    => 'AAAAWHl6XLU:APA91bGGKN0TS15w2AnfWCu2n3EzwSEl57i_ffa249ltNyyRiA-44QmVXbmuqLYt1zbtowb9FsZ6UAsLaJQ9sxGEe5I0TgsAicg17LM5RpWI3v7rkb6m8U-27gahV_aj_pSVw6S0oAKu',
                    'IOS_APPLE_CERTIFICATE'     => app_path('certificate/production/apns-live.pem'),
                    'IOS_APPLE_PASSWORD'        => 'Lucknow1',
                ],
            ]
    ];
