<?php
/**
 * Molajito Abstract Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EscapeInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\RenderInterface;
use stdClass;

/**
 * Molajito Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
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
     * @var    object
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
     * On Before Event
     *
     * @var    string
     * @since  1.0.0
     */
    protected $on_before_event;

    /**
     * On After Event
     *
     * @var    string
     * @since  1.0.0
     */
    protected $on_after_event;

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
     * @var    object
     * @since  1.0.0
     */
    protected $parameters = null;

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
     * @var    object
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
     * Render Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'include_path',
            'model_registry',
            'parameters',
            'plugin_data',
            'query_results',
            'row',
            'rendered_view'
        );

    /**
     * Constructor
     *
     * @param  EscapeInterface $escape_instance
     * @param  RenderInterface $render_instance
     * @param  EventInterface  $event_instance
     * @param  Object          $runtime_data
     *
     * @since  1.0.0
     */
    public function __construct(
        EscapeInterface $escape_instance,
        $render_instance,
        EventInterface $event_instance,
        $runtime_data
    ) {
        $this->render_instance = $render_instance;
        $this->event_instance  = $event_instance;
        $this->escape_instance = $escape_instance;
        $this->runtime_data    = $runtime_data;
    }

    /**
     * Render Output
     *
     * @param   array $data
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderOutput(array $data = array())
    {
        $this->initialise($data);
        $this->scheduleEvent($this->on_before_event, array());
        $this->renderView($data['suffix']);
        $this->scheduleEvent($this->on_after_event, array());

        return $this->rendered_view;
    }

    /**
     * Initialise Rendering Process
     *
     * @param   array $data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function initialise(array $data = array())
    {
        $this->rendered_view   = '';
        $this->on_before_event = $data['on_before_event'];
        $this->on_after_event  = $data['on_after_event'];

        if (isset($data['token']->include_path)) {
            $data['include_path'] = $data['token']->include_path;
            unset($data['token']->include_path);
        }

        $this->setProperties($data);

        return $this;
    }

    /**
     * Render View
     *
     * @param   string $suffix
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function renderView($suffix)
    {
        $this->includeFile($this->include_path . $suffix);

        return $this;
    }

    /**
     * Include rendering file
     *
     * @param   string $include_path
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function includeFile($include_path)
    {
        if (file_exists($include_path)) {
        } else {
            throw new RuntimeException(
                'Molajito Abstract Renderer - rendering file not found: ' . $include_path
            );
        }

        $options = $this->getProperties();

        $options['include_path'] = $include_path;
        $options['runtime_data'] = $this->runtime_data;

        $this->rendered_view .= $this->render_instance->renderOutput($options);

        return $this;
    }

    /**
     * Set Class Properties
     *
     * @param   array $data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setProperties(array $data = array())
    {
        foreach ($this->property_array as $key) {

            if (isset($data[$key])) {
                $this->$key = $data[$key];
            } else {
                $this->$key = null;
            }
        }

        $this->setToken($data);

        return $this;
    }

    /**
     * Set Token
     *
     * @param   array $data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setToken(array $data = array())
    {
        if (is_null($this->parameters)) {
            $this->parameters = new stdClass();
        }

        $this->parameters->token = $data['token'];

        return $this;
    }

    /**
     * Get Theme/View Class Properties for array sent into Render Class
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getProperties()
    {
        $data = array();

        foreach ($this->property_array as $key) {
            $data[$key] = $this->$key;
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
     * @since   1.0.0
     */
    public function scheduleEvent($event_name, array $options = array())
    {
        $event_options = $this->setOptions($options);

        $event_results = $this->event_instance->scheduleEvent($event_name, $event_options);

        foreach ($event_results as $key => $value) {

            if (in_array($key, $this->property_array)) {
                $this->$key = $value;
            }
        }

        if (isset($this->plugin_data->render->extension->path)) {
            $this->include_path = $this->plugin_data->render->extension->path;
        }

        return $event_results;
    }

    /**
     * Set Options for Events
     *  -> also used in the TemplateView to return results from Events to Token
     *
     * @param   array $options
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setOptions(array $options = array())
    {
        $event_options = $this->event_instance->initializeEventOptions();

        foreach ($event_options as $key => $value) {

            if (isset($options[$key])) {
                $event_options[$key] = $options[$key];

            } elseif (isset($this->$key)) {
                $event_options[$key] = $this->$key;
            }
        }

        return $event_options;
    }
}
