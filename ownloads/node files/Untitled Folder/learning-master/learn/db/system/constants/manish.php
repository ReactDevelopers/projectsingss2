<?php

define('CONNECTION_DRIVER', 'mysqli_connect');
define('SELECT_DB', 'mysqli_select_db');
define('DB_HOST', getenv('DB_HOST'));
define('DB_USERNAME', getenv('DB_USERNAME'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('ADMIN_DATABASE', getenv('ADMIN_DATABASE'));

?>