<?php
/**
 * Bootstrap
 *
 * @package Molajo
 * @copyright 2014 Amy Stephen. All rights reserved.
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
include $molajito_base . '/vendor/autoload.php';

if (! defined('PHP_VERSION_ID')) {
    $version = explode('.', phpversion());
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
/** Simulates the Route process **/
include __DIR__ . '/Route.php';

/** Adds data to the $this->runtime_data object */
include __DIR__ . '/RuntimeData.php';

/** Defines the location of views and data files */
include __DIR__ . '/Input.php';

/** Dependency Injection for Molajito */
include __DIR__ . '/MolajitoFactoryMethod.php';

$class = 'Molajo\\Factories\\MolajitoFactoryMethod';
$factory = new $class(
    $molajito_base,
    $theme_base_folder,
    $view_base_folder,
    $posts_base_folder,
    $author_base_folder,
    $post_model_registry,
    $author_model_registry
);

/** Initiates Molajito */
$molajito = $factory->instantiateClass();

/** Pass in the Theme and Data ($this->runtime_data) */
$rendered_page = $molajito->render($theme_base_folder, $data);

/** Pass $rendered_page off to your response class */
echo $rendered_page;
