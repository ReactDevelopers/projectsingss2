<?php
//file : app/config/constants.php

return [
	'status_code' => [
		// auth
		'EMAIL_PASSWORD_INVALID' 	=> '101',
		'REQUIRED_ACTIVE_ACCOUNT' 	=> '102',
		'EMAIL_ALREADY_EXIST' 		=> '103',
	    'SEND_MAIL_ERROR' 			=> '104',
	    'PASSWORD_INVALID' 			=> '105',
	    'ACCOUNT_INVALID'			=> '106',
	    'FILE_UPLOAD_NOT_FOUND'		=> '107',
	    'EMAIL_NOT_FOUND' 			=> '108',
	    'LOGIN_FAILURE'				=> '109',
		'SETTING_NOT_FOUND'			=> '110',
		'ACCOUNT_WAS_CREATED'			=> '1',
		'WRONG_PASSWORD'			=> '10',

		// payment
		'TOKEN_INVALID'				=> '111',
		'TOKEN_IN_USED'				=> '112',
		'CARD_INVALID'				=> '113',
		'ADD_ITEM_FAILURED'			=> '114'		
	]	
];