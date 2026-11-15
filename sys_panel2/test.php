<?php
phpinfo();
$dbserverSql='178.252.166.106,1440';
//$dbserverSql='192.168.0.10,17110';
$dbnameSql='IGX';
$dbusernameSql='sa';
$dbpasswordSql='123@@sa';

$connectionInfo = array(
    "Database" => $dbnameSql,
    "UID" => $dbusernameSql,
    "PWD" => $dbpasswordSql,
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($dbserverSql, $connectionInfo);
if (!$conn) {
    //die(print_r(sqlsrv_errors(), true));
}
$query = "SELECT * FROM W_4AHSNCaseContractQP";
$resultSql = sqlsrv_query($conn, $query);

var_dump($resultSql);