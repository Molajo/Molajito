<?php
/**
 * Molajo Data Adapter for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Data;

use CommonApi\Render\DataInterface;
use CommonApi\Render\PaginationInterface;
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
     * @since  1.0.0
     */
    protected $runtime_data = null;

    /**
     * Plugin Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $plugin_data = null;

    /**
     * Token
     *
     * @var    object
     * @since  1.0.0
     */
    protected $token = null;

    /**
     * Model Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_type = '';

    /**
     * Model Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_name = '';

    /**
     * Field Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $field_name = '';

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_results = array();

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0.0
     */
    protected $model_registry = null;

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0.0
     */
    protected $parameters = null;

    /**
     * Get Data for Rendering
     *
     * @param   object $token
     * @param   array  $options
     *
     * @return  stdClass
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getData($token, array $options = array())
    {
        $this->initialise($token, $options);

        $this->setModel();

        if ($this->model_type == 'primary') {
            $this->getPrimaryData();

        } elseif ($this->model_type === 'runtime_data') {
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
        $this->model_type     = 'default';
        $this->model_name     = 'default';
        $this->field_name     = '';
        $this->query_results  = array();
        $this->model_registry = array();
        $this->token          = $token;
        $this->runtime_data   = $options['runtime_data'];
        $this->plugin_data    = $options['plugin_data'];
        $this->parameters     = new stdClass();

        return $this;
    }

    /**
     * Initialise Class Properties
     *
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModel()
    {
        $model = new MolajoModel(
            $this->token,
            $this->runtime_data,
            $this->plugin_data
        );

        $results = $model->setModel();

        $this->model_type = $results['model_type'];
        $this->model_name = $results['model_name'];
        $this->field_name = $results['field_name'];

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

        if (isset($this->runtime_data->render->extension->parameters)) {
            $hold_parameters = $this->runtime_data->render->extension->parameters;

            if (is_array($hold_parameters) && count($hold_parameters) > 0) {
                $this->getPrimaryDataExtensionParameters($hold_parameters);
            }
        }

        return $this;
    }

    /**
     * Get Data from Primary Data Collection
     *
     * @param   array $hold_parameters
     *
     * @return  $this
     * @since   1.0
     */
    protected function getPrimaryDataExtensionParameters($hold_parameters)
    {
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

        $this->setParameters();

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
            $this->getPluginDataQueryResults();
        }

        $this->setParameters();

        return $this;
    }

    /**
     * Get Data from Plugin Data Collection
     *
     * @return  $this
     * @since   1.0
     */
    protected function getPluginDataQueryResults()
    {
        $name = $this->model_name;

        if (isset($this->plugin_data->$name->data)) {
            $this->query_results  = $this->plugin_data->$name->data;
            $this->model_registry = $this->plugin_data->$name->model_registry;
        } else {
            $this->query_results = $this->plugin_data->$name;
        }

        $this->getPluginDataQueryResultsField();

        return $this;
    }

    /**
     * Reduce Query object to specific field requested
     *
     * @return  $this
     * @since   1.0
     */
    protected function getPluginDataQueryResultsField()
    {
        if (isset($this->query_results[ $this->field_name ])) {
            $x                     = $this->query_results[ $this->field_name ];
            $this->query_results   = array();
            $this->query_results[] = $x;
        }

        return $this;
    }

    /**
     * Set Parameters from Query Results or Extension Parameters
     *
     * @return  $this
     * @since   1.0
     */
    protected function setParameters()
    {
// todo: is this possible?
//        if (isset($this->query_results->parameters)) {
//            $this->parameters = $this->query_results->parameters;
//            unset($this->query_results->parameters);

//        } else {
        $this->parameters = $this->runtime_data->render->extension->parameters;
//        }

        return $this;
    }

    /**
     * Get Default Data - just parameters, no query results
     *
     * @return  $this
     * @since   1.0
     */
    protected function getDefaultData()
    {
        if (isset($this->runtime_data->render->extension->parameters)) {
            $this->parameters = $this->runtime_data->render->extension->parameters;
        } else {
            $this->parameters = new stdClass();
        }

        $this->model_registry = new stdClass();

        return $this;
    }

    /**
     * Set data for return
     *
     * @return  stdClass
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setDataResults()
    {
        $this->setDataResultsQueryResults();
        $this->setDataResultsParameters();
        return $this->setDataResultsDataObject();
    }

    /**
     * Set Query Results
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setDataResultsQueryResults()
    {
        if (is_array($this->query_results)) {
        } else {
            $this->query_results = array($this->query_results);
        }

        return $this;
    }

    /**
     * Set Parameters
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setDataResultsParameters()
    {
        if (is_object($this->parameters)) {
        } else {
            $this->parameters = new stdClass();
        }

        $this->parameters->token      = $this->token;
        $this->parameters->model_type = $this->model_type;
        $this->parameters->model_name = $this->model_name;
        $this->parameters->field_name = $this->field_name;

        $this->parameters = $this->setParametersFromToken($this->token, $this->parameters);

        return $this;
    }

    /**
     * Set Data Results Data Object
     *
     * @return  stdClass
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setDataResultsDataObject()
    {
        $data = new stdClass();

        $data->query_results  = $this->query_results;
        $data->model_registry = $this->model_registry;
        $data->parameters     = $this->parameters;

        return $data;
    }
}
