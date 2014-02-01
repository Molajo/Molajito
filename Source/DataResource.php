<?php
/**
 * Pagination Data Resource
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\DataResourceInterface;
use stdClass;

/**
 * Pagination Data Resource
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class DataResource implements DataResourceInterface
{
    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data;

    /**
     * Token
     *
     * @var    object
     * @since  1.0
     */
    protected $token = null;

    /**
     * Model Type
     *
     * @var    string
     * @since  1.0
     */
    protected $model_type = '';

    /**
     * Model Name
     *
     * @var    string
     * @since  1.0
     */
    protected $model_name = '';

    /**
     * Field Name
     *
     * @var    string
     * @since  1.0
     */
    protected $field_name = '';

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0
     */
    protected $query_results = array();

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = array();

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0
     */
    protected $parameters = null;

    /**
     * Constructor
     *
     * @param  object $resource
     * @param  object $runtime_data
     * @param  string $token
     *
     * @since  1.0
     */
    public function __construct(
        $runtime_data,
        $token
    ) {
        $this->runtime_data = $runtime_data;
        $this->token        = $token;
        $this->parameters   = new stdClass();
    }

    /**
     * Get Data for Rendering
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getData()
    {
        $this->setModel();

        if ($this->model_name == 'primary') {
            $this->getPrimaryData();

        } elseif ($this->model_type == 'runtime_data') {
            $this->getRuntimeData();

        } elseif ($this->model_type == 'plugin_data') {
            $this->getPluginData();
        }

        if (is_array($this->query_results)) {
        } else {
            $this->query_results = array($this->query_results);
        }

        if (is_object($this->parameters)) {
        } else {
            $this->parameters = new stdClass();
        }

        $this->parameters->token      = $this->token;
        $this->parameters->model_type = $this->model_type;
        $this->parameters->model_name = $this->model_name;
        $this->parameters->field_name = $this->field_name;

        $data = new stdClass();

        $data->query_results  = $this->query_results;
        $data->model_registry = $this->model_registry;
        $data->parameters     = $this->parameters;

        return $data;
    }

    /**
     * Set Model Type, Model Name and Field Name values used for data retrieval
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModel()
    {
        if (isset($this->token->attributes['model_type'])) {
            $this->model_type = $this->token->attributes['model_type'];

        } elseif (isset($this->runtime_data->render->extension->parameters->model_type)) {
            $this->model_type = $this->runtime_data->render->extension->parameters->model_type;
        }

        $this->model_type = strtolower($this->model_type);

        if (isset($this->token->attributes['model_name'])) {

            $name = strtolower($this->token->attributes['model_name']);

            if ($this->model_type == 'runtime_data'
                && isset($this->runtime_data->$name)
            ) {
                $this->model_name = $name;

            } elseif ($this->model_type == 'plugin_data'
                && isset($this->runtime_data->plugin_data->$name)
            ) {
                $this->model_name = $name;
            }
        }

        if ($this->model_name == ''
            && isset($this->runtime_data->render->extension->parameters->model_name)
        ) {
            $this->model_name = $this->runtime_data->render->extension->parameters->model_name;
        }

        $this->model_name = strtolower($this->model_name);

        if (isset($this->token->attributes['field_name'])) {
            $this->field_name = $this->token->attributes['field_name'];
        }

        $this->field_name = strtolower($this->field_name);

        return $this;
    }

    /**
     * Get Data from Primary Data Collection
     *
     * @return  $this
     * @since   1.0
     */
    protected function getPrimaryData()
    {
        $this->query_results  = $this->runtime_data->resource->data;
        $this->model_registry = $this->runtime_data->resource->model_registry;
        $this->parameters     = $this->runtime_data->resource->parameters;
        $hold_parameters      = $this->runtime_data->render->extension->parameters;

        if (is_array($hold_parameters) && count($hold_parameters) > 0) {

            foreach ($hold_parameters as $key => $value) {
                if (isset($this->parameters->$key)) {
                    if ($this->parameters->$key === null) {
                        $this->parameters->$key = $value;
                    }
                } else {
                    $this->parameters->$key = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Get Data from Runtime Data Collection
     *
     * @return  $this
     * @since   1.0
     */
    protected function getRuntimeData()
    {
        $name = $this->model_name;

        if (isset($this->runtime_data->$name)) {
            $this->query_results = $this->runtime_data->$name;
        }

        $this->model_registry = new stdClass();

        if (isset($this->query_results->parameters)) {
            $this->parameters = $this->query_results->parameters;
            unset($this->query_results->parameters);

        } else {
            $this->parameters = $this->runtime_data->render->extension->parameters;
        }

        return $this;
    }

    /**
     * Get Data from Plugin Data Collection
     *
     * @return  $this
     * @since   1.0
     */
    protected function getPluginData()
    {
        $name = $this->model_name;

        if (isset($this->runtime_data->$name)) {
            $this->query_results = $this->runtime_data->$name;
        }

        if (isset($this->runtime_data->plugin_data->$name)) {

            if (isset($this->runtime_data->plugin_data->$name->data)) {
                $this->query_results  = $this->runtime_data->plugin_data->$name->data;
                $this->model_registry = $this->runtime_data->plugin_data->$name->model_registry;
            } else {
                $this->query_results = $this->runtime_data->plugin_data->$name;
            }

            if ($this->field_name == '') {
            } elseif (isset($query_results[$this->field_name])) {
                $x                     = $this->query_results[$this->field_name];
                $this->query_results   = array();
                $this->query_results[] = $x;
            }
        }

        if (isset($this->query_results->parameters)) {
            $this->parameters = $this->query_results->parameters;
            unset($this->query_results->parameters);

        } else {
            $this->parameters = $this->runtime_data->render->extension->parameters;
        }

        return $this;
    }
}
