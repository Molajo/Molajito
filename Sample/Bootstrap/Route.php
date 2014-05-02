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
$page_url = strtolower($page_url);
if (substr($page_url, - 1) === '?') {
    $page_url = substr($page_url, 0, strlen($page_url) - 1);
}
if (substr($page_url, - 9) === 'index.php') {
    $page_url = substr($page_url, 0, strlen($page_url) - 9);
}
if (substr($page_url, - 1) === '/') {
    $page_url = substr($page_url, 0, strlen($page_url) - 1);
}

/**
 *  $page
 */
$parameter_page     = '';
$parameter_name     = '';
$parameter_star     = '';
$parameter_category = '';
$parameter_tag      = '';

$parameter_array = explode('&', $query_string);
$page            = 'Home';
foreach ($parameter_array as $pair) {

    $pair_array = explode('=', $pair);

    if (count($pair_array) == 2) {

        if (strtolower($pair_array[0]) == 'page') {
            $parameter_page = trim(strtolower($pair_array[1]));
            $page = ucfirst($parameter_page);

        } elseif (strtolower($pair_array[0]) == 'name') {
            $parameter_name = trim(strtolower($pair_array[1]));

        } elseif (strtolower($pair_array[0]) == 'start') {
            $parameter_start = trim(strtolower($pair_array[1]));

        } elseif (strtolower($pair_array[0]) == 'category') {
            $parameter_category = trim(strtolower($pair_array[1]));

        } elseif (strtolower($pair_array[0]) == 'tag') {
            $parameter_tag = trim(strtolower($pair_array[1]));
        }
    }
}

$current_url = $page_url;

if ($parameter_page == '') {
    $breadcrumb_current_url = $current_url;
} else {
    $current_url .= '/index.php';
    $current_url .= '?page=' . strtolower($parameter_page);
    $breadcrumb_current_url = $current_url;

    if ($parameter_name == '') {

        /** Id supports pagination - and category and tag can both paginate */
        if ($parameter_name == 'category') {
            $current_url .= '&category=' . $parameter_category;

        } elseif ($parameter_name == 'tag') {
            $current_url .= '&tag=' . $parameter_tag;
        }

        if ($parameter_name == 'start') {
            $current_url .= '&start=' . $parameter_start;
        }

    } else {
        $page = 'Post';
        $current_url .= '&name=' . $parameter_name;
        $breadcrumb_current_url = $current_url;
    }
}
