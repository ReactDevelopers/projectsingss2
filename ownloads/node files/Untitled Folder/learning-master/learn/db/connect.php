<?php 

ob_start();
@session_start();
date_default_timezone_set('Asia/Singapore');

$con = (CONNECTION_DRIVER)(DB_HOST, DB_USERNAME, DB_PASSWORD);

if ($con->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$db_selected = (SELECT_DB)((CONNECTION_DRIVER)(DB_HOST, DB_USERNAME, DB_PASSWORD),ADMIN_DATABASE);
var_dump($db_selected);

if (!$db_selected) {
    die ('Database is not selected : ' . (CONNECTION_DRIVER)());
}
echo "Connected successfully";


?>