<?php
/**
 * Entry Point to Molajito Sample
 *
 * @package    Molajito
 * @link       https://github.com/Molajo/Molajito
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$path = __DIR__;
$path = substr($path, 0, strlen($path) - 7);
define('FOLDER_BASE', $path);
include FOLDER_BASE . '/Bootstrap/Route.php';
include FOLDER_BASE . '/Public/Foundation5/index.php';
