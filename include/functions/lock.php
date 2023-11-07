<?php
/**
 * File:          lock.php
 * File Created:  2021/04/18 11:20
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
require_once __DIR__ .'/dash-loader.php';
defined('ROOT_PATH') || exit;
use Dash\DashAuth;

$dashAuth = new DashAuth($pdo);
$user = $dashAuth->sessionUser();
$siteUrl = currentUrl();
if ($user === false) {
    header("Location: ".$siteUrl."");
    exit;
}
if ($user['isactive'] === '0') {
    header("Location: ".$siteUrl."");
    exit;
}
if ($user['isactive'] === '2') {
    header("Location: ".$siteUrl."");
    exit;
}
