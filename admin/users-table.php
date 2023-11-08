<?php
/*
 *  Template Controller page
 *  Author: Gregory Schoeman
*/
require_once __DIR__ .'/../dash-loader.php';
defined('ROOT_PATH') || exit;
lock($pdo);
$companies = new \Dash\Companies($pdo);

$template = "user_users_table.twig";
$thead = $pdo->query("DESCRIBE phpauth_users")->fetchAll(PDO::FETCH_COLUMN);
$tbody = $pdo->query("SELECT * FROM phpauth_users")->fetchAll(PDO::FETCH_ASSOC);
$companyList = $companies->getAll();
$userGroups = $dashAuth->getGroups();


$values = array(
    'page' => array(
        'title'         => "User List",
        'description'   => "Table of all Users",
        'class'         => "cf-tables",
        'pic'           => ""
    ),
    'tbody'             => $tbody,
    'thead'             => $thead,
    'companies'         => $companyList,
    'userGroups'        => $userGroups,
);
echo $twig->render($template, $values);
