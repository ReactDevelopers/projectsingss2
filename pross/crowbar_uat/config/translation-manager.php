<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */
	'route' => [
        'prefix' => 'administrator/translations',
        'middleware' => [
	        '\App\Http\Middleware\AdminAuth',
		],
    ],

	/**
	 * Enable deletion of translations
	 *
	 * @type boolean
	 */
	'delete_enabled' => true,

	/**
	 * Exclude specific groups from Laravel Translation Manager. 
	 * This is useful if, for example, you want to avoid editing the official Laravel language files.
	 *
	 * @type array
	 *
	 * 	array(
	 *		'pagination',
	 *		'reminders',
	 *		'validation',
	 *	)
	 */
	'exclude_groups' => array(
		'vendor',
		'pagination',
		'passwords',
		'validation',
		'admin',
		'auth',
		'_json',
		'payment',
		'laravel-share/en/laravel-share',
	),

	/**
	 * Export translations with keys output alphabetically.
	 */
	'sort_keys ' => false,

);
