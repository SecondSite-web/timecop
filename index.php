<?php
require_once __DIR__ .'/dash-loader.php';

// Get the requested URI
if (isset($_SERVER['REQUEST_URI'])) {
    $request = trim($_SERVER['REQUEST_URI']);
} else {
    $request = '/';
}


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

