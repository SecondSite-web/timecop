<?php

use Dash\DashAuth;

require_once __DIR__ .'/dash-loader.php';

use PHPAuth\Auth as PHPAuth;
use PHPAuth\Config as PHPAuthConfig;
$config = new PHPAuthConfig($pdo);
$auth = new PHPAuth($pdo, $config);



$siteUrl = currentUrl();
$timerUrl = $siteUrl."admin/dash/";
$adminUrl = $siteUrl."admin/";

if (!$auth->isLogged()) {
    $template = "user_login.twig";

    $values = array(
        'page' => array(
            'title' => "Home",
            'description' => "Home Page Description",
            'class' => "home",
            'pic' => 'site-img.png'
        )
    );

    echo $twig->render($template, $values);
} else {
    header("Location: ".$timerUrl."");
    exit;
}
