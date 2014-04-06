<?php
/**
 * Runtime Data
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

/** Define Runtime Data: Routing Info */
$runtime_data                 = new stdClass();
$runtime_data->error_code     = 0;
$runtime_data->redirect_to_id = 0;
$runtime_data->base_path      = MOLAJITO_BASE;
$runtime_data->request        = $_SERVER;

$runtime_data->parameters                 = new stdClass();
$runtime_data->parameters->posts_per_page = 3;
$runtime_data->parameters->copyright      = 'Molajo is © Copyright Amy Stephen. All rights reserved.';

$runtime_data->route                  = new stdClass();
$runtime_data->route->home            = $page_url;
$runtime_data->route->blog            = $page_url . '/index.php?page=blog';
$runtime_data->route->post            = $page_url . '/index.php?page=post';
$runtime_data->route->about           = $page_url . '/index.php?page=about';
$runtime_data->route->contact         = $page_url . '/index.php?page=contact';
$runtime_data->route->parameter_array = $parameter_array;

$runtime_data->resource = new stdClass();

// $this->runtime_data->render = $this->view_instance->getView($token);
$runtime_data->render                                      = new stdClass();
$runtime_data->render->scheme                              = 'Theme';
$runtime_data->render->extension                           = new stdClass();
$runtime_data->render->extension->title                    = '';
$runtime_data->render->extension->include_path             = '';
$runtime_data->render->extension->parameters               = array();
$runtime_data->render->extension->parameters['model_type'] = '';
$runtime_data->render->extension->parameters['model_name'] = '';


/** Will be input to rendering */
$data                 = array();
$data['page_name']    = $page;
$data['runtime_data'] = $runtime_data;
