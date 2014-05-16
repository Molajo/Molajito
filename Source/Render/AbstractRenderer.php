<?php
/**
 * Molajito Abstract Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Render\EscapeInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\RenderInterface;

/**
 * Molajito Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class AbstractRenderer implements RenderInterface
{
    /**
     * Escape Instance
     *
     * @var    object   CommonApi\Render\EscapeInterface
     * @since  1.0.0
     */
    protected $escape_instance = null;

    /**
     * Render Instance
     *
     * @var    object   CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $render_instance = null;

    /**
     * Event Handler
     *
     * @var    object  CommonApi\Render\EventInterface
     * @since  1.0.0
     */
    protected $event_instance = null;

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
     * Parameters
     *
     * @var    array
     * @since  1.0.0
     */
    protected $parameters = array();

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $model_registry = array();

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_results = array();

    /**
     * Object containing a single row for using within View
     *
     * @var    array
     * @since  1.0.0
     */
    protected $row = null;

    /**
     * Path to Include File
     *
     * @var    string
     * @since  1.0.0
     */
    protected $include_path;

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_view = null;

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page = null;

    /**
     * Render Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'runtime_data',
            'plugin_data',
            'parameters',
            'model_registry',
            'query_results',
            'row',
            'include_path',
            'rendered_view',
            'rendered_page'
        );

    /**
     * Constructor
     *
     * @param  EscapeInterface $escape_instance
     * @param  RenderInterface $render_instance
     * @param  EventInterface  $event_instance
     *
     * @since  1.0.0
     */
    public function __construct(
        EscapeInterface $escape_instance,
        RenderInterface $render_instance,
        EventInterface $event_instance
    ) {
        $this->render_instance = $render_instance;
        $this->event_instance  = $event_instance;
        $this->escape_instance = $escape_instance;
    }

    /**
     * Render Page View
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     */
    public function renderOutput($include_path, array $data = array())
    {
        return $this->performRendering($include_path, $data);
    }

    /**
     * Set Theme/View Class Properties
     *
     * @param   array $data
     * @param   array $properties
     *
     * @return  $this
     * @since   1.0
     */
    protected function setProperties(array $data = array(), array $properties = array())
    {
        foreach ($properties as $key) {
            if (isset($data[ $key ])) {
                $this->$key = $data[ $key ];
            } else {
                $this->$key = null;
            }
        }

        return $this;
    }

    /**
     * Get Theme/View Class Properties for array sent into Render Class
     *
     * @return  array
     * @since   1.0
     */
    protected function getProperties()
    {
        $data = array();

        foreach ($this->property_array as $key) {
            $data[ $key ] = $this->$key;
        }

        return $data;
    }

    /**
     * Send Theme/View data into Event and retrieve data from Event for Theme/View
     *
     * @param   string $event_name
     * @param   array  $options
     *
     * @return  array
     * @since   1.0
     */
    public function scheduleEvent($event_name, array $options = array())
    {
        $event_options = $this->setEventOptions($options);

        $event_results = $this->event_instance->scheduleEvent($event_name, $event_options);

        foreach ($event_results as $key => $value) {

            if (in_array($key, $this->property_array)) {
                $this->$key = $value;
            }
        }

        return $event_results;
    }

    /**
     * Set Event Options
     *
     * @param   array $options
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setEventOptions(array $options = array())
    {
        $event_options = $this->event_instance->initializeEventOptions();

        foreach ($event_options as $key => $value) {

            if (isset($options[ $key ])) {
                $event_options[ $key ] = $options[ $key ];

            } elseif (isset($this->$key)) {
                $event_options[ $key ] = $this->$key;
            }
        }

        return $event_options;
    }

    /**
     * Render Output
     *
     * @param   string $file_path
     * @param   array  $options
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function performRendering($file_path, array $options = array())
    {
        return $this->render_instance->renderOutput($file_path, $options);
    }
}
