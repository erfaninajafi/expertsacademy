<?php

$dbserver = 'localhost';
$dbname = 'tarfa';
$dbusername = 'root';
$dbpassword = '';


function Query ($query)
{
    global $dbserver,$dbname,$dbusername,$dbpassword;
    $con=  mysqli_connect("$dbserver", "$dbusername", "$dbpassword", "$dbname");
    mysqli_set_charset( $con, 'utf8' );
    if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $select = mysqli_select_db($con, "$dbname");
    $result = mysqli_query($con, $query);
    return $result ;
}

function isValidEmail($email){
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email))
        {
            return false;
        }

        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++)
        {
             if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i]))
             {
                return false;
             }
        }
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1]))
        {
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2)
            {
                return false;
            }
            for ($i = 0; $i < sizeof($domain_array); $i++)
            {
                if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i]))
                {
                    return false;
                }
            }
        }
        return true;
    }