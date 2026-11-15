<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$conn = mysqli_connect('localhost', 'experts_admin', 'experts_password', 'expertsacademy_dev');
if (!$conn) {
    echo "Connection failed: " . mysqli_connect_error();
} else {
    echo "Connected successfully!";
}
?>
