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
$runtime_data->parameters->snippet_length = 150;
$runtime_data->parameters->copyright      = 'Molajo is © Copyright Amy Stephen. All rights reserved.';

$runtime_data->parameters->action_heading = 'Get in touch!';
$runtime_data->parameters->action_message = '<p>We would love to hear from you, you cupcake, you.</p>';
$runtime_data->parameters->action_button  = 'Contact Us';

$runtime_data->parameters->contact_heading = 'Want to get in Touch?';
$runtime_data->parameters->contact_message = '<p>I would love to hear from you. You can either cupcake liquorice caramels or you can cake candy canes. <em>It is your choice</em>.</p>';
$runtime_data->parameters->map = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3020.1766407600185!2d-96.68290400000004!3d40.802112000000015!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8796be60e05420c7%3A0xe9ca08c6602dada7!2sSunken+Gardens!5e0!3m2!1sen!2sus!4v1396636781204';
$runtime_data->parameters->map_address = 'Sunken Gardens<br>
    S 27th St<br>
    Lincoln, NE 68502';

$runtime_data->parameters->disqus_name    = 'AmyStephen';

$runtime_data->parameters->orbit_image1   = 'http://placehold.it/1000x400&amp;text=%20Molajito%20';
$runtime_data->parameters->orbit_image2   = 'http://placehold.it/1000x400&amp;text=%20is%20';
$runtime_data->parameters->orbit_image3   = 'http://placehold.it/1000x400&amp;text=%20PHP%20';
$runtime_data->parameters->orbit_image4   = 'http://placehold.it/1000x400&amp;text=%20Framework%20';
$runtime_data->parameters->orbit_image5   = 'http://placehold.it/1000x400&amp;text=%20Independent%20';
$runtime_data->parameters->orbit_image6   = '';
$runtime_data->parameters->orbit_image7   = '';
$runtime_data->parameters->orbit_image8   = '';
$runtime_data->parameters->orbit_image9   = '';

$runtime_data->route                  = new stdClass();
$runtime_data->route->home            = $page_url;
$runtime_data->route->blog            = $page_url . '/index.php?page=blog';
$runtime_data->route->post            = $page_url . '/index.php?page=post';
$runtime_data->route->about           = $page_url . '/index.php?page=about';
$runtime_data->route->contact         = $page_url . '/index.php?page=contact';
$runtime_data->route->parameter_array = $parameter_array;

$runtime_data->resource = new stdClass();

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
