<?php
/**
 * Abstract Extension Adapter for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Extension;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\ExtensionInterface;

/**
 * Abstract Extension Adapter for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
abstract class AbstractAdapter implements ExtensionInterface
{
    /**
     * Get Resource for Rendering
     *
     * @param   array $options
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    abstract public function getResourceExtension(array $options = array());

    /**
     * Get Data required to render token
     *
     * @param   object $token
     *
     * @return  object
     * @throws  \CommonApi\Exception\RuntimeException
     */
    abstract public function getExtension($token);
}
