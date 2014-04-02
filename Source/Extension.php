<?php
/**
 * Molajito Extension - proxy to backend adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\ExtensionInterface;

/**
 * Molajito Extension - proxy to backend adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Extension implements ExtensionInterface
{
    /**
     * Adapter
     *
     * @var    object  CommonApi\Render\ExtensionInterface
     * @since  1.0
     */
    protected $adapter = null;

    /**
     * Constructor
     *
     * @param   ExtensionInterface $adapter
     *
     * @since   1.0
     */
    public function __construct(ExtensionInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get Resource for Rendering
     *
     * @param   array $options
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getResourceExtension(array $options = array())
    {
        return $this->adapter->getResourceExtension($options);
    }

    /**
     * Get Data required to render token
     *
     * @param   object $token
     *
     * @return  object
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getExtension($token)
    {
        return $this->adapter->getExtension($token);
    }
}
