<?php
/**
 * Pagination Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\ExtensionResourceInterface;
use CommonApi\Render\EventHandlerInterface;
use CommonApi\Render\ParseInterface;
use CommonApi\Render\RenderInterface;
use Exception;
use stdClass;

/**
 * Pagination Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Molajito implements RenderInterface
{
    /**
     * Exclude tokens from parsing (Head tokens held until end)
     *
     * @var    array
     * @since  1.0
     */
    protected $exclude_tokens = array();

    /**
     * Event Handler
     *
     * @var    object  CommonApi\Render\EventHandlerInterface
     * @since  1.0
     */
    protected $event_handler = null;

    /**
     * Event option keys
     *
     * @var    array
     * @since  1.0
     */
    protected $event_option_keys = array();

    /**
     * Resource
     *
     * @var    object  CommonApi\Render\ExtensionResourceInterface
     * @since  1.0
     */
    protected $extension_resource = null;

    /**
     * Stop Parse and Render Loop Count
     *
     * @var    int
     * @since  1.0
     */
    protected $stop_loop_count = 100;

    /**
     * Theme Path
     *
     * @var    string
     * @since  1.0
     */
    protected $theme_path = null;

    /**
     * Page Name
     *
     * @var    string
     * @since  1.0
     */
    protected $page_name = null;

    /**
     * Rendering Properties in associative array
     *
     * @var    array
     * @since  1.0
     */
    protected $rendering_properties = array();

    /**
     * Runtime Data
     *
     * @var    array
     * @since  1.0
     */
    protected $runtime_data = array();

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
     * Tokens to Render
     *
     * @var    array
     * @since  1.0
     */
    protected $tokens = array();

    /**
     * Constructor
     *
     * @param  array                      $exclude_tokens
     * @param  EventHandlerInterface      $event_handler
     * @param  array                      $event_option_keys
     * @param  ExtensionResourceInterface $extension_resource
     * @param  int                        $stop_loop_count
     * @param  string                     $theme_path
     * @param  string                     $page_name
     * @param  object                     $runtime_data
     * @param  array                      $rendering_properties
     *
     * @since  1.0
     */
    public function __construct(
        array $exclude_tokens = array(),
        EventHandlerInterface $event_handler,
        array $event_option_keys = array(),
        ExtensionResourceInterface $extension_resource,
        $stop_loop_count = 100,
        $theme_path,
        $page_name,
        $runtime_data,
        array $rendering_properties = array()
    ) {
        $this->exclude_tokens       = $exclude_tokens;
        $this->event_handler        = $event_handler;
        $this->event_option_keys    = $event_option_keys;
        $this->extension_resource   = $extension_resource;
        $this->stop_loop_count      = $stop_loop_count;
        $this->theme_path           = $theme_path;
        $this->page_name            = $page_name;
        $this->runtime_data         = $runtime_data;
        $this->rendering_properties = $rendering_properties;
    }

    /**
     * Render Output
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function render()
    {
        $options = $this->event_handler->initializeEventOptions();
        $this->scheduleEvent('onBeforeRender', $options);

        $this->renderTheme();

        $this->renderLoop($this->exclude_tokens);

        $this->renderLoop(array());

        $options                  = $this->event_handler->initializeEventOptions();
        $options['rendered_page'] = $this->rendered_page;
        $this->scheduleEvent('onAfterRender', $options);

        return $this;
    }

    /**
     * Render Loop
     *
     * @param   array $exclude_tokens
     *
     * @return  $this
     * @throws  \CommonApi\Exception\RuntimeException
     * @since   1.0
     */
    protected function renderLoop(array $exclude_tokens = array())
    {
        /** Step 1. Initialise */

        $complete     = false;
        $loop_counter = 0;

        while ($complete === false) {

            /** Step 2. Counter */
            $loop_counter ++;

            /** Step 3. Schedule onBeforeParse Event */
            $options                  = $this->event_handler->initializeEventOptions();
            $options['rendered_page'] = $this->rendered_page;
            $options['parameters']    = $this->parameters;

            $this->scheduleEvent('onBeforeParse', $options);

            /** Step 3. Parse Output for Tokens */
            $this->tokens = $this->parseTokens($this->exclude_tokens);

            /** Step 3. Schedule onAfterParse Event */
            $options                  = $this->event_handler->initializeEventOptions();
            $options['rendered_page'] = $this->rendered_page;
            $options['parameters']    = $this->parameters;

            $this->scheduleEvent('onAfterParse', $options);
            if (is_array($this->tokens) && count($this->tokens) > 0
            ) {
            } else {
                $complete = true;
                break;
            }

            /** Step 4. Render Output for Tokens */
            foreach ($this->tokens as $token) {
                $this->renderToken($token);
                unset($this->tokens[$token]);
            }

            if ($loop_counter > $this->stop_loop_count) {
                throw new RuntimeException
                ('Pagination Renderloop: Maximum loop count exceeded: ' . $loop_counter);
            }

            continue;
        }

        return $this;
    }

    /**
     * Instantiate Parse Class
     *
     * @param   array $exclude_tokens
     *
     * @return  array
     * @since   1.0
     */
    protected function parseTokens(array $exclude_tokens = array())
    {
        $instance = new Parse($this->rendered_page, $exclude_tokens);

        return $instance->parse();
    }

    /**
     * Render Token
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderToken($token)
    {
        /** Step 1. Initialise */
        $this->rendered_view = '';

        /** Step 2. Get Rendering Extension */
        try {
            $extension = $this->extension_resource->getExtension($token);

            $this->runtime_data->render->extension = $extension;

        } catch (Exception $e) {
            throw new RuntimeException('Pagination renderToken getExtension Exception ' . $e->getMessage());
        }

        /** Step 3. Get Query Data for Rendering Extension */
        $this->getData($token);

        /** Step 4. Schedule onBeforeRenderView Event */
        $options                   = $this->event_handler->initializeEventOptions();
        $options['parameters']     = $this->parameters;
        $options['query_results']  = $this->query_results;
        $options['model_registry'] = $this->model_registry;
        $options['rendered_page']  = $this->rendered_page;

        $this->scheduleEvent('onBeforeRenderView', $options);

        /** Step 5. Render View */
        $this->include_path = $this->runtime_data->render->extension->include_path;

        if ($this->runtime_data->render->scheme == 'page') {
            $this->renderPageView();

        } else {
            $this->renderTemplateView();

            if ($token->wrap == '') {
            } else {
                $this->renderWrapView($token);
            }
        }

        /** Step 6. Schedule onAfterRenderView Event */
        $options                   = $this->event_handler->initializeEventOptions();
        $options['parameters']     = $this->parameters;
        $options['model_registry'] = $this->model_registry;
        $options['rendered_view']  = $this->rendered_view;
        $options['rendered_page']  = $this->rendered_page;

        $this->scheduleEvent('onAfterRenderView', $options);

        /** Step 7. Inject Rendered Output */
        $this->rendered_page = str_replace($token->replace_this, $this->rendered_view, $this->rendered_page);

        return $this;
    }

    /**
     * Get Data required to render token
     *
     * @return  $this
     * @since   1.0
     */
    protected function getData($token)
    {
        try {
            $instance = new DataResource(
                $this->runtime_data,
                $token);

            $data = $instance->getData();

            $this->query_results  = $data->query_results;
            $this->model_registry = $data->model_registry;
            $this->parameters     = $data->parameters;

        } catch (Exception $e) {
            throw new RuntimeException('Pagination getData Exception ' . $e->getMessage());
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
        $event_results = $this->event_handler->scheduleEvent($event_name, $options);

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
     * Inclusion of the Theme renders initial output that is parsed for tokens
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderTheme()
    {
        $row            = new stdClass();
        $row->page_name = $this->page_name;
        $options        = $this->rendering_properties;
        $options['row'] = $row;

        try {
            $instance = new PageViewRenderer($this->theme_path, $options);

            $this->rendered_page = $instance->render();

        } catch (Exception $e) {
            throw new RuntimeException
            ('Pagination renderTheme: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Render Page View
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderPageView()
    {
        try {
            $instance = new PageViewRenderer(
                $this->include_path,
                $this->getRenderingProperties());

            $this->rendered_view = $instance->render();

        } catch (Exception $e) {
            throw new RuntimeException
            ('Pagination renderTheme: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Render Template View
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderTemplateView()
    {


        /**
         * Render option keys
         *
         * @var    array
         * @since  1.0
         */

        /**
        protected $extract_properties // $event_option_keys= array(
        'runtime_data',
        'parameters',
        'model_registry',
        'query_results',
        'row',
        'rendered_page',
        'rendered_view'
        );
         */
        $instance = new TemplateViewRenderer($this->event_callback,
            $this->runtime_data,
            $this->include_path,
            $this->parameters,
            $this->model_registry,
            $this->query_results,
            $this->rendered_view,
            $this->rendered_page,
            $this->rendering_properties,
            $this->rendering_properties,
            $this->event_handler);

        $this->rendered_view = $instance->render();

        return $this;
    }

    /**
     * Render Wrap View
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderWrapView()
    {
        $model                                 = 'Wrap' . ':///Molajo//View//Wrap//' . ucfirst(
                strtolower($wrap)
            );
        $this->runtime_data->render->scheme    = 'wrap';
        $this->runtime_data->render->extension = $this->resource->get($model);
        $this->include_path                    = $this->runtime_data->render->extension->include_path;

        $row           = new stdClass();
        $row->title    = '';
        $row->subtitle = '';
        $row->content  = $this->rendered_view;

        $instance = new WrapViewRenderer($this->runtime_data,
            $this->include_path,
            $this->parameters,
            $this->model_registry,
            $this->query_results,
            $this->rendered_page,
            $this->rendering_properties,
            $this->rendering_properties);

        $this->rendered_view = $instance->render();

        return $this;
    }


    /**
     * Get Rendering Properties
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getRenderingProperties()
    {
        $options = array();

        foreach ($this->rendering_properties as $key => $value) {
            if (isset($this->key)) {
                $options[$key] = $this->$key;
            } else {
                $options[$key] = $value;
            }
        }

        return $options;
    }
}
