<?php
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
// lock2($pdo);

$tasks = new \Dash\Tasks($pdo);

$template = "timer_dash.twig";

$values = array(
    'page' => array(
        'title' => "Timecop",
        'description' => "Work time tracker",
        'class' => "dashboard",
        'pic' => ''
    ),
    'statuses' => $tasks->getStatuses(),
    'tasks' => $tasks->getByStatus('open')
);

echo $twig->render($template, $values);