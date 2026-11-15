<?php
require_once './include_site/config.php';
 = mysqli_connect($dbserver, $dbusername, $dbpassword, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully to " . $dbname;
?>
