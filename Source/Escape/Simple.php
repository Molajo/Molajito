<?php
/**
 * Molajito Escape Class Simple Adapter
 *
 * Only useful for demo purposes - handles data as numeric, null, or string
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Escape;

use CommonApi\Render\EscapeInterface;

/**
 * Molajito Escape Class Simple Adapter
 *
 * Only useful for demo purposes - handles data as numeric, null, or string
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Simple extends AbstractAdapter implements EscapeInterface
{
    /**
     * Data
     *
     * @var    array
     * @since  1.0.0
     */
    protected $data = array();

    /**
     * Data
     *
     * @var    array
     * @since  1.0.0
     */
    protected $white_list = '<b><br><em><h1><h2><h3><h4><h5><h6><hr><i><img><li><ol><p><ul><strong>';

    /**
     * Constructor
     *
     * @since  1.0.0
     */
    public function __construct(
        $white_list = NULL
    ) {
        if ($white_list === NULL) {
        } else {
            $this->white_list = $white_list;
        }
    }

    /**
     * Simple Query Output prior to Rendering
     *
     * @param   array $data
     * @param   array $model_registry
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function escape(array $data = array(), array $model_registry = array())
    {
        $this->data = parent::escape($data, $model_registry);

        return $this->data;
    }

    /**
     * Escape Data Element
     *
     * @param   string     $data_key
     * @param   null|mixed $data_value
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function escapeDataElement($data_key, $data_value = NULL)
    {
        if (is_numeric($data_value)) {
            return $data_value;

        } elseif (is_null($data_value)) {
            return $data_value;

        } elseif (is_array($data_value)) {
            return $data_value;

        } elseif (is_string($data_value)) {
            return strip_tags($data_value, $this->white_list);
        }

        return NULL;
    }
}
