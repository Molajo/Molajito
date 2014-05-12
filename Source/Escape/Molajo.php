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
    protected $fieldhandler = '';

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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function escape(array $data = array(), array $model_registry = array())
    {
        $this->model_registry = $model_registry;

        $this->data = parent::escape($data, $model_registry);

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
    protected function escapeDataElement($data_key, $data_value = null)
    {
        $escape_key = $this->setEscapeDataType($data_key, $data_value);

        try {
            $results = $this->fieldhandler->escape($data_key, $data_value, $escape_key);

            return $results->getReturnValue();

        } catch (Exception $e) {
            throw new RuntimeException(
                'Molajito Escape Molajo: Fieldhandler class Failed for Key: ' . $data_key
                . ' Fieldhandler: ' . $data_value . ' ' . $e->getMessage()
            );
        }
    }

    /**
     * Set the Escape Data Type
     *
     * @param   string     $data_key
     * @param   null|mixed $data_value
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setEscapeDataType($data_key, $data_value = null)
    {
        $escape_key = null;

        if (count($this->model_registry) > 0) {
            foreach ($this->model_registry as $model_item) {
                if ($model_item['name'] == $data_key) {
                    $escape_key = $model_item['type'];
                }
            }
        }

        if ($escape_key === null) {
            if (is_numeric($data_value)) {
                $escape_key = 'numeric';

            } elseif (is_null($data_value)) {
                $escape_key = 'string';

            } elseif (is_object($data_value)) {
                $escape_key = null;

            } else {
                $escape_key = 'string';
            }
        }

        return $escape_key;
    }
}
