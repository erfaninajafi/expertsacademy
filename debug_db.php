<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once './include_site/config.php';
echo "Server: $dbserver, User: $dbusername, DB: $dbname<br>";
$conn = mysqli_connect($dbserver, $dbusername, $dbpassword, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully to " . $dbname;
?>
