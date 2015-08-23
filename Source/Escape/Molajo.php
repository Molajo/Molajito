<?php
/**
 * Molajito Escape Molajo Fieldhandler Adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Escape;

use CommonApi\Fieldhandler\FieldhandlerInterface;
use CommonApi\Render\EscapeInterface;
use stdClass;

/**
 * Molajito Escape Molajo Fieldhandler Adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class Molajo extends AbstractAdapter implements EscapeInterface
{
    /**
     * Fieldhandler Instance
     *
     * @var    object  CommonApi\Fieldhandler\FieldhandlerInterface
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
     * Customfield Groups
     *
     * @var    array
     * @since  1.0.0
     */
    protected $customfieldgroups = array();

    /**
     * Fields
     *
     * @var    array
     * @since  1.0.0
     */
    protected $fields = array();

    /**
     * Constructor
     *
     * @param   FieldhandlerInterface $fieldhandler
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
     * @param   array      $data
     * @param   null|array $model_registry
     *
     * @return  array
     * @since   1.0.0
     */
    public function escapeOutput(array $data = array(), array $model_registry = array())
    {
        $this->model_registry = $model_registry;
        $this->data           = array();

        $this->setFieldGroup();

        if (count($data) > 0) {
            foreach ($data as $row) {
                $result = $this->processRow($row);
                if ($result === false) {
                } else {
                    $this->data[] = $row;
                }
            }
        }

        return $this->data;
    }

    /**
     * Process Data Elements for Row
     *
     * @param   object $row
     *
     * @return  object
     * @since   1.0.0
     */
    protected function processRow($row)
    {
        $new_row = new stdClass();
        $new_row = $this->processRowStandardFields($row, $new_row);
        $new_row = $this->processRowCustomFields($row, $new_row);

        return $new_row;
    }

    /**
     * Process Elements for Standard Fields
     *
     * @param   object $row
     * @param   object $new_row
     *
     * @return  object $new_row
     * @since   1.0.0
     */
    protected function processRowStandardFields($row, $new_row)
    {
        $data = new stdClass();

        foreach ($row as $data_key => $data_value) {
            if (in_array($data_key, $this->customfieldgroups)) {
            } else {
                $data->$data_key = $data_value;
            }
        }

        return $this->processRowFields($data, $this->fields['fields'], $new_row);
    }

    /**
     * Process Elements for each Custom Field
     *
     * @param   object $row
     * @param   object $new_row
     *
     * @return  object $new_row
     * @since   1.0.0
     */
    protected function processRowCustomFields($row, $new_row)
    {
        if (count($this->customfieldgroups) > 0) {

            foreach ($this->customfieldgroups as $key) {

                if (isset($row->$key) && count($row->$key) > 0) {
                    $new_row = $this->processRowFields($row->$key, $this->fields[$key], $new_row);
                }
            }
        }

        return $new_row;
    }

    /**
     * Escape data elements in the row for a specific field group
     *
     * @param   object $data
     * @param   array  $fields
     * @param   object $new_row
     *
     * @return  object
     * @since   1.0.0
     */
    protected function processRowFields($data, $fields, $new_row)
    {
        foreach ($data as $data_key => $data_value) {
            $new_row->$data_key = $this->escapeDataElement($data_key, $data_value, $fields);
        }

        return $new_row;
    }

    /**
     * Process Field - sanitize each value
     *
     * @param   string     $data_key
     * @param   null|mixed $data_value
     * @param   array      $fields
     *
     * @return  object
     * @since   1.0.0
     */
    protected function escapeDataElement($data_key, $data_value = null, array $fields = array())
    {
        $escape_key = $this->setEscapeDataType($data_key, $data_value, $fields);

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
     * @param   array      $fields
     *
     * @return  boolean|string
     * @since   1.0.0
     */
    protected function setEscapeDataType($data_key, $data_value = null, array $fields = array())
    {
        $escape_key = $this->setEscapeDataTypeModelRegistry($data_key, $fields);

        if ($escape_key === false) {
            $escape_key = $this->setDefaultEscapeDataType($data_value);
        }

        return $escape_key;
    }

    /**
     * Set the Escape Data Type using the Model Registry
     *
     * @param   string $data_key
     * @param   array  $fields
     *
     * @return  boolean|string
     * @since   1.0.0
     */
    protected function setEscapeDataTypeModelRegistry($data_key, array $fields = array())
    {
        if (count($fields) === 0) {
            return false;
        }

        if (isset($fields[$data_key])) {
            return $fields[$data_key];
        }

        return false;
    }

    /**
     * Set the Default Escape Data Type
     *
     * @param   null|mixed $data_value
     *
     * @return  string
     * @since   1.0.0
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

    /**
     * Process Model Registry Field Groups
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setFieldGroup()
    {
        $this->customfieldgroups = array();
        $this->fields            = array();
        $this->fields['fields']  = array();

        if (isset($this->model_registry['fields']) && count($this->model_registry['fields']) > 0) {
            $fields                 = $this->setFieldsForFieldGroup($this->model_registry['fields']);
            $this->fields['fields'] = $fields;
        }

        if (isset($this->model_registry->customfieldgroups) && count($this->model_registry->customfieldgroups) > 0) {
        } else {
            return $this;
        }

        $this->customfieldgroups = $this->model_registry->customfieldgroups;

        if (count($this->customfieldgroups) === 0) {
            return $this;
        }

        foreach ($this->customfieldgroups as $key) {
            if (isset($this->model_registry->$key) && count($this->model_registry->$key) > 0) {
                $fields             = $this->setFieldsForFieldGroup($this->model_registry->$key);
                $this->fields[$key] = $fields;
            }
        }

        return $this;
    }

    /**
     * Load Fields Array for Specific Field Group
     *
     * @param   array $fields
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setFieldsForFieldGroup($fields)
    {
        $temp = array();

        if (count($fields) === 0) {
            return $temp;
        }

        foreach ($this->customfieldgroups as $item) {
            $temp[$item->name] = $item;
        }

        ksort($temp);

        return $temp;
    }
}
