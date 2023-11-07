<?php

use Dash\DashSetup;

require_once ROOT_PATH . 'vendor/autoload.php'; // Composer Autoloader
require_once ROOT_PATH . 'include/functions/connect.php'; // Sql Connection
require_once ROOT_PATH . 'include/functions/core-functions.php'; // Extra Core Functions
require_once ROOT_PATH . 'include/classes/classes.php'; // Dash PHP Classes
if (! defined('SMTP_EMAIL')) {
    $dashSetup = new dashSetup($pdo);
}
require_once ROOT_PATH.'include/functions/twig-functions.php'; // Load Dash Functions
