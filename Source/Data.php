<?php
/**
 * Proxy Class for Molajito Data Adapters
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito;

use CommonApi\Render\DataInterface;
use CommonApi\Exception\RuntimeException;
use Exception;

/**
 * Proxy Class for Molajito Data Adapters
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Data implements DataInterface
{
    /**
     * Data Adapter
     *
     * @var     object  CommonApi\Render\DataInterface
     * @since  1.0
     */
    protected $data_adapter = null;

    /**
     * Class Constructor
     *
     * @param   DataInterface $data_adapter
     *
     * @since   1.0
     */
    public function __construct(
        DataInterface $data_adapter
    ) {
        $this->data_adapter = $data_adapter;
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
        try {
            return $this->data_adapter->getData($token, $options);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito Data getData Method Failed: ' . $e->getMessage());
        }
    }
}
