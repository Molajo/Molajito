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

use CommonApi\Exception\RuntimeException;
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
class Simple implements EscapeInterface
{
    /**
     * Data
     *
     * @var    array
     * @since  1.0
     */
    protected $data = array();

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
        if (count($data) == 0) {
            return $data;
        }

        $this->data = $data;

        foreach ($this->data as $row) {
            foreach ($row as $data_key => $data_value) {
                $row->$data_key = $this->escapeDataElement($data_value);
            }
        }

        return $this->data;
    }

    /**
     * Simple Query Output for data element
     *
     * @param   null|mixed $data_value
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function escapeDataElement($data_value = null)
    {
        if (is_numeric($data_value)) {
            return $data_value;

        } elseif (is_null($data_value)) {
            return $data_value;

        } elseif (is_array($data_value)) {
            return $data_value;
        }

        return htmlentities($data_value);
    }
}
