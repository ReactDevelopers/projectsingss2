<?php
/*
 *---------------------------------------------------------------
 * SYSTEM DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" directory.
 * Set the path if it is not in the same directory as this file.
 */
$system_path = 'system';

/*
* ---------------------------------------------------------------
*  Resolve the system path for increased reliability
* ---------------------------------------------------------------
*/

// Set the current directory correctly for CLI requests
if (defined('STDIN'))
{
	chdir(dirname(__FILE__));
}

if (($_temp = realpath($system_path)) !== FALSE)
{
	$system_path = $_temp.DIRECTORY_SEPARATOR;
}
else
{
	// Ensure there's a trailing slash
	$system_path = strtr(
		rtrim($system_path, '/\\'),
		'/\\',
		DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
	).DIRECTORY_SEPARATOR;
}

// Is the system path correct?
if ( ! is_dir($system_path))
{
	header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
	echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
	exit(3); // EXIT_CONFIG
}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
// The name of THIS file
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// Path to the system directory
define('BASEPATH', $system_path);

// Path to the front controller (this file) directory
define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

// Name of the "system" directory
define('SYSDIR', basename(BASEPATH));

/*
 * --------------------------------------------------------------------
 * LOAD PHP DOT ENV FILE
 * --------------------------------------------------------------------
 *
 *
 */
require_once BASEPATH . 'dotenv/autoloader.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

/*
 * --------------------------------------------------------------------
 * LOAD CONSTANTS
 * --------------------------------------------------------------------
 */
require_once BASEPATH . 'constants/'.getenv('WEB_ENV').'.php';

/*
 * --------------------------------------------------------------------
 * LOAD ROUTES
 * --------------------------------------------------------------------
 */
require_once BASEPATH . 'routes.php';


/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
define('ENVIRONMENT', getenv('WEB_ENV'));

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch (ENVIRONMENT)
{
	case 'manish':
	case 'development':
		error_reporting(1);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1);
}

if(!empty($_SERVER['PATH_INFO'])) {
	if(in_array($_SERVER['PATH_INFO'], array_keys(${getenv('WEB_ROUTES')}))) {
		${getenv('LOAD_FILE')} = sprintf('%s%s',FCPATH,${getenv('WEB_ROUTES')}[$_SERVER['PATH_INFO']]);
		if(file_exists(${getenv('LOAD_FILE')})){
			include_once(${getenv('LOAD_FILE')});
		} else {
			die('File Not Found');
		}
	}
} else {
	die('welcome');
}
