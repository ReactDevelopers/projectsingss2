<?php
	return [
		'debug'               => function_exists('env') ? env('APP_DEBUG', false) : false,
		'API_URL'             => 'api.twitter.com',
		'UPLOAD_URL'          => 'upload.twitter.com',
		'API_VERSION'         => '1.1',
		'AUTHENTICATE_URL'    => 'https://api.twitter.com/oauth/authenticate',
		'AUTHORIZE_URL'       => 'https://api.twitter.com/oauth/authorize',
		'ACCESS_TOKEN_URL'    => 'https://api.twitter.com/oauth/access_token',
		'REQUEST_TOKEN_URL'   => 'https://api.twitter.com/oauth/request_token',
		'USE_SSL'             => true,

		'CONSUMER_KEY'        => 'LV0VvEQSb3UoD7lsF3Rf1xu3E',
		'CONSUMER_SECRET'     => 'dNCgOGRhW7BhRsGMesFmtAkmenCVZDQCAjV4tLOl8848B9YkiN',
		'ACCESS_TOKEN'        => '902984289905770497-nVaQBy2WgRVUnZ98vYeRGscDH8Y7KSo',
		'ACCESS_TOKEN_SECRET' => 'Zc1UsKv3jSxY8fxTf80rsli7fawQ6IDKyM5oX93Gp9nzH',
	];
