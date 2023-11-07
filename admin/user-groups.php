<?php
/*
 *  Twig Admin Center Index Page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
lock($pdo);

$template = "user_user_groups.twig";
$users = $dashAuth->getAllUsers();
$groups = $dashAuth->getGroups();

$values = array(
    'page' => array(
        'title'         => 'Add a new user',
        'description'   => 'Admin add new user form',
        'class'         => 'addUser',
        'pic'           => ''
    ),
    'groups'            => $groups,
    'users'             => $users
);
echo $twig->render($template, $values);
