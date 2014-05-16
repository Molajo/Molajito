<?php
/**
 * Molajito Escape Molajo Fieldhandler Adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Escape;

use CommonApi\Exception\RuntimeException;
use CommonApi\Model\FieldhandlerInterface;
use CommonApi\Render\EscapeInterface;
use Exception;

/**
 * Molajito Fieldhandler Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Molajo extends AbstractAdapter implements EscapeInterface
{
    /**
     * Fieldhandler Instance
     *
     * @var    object  CommonApi\Query\FieldhandlerInterface
     * @since  1.0.0
     */
    protected $fieldhandler;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $model_registry = array();

    /**
     * Data
     *
     * @var    array
     * @since  1.0.0
     */
    protected $data = array();

    /**
     * Constructor
     *
     * @since  1.0.0
     */
    public function __construct(
        FieldhandlerInterface $fieldhandler
    ) {
        $this->fieldhandler = $fieldhandler;
    }

    /**
     * Escape prior to Rendering
     *
     * @param   array       $data
     * @param   null|object $model_registry
     *
     * @return  array
     * @since   1.0
     */
    public function escapeOutput(array $data = array(), array $model_registry = array())
    {
        $this->model_registry = $model_registry;

        $this->data = parent::escapeOutput($data, $model_registry);

        return $this->data;
    }

    /**
     * Fieldhandler Query Output for data element
     *
     * @param   string     $data_key
     * @param   null|mixed $data_value
     *
     * @return  mixed
     * @since   1.0
     */
    protected function escapeDataElement($data_key, $data_value = null)
    {
        $escape_key = $this->setEscapeDataType($data_key, $data_value);

        if ($escape_key === 'object') {
            return null;
        }

        $results = $this->fieldhandler->sanitize($data_key, $data_value, $escape_key);

        return $results->getFieldValue();
    }

    /**
     * Set the Escape Data Type
     *
     * @param   string     $data_key
     * @param   null|mixed $data_value
     *
     * @return  boolean|string
     * @since   1.0
     */
    protected function setEscapeDataType($data_key, $data_value = null)
    {
        $escape_key = $this->setEscapeDataTypeModelRegistry($data_key);

        if ($escape_key === false) {
            $escape_key = $this->setDefaultEscapeDataType($data_value);
        }

        return $escape_key;
    }

    /**
     * Set the Escape Data Type using the Model Registry
     *
     * @param   string $data_key
     *
     * @return  boolean|string
     * @since   1.0
     */
    protected function setEscapeDataTypeModelRegistry($data_key)
    {
        foreach ($this->model_registry as $model_item) {
            if ($model_item['name'] == $data_key) {
                return $model_item['type'];
            }
        }

        return false;
    }

    /**
     * Set the Default Escape Data Type
     *
     * @param   null|mixed $data_value
     *
     * @return  string
     * @since   1.0
     */
    protected function setDefaultEscapeDataType($data_value = null)
    {
        $escape_key = 'string';

        if (is_numeric($data_value)) {
            $escape_key = 'numeric';

        } elseif (is_array($data_value)) {
            $escape_key = 'array';

        } elseif (is_object($data_value)) {
            $escape_key = 'object';
        }

        return $escape_key;
    }
}
