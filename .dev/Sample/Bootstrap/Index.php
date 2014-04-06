<?php
/**
 * Bootstrap
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
include MOLAJITO_BASE . '/vendor/autoload.php';

if (! defined('PHP_VERSION_ID')) {
    $version = explode('.', phpversion());
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
include __DIR__ . '/Route.php';
include __DIR__ . '/MolajitoFactoryMethod.php';

$class = 'Molajo\\Factories\\MolajitoFactoryMethod';
$theme_base_folder = MOLAJITO_BASE . '.dev/Sample/Public/Foundation5';
$view_base_folder = MOLAJITO_BASE . '.dev/Sample/Views/Foundation5';
$factory = new $class($theme_base_folder, $view_base_folder);
$molajito = $factory->instantiateClass();

$rendered_page = $molajito->render($theme_base_folder, $data);

/** Pass $rendered_page off to your response class */
echo $rendered_page;
