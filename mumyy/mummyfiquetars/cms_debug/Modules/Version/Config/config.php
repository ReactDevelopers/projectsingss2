<?php

return [
	'name' => 'Version',
	'config' => [
		[
			'title' => 'android-version',
			'name' => 'version::android-version',
			'view' => 'text',
			'required' => true,
		],
		[
			'title' => 'ios-version',
			'name' => 'version::ios-version',
			'view' => 'text',
			'required' => true,
		],
		[
			'title' => 'description',
			'name' => 'version::description',
			'view' => 'textarea',
			'required' => false,
		],
	]
];