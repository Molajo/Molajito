<?php
/**
 * Autoload for Data Class
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
include $molajito_base . '/vendor/autoload.php';

$classmap['Molajito\\Data\\Blog'] = $molajito_base . '/Sample/Source/Data/Blog.php';

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);
