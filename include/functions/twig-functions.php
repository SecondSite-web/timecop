<?php
/**
 * File:          twig-functions.php
 * File Created:  2021/03/29 16:52
 * Modified By:   Gregory Schoeman <gregory@secondsite.xyz>
 * PHP version 8.0
 * -----
 *
 * @category  WebApp
 * @package   NPM
 * @author    Gregory Schoeman <gregory@secondsite.xyz>
 * @copyright 2019-2021 SecondSite
 * @license   https://opensource.org/licenses/MIT  MIT
 * @version   GIT: <1.0.0>
 * @link      https://github.com/SecondSite-web/dash.git
 * @project   dash
 */

use Dash\DashAuth;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Dash\DashNonce;
use Dash\Permissions;

$config = new \PHPAuth\Config($pdo);
$auth   = new \PHPAuth\Auth($pdo, $config);

$dashAuth = new DashAuth($pdo);
$dashNonce = new DashNonce($pdo);
$permissions = new Permissions($pdo);
/*
 * Template Directories
 */
$templatePaths = array(
    ROOT_PATH ."/include/templates",
    ROOT_PATH ."/include/templates/admin",
    ROOT_PATH ."/include/templates/auth",
    ROOT_PATH ."/include/templates/client"
);
$loader = new FilesystemLoader($templatePaths);

/*
 * Environment Loader
 */
$twig = new Environment($loader, array(
    'debug' => true,
    # 'cache' => 'cache',
));
$twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Africa/Johannesburg');
/*
 * Custom Twig function to call a nonce from client side
 * <input type="hidden" name="nonce" value="{{ call_nonce('contact-form') }}" />
 */
$nonce_function = new TwigFunction(
    'call_nonce',
    function ($action_name, $script = false) use ($dashNonce) {
        ob_start();
        $nonceUtil = $dashNonce->nonceInit();
        $nonce = $nonceUtil->create($action_name);
        ob_end_flush();
        return $nonce;
    }
);
$twig->addFunction($nonce_function);
/*
 * Permissions function to verify if a user has permissions
 */
$verify_function = new TwigFunction(
    'verify',
    function ($userId, $permissionTitle) use ($permissions) {
        return $permissions->verify($userId, $permissionTitle);
    }
);
$twig->addFunction($verify_function);

/*
 * Gets a list of groups that have granted permissions
 */
$permission_function = new TwigFunction(
    'getGroups',
    function ($permissionTitle) use ($permissions) {
        return $permissions->getPermissionGroups($permissionTitle);
    }
);
$twig->addFunction($permission_function);

$siteUrl = currentUrl();
$twig_globals = [
    'name' => SITE_NAME,
    'url' => $siteUrl,
    'year' => date('Y'),
    'worker' => $siteUrl.'include/workers/',
    'img' => $siteUrl.'img/',
    'admin' => $siteUrl.'admin/'
];
$twig->addGlobal('site', $twig_globals);
if ($auth->isLogged()) {
    $twig->addGlobal('user', $dashAuth->sessionUser());
}
/*
 * Debug extension for Twig templating
 */
$twig->addExtension(new DebugExtension());

