<?php
/**
 * Molajito Engine
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Language\TranslateInterface;
use CommonApi\Render\DataInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\ParseInterface;
use CommonApi\Render\PositionInterface;
use CommonApi\Render\RenderInterface;
use CommonApi\Render\ViewInterface;
use Exception;
use stdClass;

/**
 * Molajito Engine
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Engine implements RenderInterface
{
    /**
     * Retrieve Data to Render View
     *
     * @var    object  CommonApi\Render\DataInterface
     * @since  1.0.0
     */
    protected $data_instance = NULL;

    /**
     * Retrieve View information for rendering
     *
     * @var    object  CommonApi\Render\ViewInterface
     * @since  1.0.0
     */
    protected $view_instance = NULL;

    /**
     * Event Handler
     *
     * @var    object  CommonApi\Render\EventInterface
     * @since  1.0.0
     */
    protected $event_instance = NULL;

    /**
     * Event option keys
     *
     * @var    array
     * @since  1.0.0
     */
    protected $event_option_keys = array(
        'runtime_data',
        'plugin_data',
        'parameters',
        'model_registry',
        'query_results',
        'row',
        'rendered_view',
        'rendered_page'
    );

    /**
     * Parse Instance
     *
     * @var    object  CommonApi\Render\ParseInterface
     * @since  1.0.0
     */
    protected $parse_instance = NULL;

    /**
     * Exclude tokens from parsing (tokens to generate head are held until body is processed)
     *
     * @var    array
     * @since  1.0.0
     */
    protected $exclude_tokens = array();

    /**
     * Stop Parse and Render Loop Count
     *
     * @var    int
     * @since  1.0.0
     */
    protected $stop_loop_count = 100;

    /**
     * Position Instance
     *
     * @var    object  CommonApi\Render\PositionInterface
     * @since  1.0.0
     */
    protected $position_instance = NULL;

    /**
     * Theme Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $theme_instance = NULL;

    /**
     * Page View Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $page_instance = NULL;

    /**
     * Template View Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $template_instance = NULL;

    /**
     * Wrap View Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $wrap_instance = NULL;

    /**
     * Wrap View Instance
     *
     * @var    object  CommonApi\Language\TranslateInterface
     * @since  1.0.0
     */
    protected $translate_instance = NULL;

    /**
     * Theme Path
     *
     * @var    string
     * @since  1.0.0
     */
    protected $theme_path = NULL;

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
    protected $model_registry = array();

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
     * Tokens to Render
     *
     * @var    array
     * @since  1.0.0
     */
    protected $tokens = array();

    /**
     * Include Path
     *
     * @var    string
     * @since  1.0.0
     */
    protected $include_path = NULL;

    /**
     * Render Properties
     *
     * @var    object
     * @since  1.0.0
     */
    protected $render_array = array(
        'plugin_data',
        'runtime_data',
        'model_registry',
        'parameters',
        'query_results',
        'row'
    );

    /**
     * Constructor
     *
     * @param DataInterface      $data_instance
     * @param ViewInterface      $view_instance
     * @param EventInterface     $event_instance
     * @param array              $event_option_keys
     * @param ParseInterface     $parse_instance
     * @param array              $exclude_tokens
     * @param int                $stop_loop_count
     * @param PositionInterface  $position_instance
     * @param RenderInterface    $theme_instance
     * @param RenderInterface    $page_instance
     * @param RenderInterface    $template_instance
     * @param RenderInterface    $wrap_instance
     * @param TranslateInterface $translate_instance
     *
     * @since  1.0.0
     */
    public function __construct(
        DataInterface $data_instance,
        ViewInterface $view_instance,
        EventInterface $event_instance,
        array $event_option_keys = array(),
        ParseInterface $parse_instance,
        array $exclude_tokens = array(),
        $stop_loop_count = 100,
        PositionInterface $position_instance,
        RenderInterface $theme_instance,
        RenderInterface $page_instance,
        RenderInterface $template_instance,
        RenderInterface $wrap_instance,
        TranslateInterface $translate_instance
    ) {
        $this->data_instance      = $data_instance;
        $this->view_instance      = $view_instance;
        $this->event_instance     = $event_instance;
        $this->parse_instance     = $parse_instance;
        $this->exclude_tokens     = $exclude_tokens;
        $this->stop_loop_count    = $stop_loop_count;
        $this->position_instance  = $position_instance;
        $this->theme_instance     = $theme_instance;
        $this->page_instance      = $page_instance;
        $this->template_instance  = $template_instance;
        $this->wrap_instance      = $wrap_instance;
        $this->translate_instance = $translate_instance;

        if (count($event_option_keys) > 0) {
            $this->event_option_keys = $event_option_keys;
        }
    }

    /**
     * Render output for specified view and data
     *
     * @param   string $include_file
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function render($include_file, array $data = array())
    {
        /** Step 0. Initialise */
        $this->initialiseData($data);

        /** Step 1. Schedule onBeforeRender Event */
        $this->scheduleEvent('onBeforeRender', $this->setOptionValues());

        /** Step 2. Render Theme */
        $this->renderTheme($include_file);

        /** Step 3. Render Body */
        $this->renderLoop($this->exclude_tokens);

        /** Step 4. Render Head */
        $this->renderLoop(array());

        /** Step 5. Translate */
        $this->rendered_page = $this->translate_instance->translate($this->rendered_page);

        /** Step 6. Schedule onAfterRender Event */
        $options                  = $this->setOptionValues();
        $options['rendered_page'] = $this->rendered_page;

        $this->scheduleEvent('onAfterRender', $options);

        return $this->rendered_page;
    }

    /**
     * Initialise Class Data
     *
     * @param   array $data
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function initialiseData(array $data = array())
    {
        if (isset($data['runtime_data'])) {
            $this->runtime_data = $data['runtime_data'];
        } else {
            throw new RuntimeException ('Molajito Renderer requires Runtime Data');
        }

        if (isset($data['page_name'])) {
            $this->runtime_data->page_name = $data['page_name'];
        }

        if (isset($data['plugin_data'])) {
            $this->plugin_data = $data['plugin_data'];
        } else {
            $this->plugin_data = new stdClass();
        }

        $this->parameters     = NULL;
        $this->model_registry = array();
        $this->query_results  = array();
        $this->rendered_view  = NULL;
        $this->rendered_page  = NULL;
        $this->tokens         = array();
        $this->include_path   = NULL;

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

        while (TRUE === TRUE) {

            $loop_counter++;

            /** Step 1. Schedule onBeforeParse Event */
            $options                  = $this->setOptionValues();
            $options['rendered_page'] = $this->rendered_page;
            $options['parameters']    = $this->parameters;

            $this->scheduleEvent('onBeforeParse', $options);

            /** Step 2. Parse Output for Tokens */
            $this->tokens = $this->parseTokens($exclude_tokens);

            /** Step 3. Schedule onAfterParse Event */
            $options                  = $this->setOptionValues();
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
                (
                    'Molajito renderLoop: Maximum loop count exceeded: ' . $loop_counter
                );
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
            $this->rendered_view = $this->position_instance->getPositionTemplateViews(
                $position_name,
                $this->runtime_data->render->extension
            );

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito renderPosition Method Failed: ' . $e->getMessage()
            );
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
//echo '<br><br>View:  ' . $token->name . '<br>';
//echo '<pre>';
//var_dump($token);
//echo '<pre>';

        /** Step 1. Initialise */
        $this->rendered_view = '';

        /** Step 2. Get Rendering Extension */
        $this->getView($token);

        if ($this->runtime_data->render->extension->title == $token->name) {
        } else {
            $token->name = $this->runtime_data->render->extension->title;
        }

        /** Step 3. Get Query Data for Rendering Extension */
        $this->getData($token);

        /** Step 4. Schedule onBeforeRenderView Event */
        $options                   = $this->setOptionValues();
        $options['parameters']     = $this->parameters;
        $options['query_results']  = $this->query_results;
        $options['model_registry'] = $this->model_registry;
        $options['rendered_page']  = $this->rendered_page;

        $this->scheduleEvent('onBeforeRenderView', $options);

        /** Step 5. Render View */
        $this->include_path = $this->runtime_data->render->extension->include_path;

        if (strtolower($this->runtime_data->render->scheme) == 'page') {
            $this->renderPageView();

        } else {

            $this->renderTemplateView();

            if ($token->wrap == '') {
            } else {
                $this->renderWrapView($token);
            }
        }

        /** Step 6. Schedule onAfterRenderView Event */
        $options                   = $this->setOptionValues();
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
     * @param   string $include_file
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderTheme($include_file)
    {
        $options = $this->setOptionValues();

        $this->theme_path = $include_file;

        try {
            $this->rendered_page = $this->theme_instance->render(
                $this->theme_path,
                $options
            );

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito renderTheme Method Failed: ' . $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * Render Page View
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderPageView()
    {
        return $this->renderViewType('page_instance');
    }

    /**
     * Render Template View
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderTemplateView()
    {
        return $this->renderViewType('template_instance');
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

        $this->getView($wrap_token);

        $this->include_path = $this->runtime_data->render->extension->include_path;

        /** Step 2. Data */
        $options        = $this->setOptionValues();
        $row            = new stdClass();
        $row->title     = '';
        $row->subtitle  = '';
        $row->content   = $this->rendered_view;
        $options['row'] = $row;

        /** Step 3. Render Wrap */

        return $this->renderViewType('wrap_instance');
    }

    /**
     * Render Object
     *
     * @param   string $type
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderViewType($type)
    {
        $options = $this->setOptionValues();

        try {
            $this->rendered_view = $this->$type->render(
                $this->include_path,
                $options
            );

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito Driver renderObject Method Failed. Type: ' . $type
                . ' File path: ' . $this->include_path . ' Message: ' . $e->getMessage()
            );
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
    protected function getView($token)
    {
        try {
            $this->runtime_data->render = $this->view_instance->getView($token);

        } catch (Exception $e) {
            throw new RuntimeException('Molajito renderToken getView Exception ' . $e->getMessage());
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
            throw new RuntimeException(
                'Molajito getData Exception for '
                . ' Token: ' . $token->name . ' Message: ' . $e->getMessage()
            );
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

        if (count($event_results) > 0) {
            foreach ($event_results as $key => $value) {
                if (in_array($key, $this->event_option_keys)) {
                    $this->$key = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Set Option Properties for passing into Event and Rendering Classes
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setOptionValues()
    {
        $options = $this->event_instance->initializeEventOptions();

        $temp = array_unique(array_merge($this->render_array, $this->event_option_keys));

        foreach ($temp as $key) {
            if (isset($this->$key)) {
                $options[$key] = $this->$key;
            }
        }

        return $options;
    }
}
