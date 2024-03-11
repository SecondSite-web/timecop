<?php

require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
lock2($pdo);

use Dash\DashAuth;
$tasks = new \Dash\Tasks($pdo);
$dashAuth = new DashAuth($pdo);
$user = $dashAuth->sessionUser();

$template = "timer_dash.twig";

$values = array(
    'page' => array(
        'title' => "Timecop",
        'description' => "Work time tracker",
        'class' => "dashboard",
        'pic' => ''
    ),
    'projects' => $tasks->getProjectsByStatus('open', $user['id']),
    'statuses' => $tasks->getStatuses(),
);

echo $twig->render($template, $values);