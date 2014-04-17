<?php
/**
 * Bootstrap
 *
 * @package   Molajo
 * @copyright 2014 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
include $molajito_base . '/vendor/autoload.php';

/** Simulates Route process **/
include __DIR__ . '/Route.php';

/** Configuration Data */
include __DIR__ . '/RuntimeData.php';

/** Dependency Injection */
$options                         = array();
$options['molajito_base_folder'] = $molajito_base;
$options['theme_base_folder']    = $molajito_base . '/Sample/Public/Foundation5';
$options['view_base_folder']     = $molajito_base . '/Sample/Views/Foundation5';
$options['escape_class']         = 'simple';
$options['data_class']           = 'blog';
$options['data_options']         = array('data_folder' => $molajito_base . '/Sample/Data');

$class    = 'Molajito\\FactoryMethod';
$factory  = new $class($options);
$molajito = $factory->instantiateClass();

/** Render using the specified Theme */
$rendered_page = $molajito->render(
    $options['theme_base_folder'],
    array('runtime_data' => $runtime_data)
);

/** Hand $rendered_page off to your response class */
echo $rendered_page;
