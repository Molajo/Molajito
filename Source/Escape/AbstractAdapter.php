<?php
/**
 * Molajito Escape Abstract Adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Escape;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EscapeInterface;

/**
 * Molajito Escape Abstract Adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class AbstractAdapter implements EscapeInterface
{
    /**
     * Fieldhandler Query Output prior to Rendering
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

        $this->data           = $data;

        foreach ($this->data as $row) {
            foreach ($row as $data_key => $data_value) {
                $row->$data_key = $this->escapeDataElement($data_key, $data_value);
            }
        }

        return $this->data;
    }

    /**
     * Fieldhandler Query Output for data element
     *
     * @param   string     $data_key
     * @param   null|mixed $data_value
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    abstract protected function escapeDataElement($data_key, $data_value = null);
}
