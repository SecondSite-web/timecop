<?php
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
lock($pdo);

// $dashAuth = new \Dash\DashAuth($pdo);

$template = "sb_dash.twig";
$groupUsers = $dashAuth->groupCounter();
$values = array(
    'page' => array(
        'title' => "Dashboard",
        'description' => "Welcome to the Fuelify Dashboard",
        'class' => "dashboard",
        'pic' => ''
    ),
    'groupUsers' => $groupUsers,
);

echo $twig->render($template, $values);