<?php
/*
 *  Twig Admin Center Index Page
 *  Author: Gregory Schoeman
*/

use Dash\Permissions;

require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
lock($pdo);
$dashAuth = new \Dash\DashAuth($pdo);
$verify = new Permissions($pdo);

$template = "user_permissions.twig";

$users = $dashAuth->getAllUsers();
$userGroups = $dashAuth->getGroups();


$permissions = $verify->getAllPermissions();
$values = array(
    'page' => array(
        'title'         => 'Add a new user',
        'description'   => 'Admin add new user form',
        'class'         => 'addUser',
        'pic'           => ''
    ),
    'userGroups'        => $userGroups,
    'users'             => $users,
    'permissions'       => $permissions
);
echo $twig->render($template, $values);
