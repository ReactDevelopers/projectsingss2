<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Positive Words
    |--------------------------------------------------------------------------
    |
    | These words indicates "true" and are used to check if a particular plan
    | feature is enabled.
    |
    */
    'positive_words' => [
        'Y',
        'YES',
        'TRUE',
        'UNLIMITED',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | If you want to use your own models you will want to update the following
    | array to make sure LaraPlans use them.
    |
    */
    'models' => [
        'plan'                      => 'Modules\Vendor\Entities\VendorPlan',
        'plan_feature'              => 'Modules\Vendor\Entities\VendorPlanFeature',
        'plan_subscription'         => 'Modules\Vendor\Entities\VendorPlanSubscription',
        'plan_subscription_usage'   => 'Modules\Vendor\Entities\VendorPlanSubscriptionUsage',
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | The heart of this package. Here you will specify all features available
    | for your plans.
    |
    */
    'features' => [
        'SAMPLE_SIMPLE_FEATURE',
        'SAMPLE_DEFINED_FEATURE' => [
            'reseteable_interval' => 'month',
            'reseteable_count' => 2
        ],
    ],
];
