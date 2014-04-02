<?php
/**
 * Molajito Data - proxy to backend adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\DataInterface;

/**
 * Molajito Data - proxy to backend adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Data implements DataInterface
{
    /**
     * Adapter
     *
     * @var    object  CommonApi\Render\DataInterface
     * @since  1.0
     */
    protected $adapter = null;

    /**
     * Constructor
     *
     * @param   DataInterface $adapter
     *
     * @since   1.0
     */
    public function __construct(DataInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get Data for Rendering
     *
     * @param   object $token
     * @param   array  $options
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getData($token, array $options = array())
    {
        return $this->adapter->getData($token, $options);
    }
}
