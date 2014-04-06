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

/** Define Runtime Data: Routing Info */
$runtime_data                 = new stdClass();
$runtime_data->error_code     = 0;
$runtime_data->redirect_to_id = 0;
$runtime_data->base_path      = MOLAJITO_BASE;
$runtime_data->request        = $_SERVER;

$runtime_data->route                  = new stdClass();
$runtime_data->route->home            = $page_url;
$runtime_data->route->blog            = $page_url . '/index.php?page=blog';
$runtime_data->route->post            = $page_url . '/index.php?page=post';
$runtime_data->route->about           = $page_url . '/index.php?page=about';
$runtime_data->route->contact         = $page_url . '/index.php?page=contact';
$runtime_data->route->parameter_array = $parameter_array;

$runtime_data->resource = new stdClass();

// $this->runtime_data->render = $this->view_instance->getView($token);
$runtime_data->render                          = new stdClass();
$runtime_data->render->scheme                  = 'Theme';
$runtime_data->render->extension               = new stdClass();
$runtime_data->render->extension->title        = '';
$runtime_data->render->extension->include_path = '';
$runtime_data->render->extension->parameters   = array();
$runtime_data->render->extension->parameters['model_type'] = '';
$runtime_data->render->extension->parameters['model_name'] = '';


/** Will be input to rendering */
$data                 = array();
$data['page_name']    = $page;
$data['runtime_data'] = $runtime_data;
