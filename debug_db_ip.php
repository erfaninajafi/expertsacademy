<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$dbserver = '127.0.0.1';
$dbusername = 'root';
$dbpassword = '';
$dbname = 'expertsacademy_dev';
$conn = mysqli_connect($dbserver, $dbusername, $dbpassword, $dbname);
if (!$conn) {
    die("Connection failed (127.0.0.1): " . mysqli_connect_error());
}
echo "Connected successfully to " . $dbname;
?>
