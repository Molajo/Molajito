<?php
/**
 * Bootstrap for Testing
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$base     = substr(__DIR__, 0, strlen(__DIR__) - 5);
include $base . '/vendor/autoload.php';
include_once __DIR__ . '/CreateClassMap.php';

$classmap = array();
$results  = createClassMap($base . '/Source/Data/', 'Molajito\\Data\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Escape/', 'Molajito\\Escape\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Event/', 'Molajito\\Event\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Translate/', 'Molajito\\Translate\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/View/', 'Molajito\\View\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/', 'Molajito\\');
$classmap = array_merge($classmap, $results);

ksort($classmap);

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);
