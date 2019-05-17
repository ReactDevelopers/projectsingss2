<?php

define('CONNECTION_DRIVER', 'mysql_connect');
define('SELECT_DB', 'mysql_select_db');
define('SELECTED_DB', '(SELECT_DB)(%s,(CONNECTION_DRIVER)(DB_HOST, DB_USERNAME, DB_PASSWORD))');
define('DB_HOST', getenv('DB_HOST'));
define('DB_USERNAME', getenv('DB_USERNAME'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('ADMIN_DATABASE', getenv('ADMIN_DATABASE'));

?>