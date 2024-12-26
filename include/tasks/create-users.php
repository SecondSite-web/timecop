<?php
require_once __DIR__ .'/dash-loader.php';
use PHPAuth\Auth as PHPAuth;
use PHPAuth\Config as PHPAuthConfig;
$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);


$email = 'gregory@realhost.co.za';
$password = 'a6B345*gQ_56fGG';
$params = array(
'first_name' => 'Greg',
'last_name' => 'Schoeman',
'user_group' => 'root',
'phone' => '0799959818',
'company_id' => 1
);
$params['api_key'] = md5Array($params);

$result = $auth->register(
$email,
$password,
$password,
$params,
$captcha_response = '',
$use_email_activation = false
);