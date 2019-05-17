<?php
//file : app/config/constant.php

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
		'ACCOUNT_WAS_CREATED'			=> '121',
		'WRONG_PASSWORD'			=> '10',

		// payment
		'TOKEN_INVALID'				=> '111',
		'TOKEN_IN_USED'				=> '112',
		'CARD_INVALID'				=> '113',
		'ADD_ITEM_FAILURED'			=> '114'		
	],	
	'default' => [
		// auth
		'urlS3'	=>	'https://s3-ap-southeast-1.amazonaws.com/proj-mummy-fique',
		'take' 	=> 14,
		'LatestReview' => 1,
		'OldestReview'	=> 2,
		'HighestRating' => 3,
		'LowestRating' => 4,
		'imageMLink1'	=>	'https://s3-ap-southeast-1.amazonaws.com/proj-mummy-fique/assets/media/vendor.png',
		'imageMLink2'	=>	'https://s3-ap-southeast-1.amazonaws.com/proj-mummy-fique/assets/media/vendor1.png',
		'all' => 1,
		'favourite' => 2,
		'view' => 3,

	],
	'inbox' => [
		// auth
		'inbox' => 1,
		'send'	=> 2,
		'trash' => 3,
		'LatestMessage' => 1,
		'OldestMessage'	=> 2,
		'MakeRead'	=> 3,
	],
	'package' => [
		// auth
		'1' => 'Free package',
		'2'	=> 'Sliver package',
		'3' => 'Gold package',
	],
	'sort' => [
		'portfolio' => [
			'lastest' => '1',
			'popularity' => '2',
			'most_viewed' => '3'
		]
	],
	'version' => [
		'android-version' => 'version::android-version',
		'ios-version' => 'version::ios-version',
		// 'url' => 'version::url',
		'description' => 'version::description',
	],
	'instagram_api' => [
		'get_user_media' => 'https://api.instagram.com/v1/users/self/media/recent/?access_token=',
	],
	'other_contact' => [
		'whatsapp' => 'Whatsapp',
		'line' => 'Line',
		'skype' => 'Skype',
		'wechat' => 'Wechat',
		'snapchat' => 'Snapchat',
		'qq' => 'QQ',
		'viber' => 'Viber',
		'telegram' => 'Telegram',
		'messenger' => 'Messenger',
		'kakao_talk' => 'KakaoTalk',
	],
];