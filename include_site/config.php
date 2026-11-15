<?php

$dbserver = '127.0.0.1';
$dbname = 'expertsacademy_dev';
$dbusername = 'experts_admin';
$dbpassword = 'experts_password';

$language = array('1' => 'fa','2' => 'en');
$language_count = count($language);
$language_default = $language[1];
$directory = '../upload_site/';

$get_domain = $_SERVER['HTTP_HOST'];
