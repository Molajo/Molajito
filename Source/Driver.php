<?php
/**
 * Molajito Driver
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\DataInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\ExtensionInterface;
use CommonApi\Render\ParseInterface;
use CommonApi\Render\PositionInterface;
use CommonApi\Render\RenderInterface;
use Exception;
use stdClass;

/**
 * Molajito Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Driver
{
    /**
     * Parse Instance
     *
     * @var    object  CommonApi\Render\ParseInterface
     * @since  1.0
     */
    protected $parse_instance = null;

    /**
     * Exclude tokens from parsing (tokens to generate head are held until body is processed)
     *
     * @var    array
     * @since  1.0
     */
    protected $exclude_tokens = array();

    /**
     * Stop Parse and Render Loop Count
     *
     * @var    int
     * @since  1.0
     */
    protected $stop_loop_count = 100;

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
     * Retrieve Extension information to Render View
     *
     * @var    object  CommonApi\Render\ExtensionInterface
     * @since  1.0
     */
    protected $extension_instance = null;

    /**
     * Retrieve Data to Render View
     *
     * @var    object  CommonApi\Render\DataInterface
     * @since  1.0
     */
    protected $data_instance = null;

    /**
     * Position Instance
     *
     * @var    object  CommonApi\Render\PositionInterface
     * @since  1.0
     */
    protected $position_instance = null;

    /**
     * Theme Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0
     */
    protected $theme_instance = null;

    /**
     * Page View Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0
     */
    protected $page_instance = null;

    /**
     * Template View Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0
     */
    protected $template_instance = null;

    /**
     * Wrap View Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0
     */
    protected $wrap_instance = null;

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
     * Tokens to Render
     *
     * @var    array
     * @since  1.0
     */
    protected $tokens = array();

    /**
     * Include Path
     *
     * @var    string
     * @since  1.0
     */
    protected $include_path = null;

    /**
     * Constructor
     *
     * @param ParseInterface     $parse_instance
     * @param array              $exclude_tokens
     * @param int                $stop_loop_count
     * @param EventInterface     $event_instance
     * @param array              $event_option_keys
     * @param ExtensionInterface $extension_instance
     * @param DataInterface      $data_instance
     * @param PositionInterface  $position_instance
     * @param RenderInterface    $theme_instance
     * @param RenderInterface    $page_instance
     * @param RenderInterface    $template_instance
     * @param RenderInterface    $wrap_instance
     * @param string             $theme_path
     * @param string             $page_name
     * @param object             $runtime_data
     * @param object             $plugin_data
     *
     * @since  1.0
     */
    public function __construct(
        ParseInterface $parse_instance,
        array $exclude_tokens = array(),
        $stop_loop_count = 100,
        EventInterface $event_instance,
        array $event_option_keys = array(),
        ExtensionInterface $extension_instance,
        DataInterface $data_instance,
        PositionInterface $position_instance,
        RenderInterface $theme_instance,
        RenderInterface $page_instance,
        RenderInterface $template_instance,
        RenderInterface $wrap_instance,
        $theme_path,
        $page_name,
        $runtime_data,
        $plugin_data
    ) {
        $this->parse_instance     = $parse_instance;
        $this->exclude_tokens     = $exclude_tokens;
        $this->stop_loop_count    = $stop_loop_count;
        $this->event_instance     = $event_instance;
        $this->event_option_keys  = $event_option_keys;
        $this->extension_instance = $extension_instance;
        $this->data_instance      = $data_instance;
        $this->position_instance  = $position_instance;
        $this->theme_instance     = $theme_instance;
        $this->page_instance      = $page_instance;
        $this->template_instance  = $template_instance;
        $this->wrap_instance      = $wrap_instance;
        $this->theme_path         = $theme_path;
        $this->page_name          = $page_name;
        $this->runtime_data       = $runtime_data;
        $this->plugin_data        = $plugin_data;
    }

    /**
     * Manages processing from start to end of rendering
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function process()
    {
        /** Step 1. Schedule onBeforeRender Event */
        $options = $this->event_instance->initializeEventOptions();
        $this->scheduleEvent('onBeforeRender', $options);

        /** Step 2. Render Theme */
        $this->renderTheme();

        /** Step 3. Render Body */
        $this->renderLoop($this->exclude_tokens);

        /** Step 4. Render Head */
        $this->renderLoop(array());

        /** Step 5. Schedule onAfterRender Event */
        $options                  = $this->event_instance->initializeEventOptions();
        $options['rendered_page'] = $this->rendered_page;
        $this->scheduleEvent('onAfterRender', $options);

        return $this;
    }

    /**
     * Render Loop - runs twice, first time to render Body, second time to render Head
     *
     * @param   array $exclude_tokens
     *
     * @return  $this
     * @throws  \CommonApi\Exception\RuntimeException
     * @since   1.0
     */
    protected function renderLoop(array $exclude_tokens = array())
    {
        $loop_counter = 0;

        while (true === true) {

            $loop_counter ++;

            /** Step 1. Schedule onBeforeParse Event */
            $options                  = $this->event_instance->initializeEventOptions();
            $options['rendered_page'] = $this->rendered_page;
            $options['parameters']    = $this->parameters;

            $this->scheduleEvent('onBeforeParse', $options);

            /** Step 2. Parse Output for Tokens */
            $this->tokens = $this->parseTokens($exclude_tokens);

            /** Step 3. Schedule onAfterParse Event */
            $options                  = $this->event_instance->initializeEventOptions();
            $options['rendered_page'] = $this->rendered_page;
            $options['parameters']    = $this->parameters;

            $this->scheduleEvent('onAfterParse', $options);

            if (is_array($this->tokens) && count($this->tokens) > 0) {
            } else {
                break;
            }

            /** Step 4. Render Output for Tokens */
            $tokens = $this->tokens;

            foreach ($tokens as $token) {

                if (strtolower($token->type) == 'position') {
                    $this->renderPosition($token);
                } else {
                    $this->renderToken($token);
                }

                $this->replaceTokenWithRenderedOutput($token);
            }

            /** Step 5: Check Max Loop Count and stop or continue */
            if ($loop_counter > $this->stop_loop_count) {
                throw new RuntimeException
                ('Molajito Renderloop: Maximum loop count exceeded: ' . $loop_counter);
            }

            continue;
        }

        return $this;
    }

    /**
     * Invoke Parse Class to retrieve tokens to use in rendering
     *
     * @param   array $exclude_tokens
     *
     * @return  array
     * @since   1.0
     */
    protected function parseTokens(array $exclude_tokens = array())
    {
        return $this->parse_instance->parse($this->rendered_page, $exclude_tokens);
    }

    /**
     * Render Token for Position Type
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderPosition($token)
    {
        /** Step 1. Initialise */
        $position_name = $token->name;

        /** Step 2. Render Position */
        try {
            $this->rendered_view = $this->position_instance->getPositionViews(
                $position_name,
                $this->plugin_data->resource->extension
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito renderPosition Method Failed: ' . $e->getMessage());
        }

        return $this;
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
echo 'View:  ' . $token->name . '<br />';

        /** Step 1. Initialise */
        $this->rendered_view = '';

        /** Step 2. Get Rendering Extension */
        $this->getExtension($token);

        if ($this->plugin_data->render->extension->title == $token->name) {
        } else {
            $token->name = $this->plugin_data->render->extension->title;
        }

        /** Step 3. Get Query Data for Rendering Extension */
        $this->getData($token);

        /** Step 4. Schedule onBeforeRenderView Event */
        $options                   = $this->event_instance->initializeEventOptions();
        $options['parameters']     = $this->parameters;
        $options['query_results']  = $this->query_results;
        $options['model_registry'] = $this->model_registry;
        $options['rendered_page']  = $this->rendered_page;

        $this->scheduleEvent('onBeforeRenderView', $options);

        /** Step 5. Render View */
        $this->include_path = $this->plugin_data->render->extension->include_path;

        if ($this->plugin_data->render->scheme == 'page') {
            $this->renderPageView();

        } else {
            $this->renderTemplateView();

            if ($token->wrap == '') {
            } else {
                $this->renderWrapView($token);
            }
        }

        /** Step 6. Schedule onAfterRenderView Event */
        $options                   = $this->event_instance->initializeEventOptions();
        $options['parameters']     = $this->parameters;
        $options['query_results']  = $this->query_results;
        $options['model_registry'] = $this->model_registry;
        $options['rendered_view']  = $this->rendered_view;
        $options['rendered_page']  = $this->rendered_page;

        $this->scheduleEvent('onAfterRenderView', $options);

        return $this;
    }

    /**
     * Inclusion of the Theme file results in initial rendered output parsed for tokens
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderTheme()
    {
        $options = $this->setOptionValues();

        $row            = new stdClass();
        $row->page_name = $this->page_name;
        $options['row'] = $row;

        try {
            $this->rendered_page = $this->theme_instance->render(
                $this->theme_path,
                $options
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito renderTheme Method Failed: ' . $e->getMessage());
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
        $options = $this->setOptionValues();

        try {
            $this->rendered_view = $this->page_instance->render(
                $this->include_path,
                $options
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito Driver renderPageView Method Failed. '
            . ' File path: ' . $this->include_path . ' Message: ' . $e->getMessage());
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
        $options = $this->setOptionValues();

        try {
            $this->rendered_view = $this->template_instance->render(
                $this->include_path,
                $options
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito Driver renderTemplateView Method Failed. '
            . ' File path: ' . $this->include_path . ' Message: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Render Wrap View
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderWrapView($token)
    {
        /** Step 1. Get Rendering Extension */
        $wrap_token               = new stdClass();
        $wrap_token->type         = 'wrap';
        $wrap_token->name         = $token->wrap;
        $wrap_token->wrap         = '';
        $wrap_token->attributes   = $token->attributes;
        $wrap_token->replace_this = '';

        $this->getExtension($wrap_token);

        $this->include_path = $this->plugin_data->render->extension->include_path;

        /** Step 2. Data */
        $options        = $this->setOptionValues();
        $row            = new stdClass();
        $row->title     = '';
        $row->subtitle  = '';
        $row->content   = $this->rendered_view;
        $options['row'] = $row;

        /** Step 3. Render Wrap */
        try {
            $this->rendered_view = $this->wrap_instance->render(
                $this->include_path,
                $options
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito Driver renderWrapView Method Failed. '
            . ' File path: ' . $this->include_path . ' Message: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Replace Token with Rendered Output
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     */
    protected function replaceTokenWithRenderedOutput($token)
    {
        $this->rendered_page = str_replace($token->replace_this, $this->rendered_view, $this->rendered_page);

        return $this;
    }

    /**
     * Get Data required to render token
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getExtension($token)
    {
        try {
            $this->plugin_data->render = $this->extension_instance->getExtension($token);

        } catch (Exception $e) {
            throw new RuntimeException('Molajito renderToken getExtension Exception ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Get Data required to render token
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getData($token)
    {
        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        try {
            $data = $this->data_instance->getData($token, $options);

            $this->query_results  = $data->query_results;
            $this->model_registry = $data->model_registry;
            $this->parameters     = $data->parameters;

        } catch (Exception $e) {
            throw new RuntimeException('Molajito getData Exception for '
            . ' Token: ' . $token->name . ' Message: ' . $e->getMessage());
        }

        return $this;
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
     * Set Option Properties for passing into Event and Rendering Classes
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setOptionValues()
    {
        $options = array();

        foreach ($this->event_option_keys as $key) {
            if (isset($this->$key)) {
                $options[$key] = $this->$key;
            } else {
                $options[$key] = null;
            }
        }

        return $options;
    }
}
