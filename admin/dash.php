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
    'statuses' => $tasks->getStatuses(),
    'tasks' => $tasks->getByStatus('open', $user['id'])
);

echo $twig->render($template, $values);