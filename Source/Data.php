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
     * @var    object  CommonApi\Render\DataInterface
     * @since  1.0.0
     */
    protected $data_adapter = null;

    /**
     * Class Constructor
     *
     * @param  DataInterface $data_adapter
     *
     * @since  1.0
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
        return $this->data_adapter->getData($this->editToken($token), $this->editOptions($options));
    }

    /**
     * Edit Options
     *
     * @param   array $options
     *
     * @return  array
     * @since   1.0
     */
    protected function editOptions(array $options = array())
    {
        if (isset($options['runtime_data'])) {
        } else {
            $options['runtime_data'] = null;
        }

        if (isset($options['plugin_data'])) {
        } else {
            $options['plugin_data'] = null;
        }

        return $options;
    }

    /**
     * Edit Token
     *
     * @param   object $token
     *
     * @return  object
     * @since   1.0
     */
    protected function editToken($token)
    {
        if (isset($token->attributes)
            && is_array($token->attributes)
        ) {
        } else {
            $token->attributes = array();
        }

        return $token;
    }
}
