<?php

use Dash\DashAuth;

require_once __DIR__ .'/dash-loader.php';

$dashAuth = new DashAuth($pdo);
$user = $dashAuth->sessionUser();
$siteUrl = currentUrl();
$timerUrl = $siteUrl."admin/dash/";
$adminUrl = $siteUrl."admin/";

if ($user === false) {
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
} else if($user['user_group'] !== "root") {
    header("Location: ".$timerUrl."");
    exit;
} else {
    header("Location: ".$adminUrl."");
    exit;
}
