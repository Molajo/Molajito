<?php
/**
 * Bootstrap
 *
 * @package   Molajo
 * @copyright 2014 Amy Stephen. All rights reserved.
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */
include $molajito_base . '/vendor/autoload.php';

/** Simulates Route process */
include __DIR__ . '/Route.php';

/** Configuration Data */
include __DIR__ . '/RuntimeData.php';

/** Language Strings */
/** Dependency Injection */
$options                         = array();
$options['language_strings']     = array(
    'Categories'         => 'Categories',
    'Comments'           => 'Comments',
    'Contact Me'         => 'Contact Me',
    'Email'              => 'Email',
    'Map'                => 'Map',
    'Next'               => 'Next',
    'on'                 => 'on',
    'Previous'           => 'Previous',
    'Read More'          => 'Read More',
    'Submit'             => 'Submit',
    'Tags'               => 'Tags',
    'Things that I love' => 'Things that I love',
    'Your Name'          => 'Your Name',
    'Your Email'         => 'Your Email',
    'Your Message'       => 'Your Message',
    'Written by'         => 'Written by',
    'Pagination Template is Disabled' => 'Pagination Template is Disabled',
    'To enable, include the Molajo/Pagination class in Composer.json and update.'
    => 'To enable, include the Molajo/Pagination class in Composer.json and update.'
);
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
