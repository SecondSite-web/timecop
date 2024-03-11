<?php
/**
 * File:          core-functions.php
 * File Created:  2021/04/05 19:45
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

/**
 * Hack function - not in use
 * Returns the current url
 * @return string
 */
function currentUrl(): string
{
    ob_start();
    if (!isset($_SERVER['HTTP_HOST'])) {
        $_SERVER = array('HTTPS' => 'off', 'HTTP_HOST' => '127.0.0.1', 'REQUEST_URI' => '/');
    }
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' .
        $_SERVER['HTTP_HOST'].'/';
    ob_end_flush();
    return $link;
}

/**
 * Returns a string of random numbers
 * @param $length
 * @return string
 * @throws Exception
 */
function randomString($length): string
{
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $number = random_int(0, 36);
        $character = base_convert($number, 10, 36);
        $random_string .= $character;
    }

    return $random_string;
}

/**
 * Returns all the values in an array that match the key used in filter
 * @param $values
 * @param $filter
 * @return array
 */
function arrayColumn($values, $filter): array
{
    $output = [];
    foreach ($values as $item) {
        $filtered = array_filter($item, static function ($key) use ($filter) {
            return $key === $filter;
        }, ARRAY_FILTER_USE_KEY);
        if ($filtered[$filter]) {
            $output[] = $filtered[$filter];
        }
    }
    return $output;
}


/**
 * Takes a POST array and returns a meta_key, meta_value keypair array
 * @param $values
 * @return array
 */
function postToMeta($values): array
{
    $options = [];
    foreach ($values as $key => $value) {
        $bar = array(
            'meta_key' => $key,
            'meta_value' => $value
        );
        $options[] = $bar;
    }
    return $options;
}

/**
 * Redirects a user to the website root if they are not logged in
 * @param $pdo
 */
function lock($pdo)
{
    $dashAuth = new DashAuth($pdo);
    $user = $dashAuth->sessionUser();
    $siteUrl = currentUrl();
    $timerUrl = $siteUrl."admin/dash/";
    if ($user === false) {
        header("Location: ".$siteUrl."");
        exit;
    }
    if ($user['isactive'] === 0) {
        header("Location: ".$siteUrl."");
        exit;
    }
    if($user['user_group'] !== "root") {
        header("Location: ".$timerUrl."");
        exit;
    }
}

function lock2($pdo)
{
    $dashAuth = new DashAuth($pdo);
    $user = $dashAuth->sessionUser();
    $siteUrl = currentUrl();
    if ($user === false) {
        header("Location: ".$siteUrl."");
        exit;
    }
}

function array_to_json($array): bool|string
{
    return json_encode($array);
}

function json_to_array($json)
{
    return json_decode($json, true, 512);
}

function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

/**
 * Rounds up to the nearest 5 unless ends in 0
 * @param $number
 * @return float|int
 */
function ceilFive($number): float|int
{
    $div = floor($number / 5);
    $mod = $number % 5;

    if ($mod > 0) {
        $add = 5;
    } else {
        $add = 0;
    }

    return $div * 5 + $add;
}

/**
 * Takes an array and concatenates the values into a string
 * @param $array
 * @return string
 */
function concatArray($array): string
{
    $string = '';
    foreach ($array as $key => $value) {
        $string = $string.$value;
    }
    return $string;
}

/**
 * Takes an array and concatenates it to a string and generates a hash function
 * @param $array
 * @return string
 */
function md5Array($array): string
{
    $string = concatArray($array);
    return md5($string);
}