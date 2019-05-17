<?php

return [
    /*
     * In order to integrate the Facebook SDK into your site,
     * you'll need to create an app on Facebook and enter the
     * app's ID and secret here.
     *
     * Add an app: https://developers.facebook.com/apps
     *
     * You can add additional config options here that are
     * available on the main Facebook\Facebook super service.
     *
     * https://developers.facebook.com/docs/php/Facebook/5.0.0#config
     *
     * Using environment variables is the recommended way of
     * storing your app ID and app secret. Make sure to update
     * your /.env file with your app ID and secret.
     */
    'facebook_config' => [
        /*'app_id' => '179227956075971',//live credentials
        'app_secret' => '4f74478b6a2a75100c2c634bed67566c',//live credentials
        'default_graph_version' => 'v2.8',//live credentials*/

        'app_id' => '477361539443750',//stg credentials
        'app_secret' => '59a999d846f8d8094d5bb04935bf7bdb',//stg credentials
        'default_graph_version' => 'v2.8',//stg credentials
       
        // 'app_id' => '1862688427389070',
        // 'app_secret' => 'c19637c1904ab0d57a1a454c62e92f9c',
        //'enable_beta_mode' => true,
        //'http_client_handler' => 'guzzle',
    ],

    /*
     * The default list of permissions that are
     * requested when authenticating a new user with your app.
     * The fewer, the better! Leaving this empty is the best.
     * You can overwrite this when creating the login link.
     *
     * Example:
     *
     * 'default_scope' => ['email', 'user_birthday'],
     *
     * For a full list of permissions see:
     *
     * https://developers.facebook.com/docs/facebook-login/permissions
     */
    'default_scope' => [],

    /*
     * The default endpoint that Facebook will redirect to after
     * an authentication attempt.
     */
    'default_redirect_uri' => '/facebook/callback',
    ];