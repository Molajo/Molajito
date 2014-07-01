<?php
/**
 * Bootstrap for Testing
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$base = substr(__DIR__, 0, strlen(__DIR__) - 5);
if (function_exists('CreateClassMap')) {
} else {
    include_once __DIR__ . '/CreateClassMap.php';
}
include_once $base . '/vendor/autoload.php';

$classmap                                               = array();
$classmap['Molajito\\Data\\Molajo']                     = $base . '/Source/Data/Molajo.php';
$classmap['Molajito\\Data\\AbstractAdapter']            = $base . '/Source/Data/AbstractAdapter.php';
$classmap['Molajito\\Escape\\Molajo']                   = $base . '/Source/Escape/Molajo.php';
$classmap['Molajito\\Escape\\Simple']                   = $base . '/Source/Escape/Simple.php';
$classmap['Molajito\\Escape\\AbstractAdapter']          = $base . '/Source/Escape/AbstractAdapter.php';
$classmap['Molajito\\Event\\Molajo']                    = $base . '/Source/Event/Molajo.php';
$classmap['Molajito\\Event\\Dummy']                     = $base . '/Source/Event/Dummy.php';
$classmap['Molajito\\Event\\AbstractAdapter']           = $base . '/Source/Event/AbstractAdapter.php';
$classmap['Molajito\\Translate\\MolajoLanguageAdapter'] = $base . '/Source/Translate/MolajoLanguageAdapter.php';
$classmap['Molajito\\Translate\\StringArrayAdapter']    = $base . '/Source/Translate/StringArrayAdapter.php';
$classmap['Molajito\\Translate\\AbstractAdapter']       = $base . '/Source/Translate/AbstractAdapter.php';
$classmap['Molajito\\View\\Molajo']                     = $base . '/Source/View/Molajo.php';
$classmap['Molajito\\View\\Filesystem']                 = $base . '/Source/View/Filesystem.php';
$classmap['Molajito\\View\\AbstractAdapter']            = $base . '/Source/View/AbstractAdapter.php';
$results                                                = createClassMap($base . '/Source/', 'Molajito\\');
$classmap                                               = array_merge($classmap, $results);

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);
