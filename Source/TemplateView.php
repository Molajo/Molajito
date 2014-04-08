<?php
/**
 * Molajito Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EventInterface;
use CommonApi\Render\RenderInterface;
use CommonApi\Render\EscapeInterface;
use Exception;

/**
 * Molajito Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class TemplateView implements RenderInterface
{
    /**
     * Escape Instance
     *
     * @var    object   CommonApi\Render\EscapeInterface
     * @since  1.0
     */
    protected $escape_instance = null;

    /**
     * Render Instance
     *
     * @var    object   CommonApi\Render\RenderInterface
     * @since  1.0
     */
    protected $render_instance = null;

    /**
     * Event Handler
     *
     * @var    object  CommonApi\Render\EventInterface
     * @since  1.0
     */
    protected $event_instance = null;

    /**
     * Event option keys
     *
     * @var    array
     * @since  1.0
     */
    protected $event_option_keys = array();

    /**
     * Path to Include File
     *
     * @var    string
     * @since  1.0
     */
    protected $include_path;

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
     * Parameters
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = null;

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = array();

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0
     */
    protected $query_results = array();

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_view = null;

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_page = null;

    /**
     * Object containing a single row for using within View
     *
     * @var    array
     * @since  1.0
     */
    protected $row = null;

    /**
     * Render Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $render_array = array(
        'plugin_data',
        'runtime_data',
        'parameters',
        'query_results',
        'row'
    );

    /**
     * Constructor
     *
     * @param  EscapeInterface $escape_instance
     * @param  RenderInterface $render_instance
     * @param  EventInterface  $event_instance
     * @param  array           $event_option_keys
     *
     * @since  1.0
     */
    public function __construct(
        EscapeInterface $escape_instance,
        RenderInterface $render_instance,
        EventInterface $event_instance,
        array $event_option_keys = array()
    ) {
        $this->escape_instance   = $escape_instance;
        $this->render_instance   = $render_instance;
        $this->event_instance    = $event_instance;
        $this->event_option_keys = $event_option_keys;
    }

    /**
     * Render output for specified file and data
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     */
    public function render($include_path, array $data = array())
    {
        $this->rendered_view = '';

        $this->include_path = $include_path;

        $this->setProperties($data);

        if (file_exists($this->include_path . '/Custom.phtml')) {
            $this->renderViewCustom();
        } else {
            $this->renderLoop();
        }

        return $this->rendered_view;
    }

    /**
     * Set class properties for input data
     *
     * @param   array $data
     *
     * @return  $this
     * @since   1.0
     */
    protected function setProperties(array $data = array())
    {
        $temp = array_merge($this->render_array, $this->event_option_keys);

        foreach ($temp as $key) {
            if (isset($data[$key])) {
                $this->$key = $data[$key];
            } else {
                $this->$key = null;
            }
        }

        return $this;
    }

    /**
     * Render Template View Head
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewCustom()
    {
        $this->query_results = $this->escape_instance->escape($this->query_results, $this->model_registry);
        $file_path           = $this->include_path . '/Custom.phtml';
        $this->rendered_view = $this->renderOutput($file_path, true);

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

            $temp      = $this->escape_instance->escape(array($this->row), $this->model_registry);
            $this->row = $temp[0];

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
            $this->rendered_view = $this->renderOutput($file_path, false);
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
            $this->rendered_view .= $this->renderOutput($file_path, false);
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
            $this->rendered_view .= $this->renderOutput($file_path, false);
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
        $event_results = $this->event_instance->scheduleEvent($event_name, $options);

        if (count($event_results) > 0 && is_array($event_results)) {
        } else {
            return $this;
        }

        foreach ($event_results as $key => $value) {
            if (in_array($key, $this->event_option_keys)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    /**
     * Render Template View
     *
     * @param   string  $file_path
     * @param   boolean $custom
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderOutput($file_path, $custom = false)
    {
        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['parameters']   = $this->parameters;
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        if ($custom === false) {
            $options['row'] = $this->row;
        } else {
            $options['query_results'] = $this->query_results;
        }

        try {
            return $this->render_instance->render($file_path, $options);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito TemplateView renderOutput: '
            . ' File path: ' . $file_path . 'Message: ' . $e->getMessage());
        }
    }
}
