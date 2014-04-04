<?php
/**
 * Filesystem Data Adapter for Molajito
 *
 * @package    Filesystem
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Data;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\DataInterface;
use stdClass;

/**
 * Filesystem Data Adapter for Molajito
 *
 * @package    Filesystem
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Filesystem extends AbstractAdapter implements DataInterface
{
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

        $this->getFolderList();

        $this->getFile();

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
            $this->model_type = 'Post';
                $this->model_name = '1';


        return $this;
    }

    /**
     * Get Data from Primary Data Collection
     *
     * @return  $this
     * @since   1.0
     */
    protected function getFolderList()
    {

        return $this;
    }

    /**
     * Get Data from Runtime Data Collection
     *
     * @return  $this
     * @since   1.0
     */
    protected function getFile()
    {
        $this->query_results = array();

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
