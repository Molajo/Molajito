<?php
/**
 * Entry Point to Molajito Sample
 *
 * @package    Molajito
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$path = __DIR__;
$molajito_base = substr($path, 0, strlen($path) - 14);
include $molajito_base . '/Sample/Bootstrap/Index.php';
