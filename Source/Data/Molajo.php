<?php
/**
 * Molajo Data Adapter for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Data;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\DataInterface;
use stdClass;

/**
 * Molajo Data Adapter for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Molajo extends AbstractAdapter implements DataInterface
{
    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data = null;

    /**
     * Plugin Data
     *
     * @var    object
     * @since  1.0
     */
    protected $plugin_data = null;

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
        $this->initialise($token, $options);

        $this->setModel();

        if ($this->model_type == 'primary') {
            $this->getPrimaryData();

        } elseif ($this->model_type == 'runtime_data') {
            $this->getRuntimeData();

        } elseif ($this->model_type == 'plugin_data') {
            $this->getPluginData();

        } else {
            $this->getDefaultData();
        }

        return $this->setDataResults();
    }

    /**
     * Initialise Class Properties
     *
     * @param   object $token
     * @param   array  $options
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function initialise($token, array $options = array())
    {
        $this->runtime_data   = null;
        $this->plugin_data    = null;
        $this->model_type     = '';
        $this->model_name     = '';
        $this->field_name     = '';
        $this->query_results  = array();
        $this->model_registry = array();
        $this->parameters     = null;

        $this->token = $token;

        if (isset($options['runtime_data'])) {
            $this->runtime_data = $options['runtime_data'];
        } else {
            $this->runtime_data = null;
        }

        if (isset($options['plugin_data'])) {
            $this->plugin_data = $options['plugin_data'];
        } else {
            $this->plugin_data = null;
        }

        $this->parameters = new stdClass();

        return $this;
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

        } elseif (isset($this->runtime_data->render->extension->menuitem->parameters->model_type)) {
            $this->model_type = $this->runtime_data->render->extension->menuitem->parameters->model_type;
        }

        $this->model_type = strtolower($this->model_type);

        if (isset($this->token->attributes['model_name'])) {

            $name = strtolower($this->token->attributes['model_name']);

            if ($this->model_type == 'runtime_data'
                && isset($this->runtime_data->$name)
            ) {
                $this->model_name = $name;

            } elseif (isset($this->plugin_data->$name)) {

                $this->model_type = 'plugin_data';
                $this->model_name = $name;
            }
        }

        if (trim($this->model_name) == ''
            && isset($this->runtime_data->render->extension->parameters->model_name)
        ) {
            $this->model_name = $this->runtime_data->render->extension->parameters->model_name;
        }

        if (trim($this->model_type) == '' && trim($this->model_name) == '') {
            $name = strtolower($this->token->name);
            if (isset($this->plugin_data->$name)) {
                $this->model_type = 'plugin_data';
                $this->model_name = $name;
            }
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

        if (isset($this->plugin_data->$name)) {

            if (isset($this->plugin_data->$name->data)) {
                $this->query_results  = $this->plugin_data->$name->data;
                $this->model_registry = $this->plugin_data->$name->model_registry;
            } else {
                $this->query_results = $this->plugin_data->$name;
            }

            if ($this->field_name == '') {

            } elseif (isset($this->query_results[$this->field_name])) {
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

    /**
     * Get Data from Primary Data Collection
     *
     * @return  $this
     * @since   1.0
     */
    protected function getDefaultData()
    {
        $this->parameters = $this->runtime_data->render->extension->parameters;

        return $this;
    }

    /**
     * Set data for return
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setDataResults()
    {
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

        if (isset($this->token->attributes)
            && count($this->token->attributes) > 0
            && is_array($this->token->attributes)
        ) {
            foreach ($this->token->attributes as $key => $value) {
                $this->parameters->$key = $value;
            }
        }

        $data = new stdClass();

        $data->query_results  = $this->query_results;
        $data->model_registry = $this->model_registry;
        $data->parameters     = $this->parameters;

        return $data;
    }
}
