<?php
/**
 * Molajito Abstract Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EscapeInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\RenderInterface;
use Exception;

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
    protected $escape_instance = NULL;

    /**
     * Render Instance
     *
     * @var    object   CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $render_instance = NULL;

    /**
     * Event Handler
     *
     * @var    object  CommonApi\Render\EventInterface
     * @since  1.0.0
     */
    protected $event_instance = NULL;

    /**
     * Path to Include File
     *
     * @var    string
     * @since  1.0.0
     */
    protected $include_path;

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $runtime_data = NULL;

    /**
     * Plugin Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $plugin_data = NULL;

    /**
     * Parameters
     *
     * @var    array
     * @since  1.0.0
     */
    protected $parameters = NULL;

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0.0
     */
    protected $model_registry = NULL;

    /**
     * Allowed Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array = array();

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_results = array();

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_view = NULL;

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page = NULL;

    /**
     * Object containing a single row for using within View
     *
     * @var    array
     * @since  1.0.0
     */
    protected $row = NULL;

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
        $this->render_instance   = $render_instance;
        $this->event_instance    = $event_instance;
        $this->escape_instance   = $escape_instance;
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
            if (isset($data[$key])) {
                $this->$key = $data[$key];
            } else {
                $this->$key = NULL;
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
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function scheduleEvent($event_name, array $options = array())
    {
        $event_options = $this->event_instance->initializeEventOptions();

        foreach ($event_options as $key) {

            if (isset($options[$key])) {
                $event_options[$key] = $options[$key];

            } elseif (isset($this->$key)) {
                $event_options[$key] = $this->$key;
            }
        }

        try {
            $event_results = $this->event_adapter->scheduleEvent($event_name, $event_options);

            if (is_array($event_results) && count($event_results) > 0) {
                foreach ($event_results as $key => $value) {
                    if (isset($this->$key)) {
                        $this->$key = $value;
                    }
                }
            }

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito Driver scheduleEvent Method Failed: ' . $e->getMessage()
            );
        }
    }

    /**
     * Render Page View
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function render($include_path, array $data = array())
    {
        return $this->renderOutput($include_path, $data);
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
    protected function renderOutput($file_path, array $options = array())
    {
        if (file_exists($file_path)) {
        } else {
            throw new RuntimeException
            ('Molajito AbstractRenderer renderOutput: File not found: ' . $file_path);
        }

        try {
            return $this->render_instance->render($file_path, $options);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito AbstractRenderer renderOutput: '
                . ' File path: ' . $file_path . 'Message: ' . $e->getMessage());
        }
    }
}
