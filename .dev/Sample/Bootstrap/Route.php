<?php
/**
 * Route
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

/**
 *  $page_url
 */
$page_url = 'http';

if (empty($_SERVER['HTTPS'])) {
} else {

    $https = strtolower($_SERVER['HTTPS']);

    if ($https == 'on' || $https == '1') {
        if ($_SERVER["HTTPS"] == "on") {
            $page_url .= 's';
        }
    }
}

$page_url .= '://';

if ($_SERVER["SERVER_PORT"] == '80') {
    $page_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
} else {
    $page_url .= $_SERVER["SERVER_NAME"] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
}

$query_string = $_SERVER['QUERY_STRING'];
if (trim($query_string) == '') {
} else {
    $page_url = substr(
        $page_url,
        0,
        (strlen($page_url) - strlen($query_string))
    );
}
if (strtolower(substr($page_url, - 1)) == '?') {
    $page_url = substr($page_url, 0, strlen($page_url) - 1);
}
if (strtolower(substr($page_url, - 9)) == 'index.php') {
    $page_url = substr($page_url, 0, strlen($page_url) - 9);
}
if (strtolower(substr($page_url, - 1)) == '/') {
    $page_url = substr($page_url, 0, strlen($page_url) - 1);
}

/**
 *  $page
 */
$parameter_array = explode('&', $query_string);
$page            = 'Home';
foreach ($parameter_array as $pair) {

    $pair_array = explode('=', $pair);

    if (count($pair_array) == 2 && strtolower($pair_array[0]) == 'page') {
        $page = ucfirst(strtolower($pair_array[1]));
        break;
    }
}
