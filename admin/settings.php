<?php
/*
 *  Twig Admin Center Index Page
 *  Author: Gregory Schoeman
*/

use Dash\DashSettings;
use Dash\DashSetup;

require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
lock($pdo);
$settings = new DashSettings($pdo);

$template = "sb_settings.twig";
$setting = $settings->getAll();

$values = array(
    'page' => array(
        'title'         => 'Add a new user',
        'description'   => 'Admin add new user form',
        'class'         => 'addUser',
        'pic'           => ''
    ),
    'setting'           => $settings
);
echo $twig->render($template, $values);
