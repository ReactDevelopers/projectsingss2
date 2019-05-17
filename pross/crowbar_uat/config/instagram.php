<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Instagram Client Details
    |--------------------------------------------------------------------------
    |
    | When you run `artisan vendor:publish`, this config file will be copied
    | to `config/instagram.php`.
    |
    | Please register your application as an Instagram Client here:
    | https://www.instagram.com/developer/
    |
    | When registering your client, be sure to provide all valid redirect
    | URIs that you will use in testing, staging, and production.
    |
    | After registering, either update the values directly in
    | `config/instagram.php` or add them as the following environment
    | variables to your local `.env` file.
    |
    | * INSTAGRAM_CLIENT_ID
    | * INSTAGRAM_CLIENT_SECRET
    | * INSTAGRAM_REDIRECT_URI
    |
    */

    'clientId'      => getenv('INSTAGRAM_CLIENT_ID')?:'ceb75463b3694705a148ae23ec84517b',
    'clientSecret'  => getenv('INSTAGRAM_CLIENT_SECRET')?:'4c07f808a80a4659a18f3533cf123c03',
    'redirectUri'   => getenv('INSTAGRAM_REDIRECT_URI')?:'http://115.249.91.203:6161/crowbar/public/en/login/instagram/callback',

];
