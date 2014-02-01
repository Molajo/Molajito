<?php
/**
 * Pagination Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use Exception;
use CommonApi\Render\RenderInterface;
use CommonApi\Exception\RuntimeException;
use stdClass;

/**
 * Pagination Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class TemplateViewRenderer implements RenderInterface
{
    /**
     * Path to Include File
     *
     * @var    string
     * @since  1.0
     */
    protected $include_path;

    /**
     * Event option keys
     *
     * @var    array
     * @since  1.0
     */
    protected $event_option_keys = array();

    /**
     * Schedule Event - anonymous function to event_callback method
     *
     * @var    callable
     * @since  1.0
     */
    protected $event_callback;

    /**
     * Render option keys
     *
     * @var    array
     * @since  1.0
     */
    protected $rendering_properties = array();

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data;

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0
     */
    protected $parameters = null;

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = null;

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0
     */
    protected $query_results = array();

    /**
     * Object containing a single row for using within View
     *
     * @var    array
     * @since  1.0
     */
    protected $row = null;

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_page = null;

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_view = null;

    /**
     * Constructor
     *
     * @param  string   $include_path
     * @param  array    $event_option_keys
     * @param  callable $event_callback
     * @param  array    $rendering_properties
     *
     * @since  1.0
     */
    public function __construct(
        $include_path,
        array $event_option_keys = array(),
        callable $event_callback = null,
        array $rendering_properties = array()
    ) {
        $this->include_path      = $include_path;
        $this->event_option_keys = $event_option_keys;
        $this->event_callback    = $event_callback;

        foreach ($this->event_option_keys as $key) {
            if (isset($rendering_properties[$key])) {
                $this->$key = $rendering_properties[$key];
                unset($rendering_properties[$key]);
            }
        }

        $this->rendering_properties = $rendering_properties;
        $this->row                  = new stdClass();
    }

    /**
     * Render Template View
     *
     * @return  string
     * @since   1.0
     */
    public function render()
    {
        $this->rendered_view = '';

        if (file_exists($this->include_path . '/Custom.phtml')) {
            $this->renderViewCustom();
        } else {
            $this->renderLoop();
        }

        return $this->rendered_view;
    }

    /**
     * Render Template View Head
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewCustom()
    {
        $file_path = $this->include_path . '/Custom.phtml';

        if (file_exists($file_path)) {
            $this->rendered_view = $this->renderOutput($file_path);
        }

        return $this;
    }

    /**
     * Render Template Views Loop
     *
     * @return  string
     * @since   1.0
     */
    public function renderLoop()
    {
        $total_rows          = count($this->query_results);
        $row_count           = 1;
        $first               = 1;
        $even_or_odd         = 'odd';
        $this->rendered_view = '';

        if (count($this->query_results) > 0) {
        } else {
            return $this;
        }

        foreach ($this->query_results as $this->row) {

            if ($row_count == $total_rows) {
                $last_row = 1;
            } else {
                $last_row = 0;
            }

            $this->row->row_count   = $row_count;
            $this->row->even_or_odd = $even_or_odd;
            $this->row->total_rows  = $total_rows;
            $this->row->last_row    = $last_row;
            $this->row->first       = $first;

            if ($first === 1) {
                $this->renderViewHead();
            }

            $this->renderViewBody();

            if ($last_row == 1) {
                $this->renderViewFooter();
            }

            if ($even_or_odd == 'odd') {
                $even_or_odd = 'even';
            } else {
                $even_or_odd = 'odd';
            }

            $row_count ++;

            $first = 0;
        }

        return $this->rendered_view;
    }

    /**
     * Render Template View Head
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewHead()
    {
        $options                   = $this->initializeEventOptions();
        $options['parameters']     = $this->parameters;
        $options['model_registry'] = $this->model_registry;
        $options['row']            = $this->row;
        $options['rendered_view']  = $this->rendered_view;
        $options['rendered_page']  = $this->rendered_page;

        $this->scheduleEvent('onBeforeRenderViewHead', $options);

        $file_path = $this->include_path . '/Header.phtml';

        if (file_exists($file_path)) {
            $this->rendered_view = $this->renderOutput($file_path);
        }

        return $this;
    }

    /**
     * Render Template View Body
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewBody()
    {
        $options                   = $this->initializeEventOptions();
        $options['parameters']     = $this->parameters;
        $options['model_registry'] = $this->model_registry;
        $options['row']            = $this->row;
        $options['rendered_view']  = $this->rendered_view;
        $options['rendered_page']  = $this->rendered_page;

        $this->scheduleEvent('onBeforeRenderViewItem', $options);

        $file_path = $this->include_path . '/Body.phtml';

        if (file_exists($file_path)) {
            $this->rendered_view .= $this->renderOutput($file_path);
        }

        return $this;
    }

    /**
     * Render Template View Body
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewFooter()
    {
        $options                   = $this->initializeEventOptions();
        $options['parameters']     = $this->parameters;
        $options['model_registry'] = $this->model_registry;
        $options['row']            = $this->row;
        $options['rendered_view']  = $this->rendered_view;
        $options['rendered_page']  = $this->rendered_page;

        $this->scheduleEvent('onBeforeRenderViewFooter', $options);

        $file_path = $this->include_path . '/Footer.phtml';

        if (file_exists($file_path)) {
            $this->rendered_view .= $this->renderOutput($file_path);
        }

        return $this;
    }

    /**
     * Initialise Options Array for Event
     *
     * @return  array
     * @since   1.0
     */
    protected function initializeEventOptions()
    {
        $options = array();

        foreach ($this->event_option_keys as $key) {
            $options[$key] = null;
        }

        return $options;
    }

    /**
     * Schedule the Render Event
     *
     * @param   string $event_name
     * @param   array  $options
     *
     * @return  $this
     * @since   1.0
     */
    protected function scheduleEvent($event_name, $options)
    {
        $schedule_event = $this->event_callback;

        try {
            $event_results = $schedule_event($event_name, $options);


        } catch (Exception $e) {
            throw new RuntimeException
            ('Pagination TemplateViewRenderer scheduleEvent: ' . $e->getMessage());
        }

        if (count($event_results) == 0) {
            return $this;
        }

        foreach ($event_results as $key => $value) {
            if (in_array($key, $this->event_option_keys)) {
                $this->$key = $value;
            }
        }

        if (is_object($this->row)) {
        } else {
            $this->row = new stdClass();
        }

        return $this;
    }

    /**
     * Instantiate Render Class and Render Output
     *
     * @param   string $file_path
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderOutput($file_path)
    {
        $options = $this->rendering_properties;

        foreach ($this->event_option_keys as $key => $value) {
            if (isset($this->$key)) {
                $options[$key] = $this->$key;
            } elseif (isset($this->rendering_properties[$key])) {
                $options[$key] = $this->rendering_properties[$key];
            }
        }

        $options['include_path'] = $file_path;

        try {
            $instance = new Render($options);

            return $instance->render();

        } catch (Exception $e) {
            throw new RuntimeException
            ('Pagination TemplateViewRenderer renderOutput: ' . $e->getMessage());
        }
    }
}
