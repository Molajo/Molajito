<?php
/**
 * Molajito Token Processor
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Render\DataInterface;
use CommonApi\Render\EscapeInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\PositionInterface;
use CommonApi\Render\RenderInterface;
use CommonApi\Render\TokenInterface;
use CommonApi\Render\ViewInterface;
use stdClass;

/**
 * Molajito Token Processor
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Token implements TokenInterface
{
    /**
     * Retrieve Data to Render View
     *
     * @var    object  CommonApi\Render\DataInterface
     * @since  1.0.0
     */
    protected $data_instance = null;

    /**
     * Retrieve View information for rendering
     *
     * @var    object  CommonApi\Render\ViewInterface
     * @since  1.0.0
     */
    protected $view_instance = null;

    /**
     * Theme Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $theme_instance = null;

    /**
     * Position Instance
     *
     * @var    object  CommonApi\Render\PositionInterface
     * @since  1.0.0
     */
    protected $position_instance = null;

    /**
     * Page View Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $page_instance = null;

    /**
     * Template View Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $template_instance = null;

    /**
     * Wrap View Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $wrap_instance = null;

    /**
     * Rendered Page
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page = null;

    /**
     * Plugin Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $plugin_data = null;

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $runtime_data = null;

    /**
     * Include Path
     *
     * @var    string
     * @since  1.0.0
     */
    protected $include_path = null;

    /**
     * Token
     *
     * @var    object
     * @since  1.0.0
     */
    protected $token = null;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $model_registry = array();

    /**
     * Parameters
     *
     * @var    array
     * @since  1.0.0
     */
    protected $parameters = array();

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_results = array();

    /**
     * Row
     *
     * @var    object
     * @since  1.0.0
     */
    protected $row = null;

    /**
     * Rendered View
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_view = null;

    /**
     * Data
     *
     * @var    array
     * @since  1.0.0
     */
    protected $data = array();

    /**
     * Render Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'rendered_page',
            'plugin_data',
            'runtime_data',
            'include_path',
            'model_registry',
            'parameters',
            'query_results',
            'row',
            'rendered_view'
        );

    /**
     * Render Types
     *
     * @var    array
     * @since  1.0.0
     */
    protected $render_types
        = array(
            'theme'    =>
                array(
                    'onBeforeEvent'   => 'onBeforeRender',
                    'method'          => 'renderOutput',
                    'onAfterEvent'    => null,
                    'render_instance' => 'theme_instance',
                    'getView'         => false,
                    'getData'         => false
                ),
            'position' =>
                array(
                    'onBeforeEvent'   => null,
                    'method'          => 'renderPosition',
                    'onAfterEvent'    => null,
                    'render_instance' => 'position_instance',
                    'getView'         => false,
                    'getData'         => false
                ),
            'page'     =>
                array(
                    'onBeforeEvent'   => 'onBeforeRenderView',
                    'method'          => 'renderOutput',
                    'onAfterEvent'    => 'onAfterRenderView',
                    'render_instance' => 'page_instance',
                    'getView'         => true,
                    'getData'         => true
                ),
            'template' =>
                array(
                    'onBeforeEvent'   => 'onBeforeRenderView',
                    'method'          => 'renderTemplateView',
                    'onAfterEvent'    => 'onAfterRenderView',
                    'render_instance' => 'template_instance',
                    'getView'         => true,
                    'getData'         => true
                )
        );

    /**
     * Constructor
     *
     * @param  DataInterface     $data_instance
     * @param  ViewInterface     $view_instance
     * @param  RenderInterface   $theme_instance
     * @param  PositionInterface $position_instance
     * @param  RenderInterface   $page_instance
     * @param  RenderInterface   $template_instance
     * @param  RenderInterface   $wrap_instance
     *
     * @since  1.0.0
     */
    public function __construct(
        DataInterface $data_instance,
        ViewInterface $view_instance,
        RenderInterface $theme_instance,
        PositionInterface $position_instance,
        RenderInterface $page_instance,
        RenderInterface $template_instance,
        RenderInterface $wrap_instance
    ) {
        $this->data_instance     = $data_instance;
        $this->view_instance     = $view_instance;
        $this->theme_instance    = $theme_instance;
        $this->position_instance = $position_instance;
        $this->page_instance     = $page_instance;
        $this->template_instance = $template_instance;
        $this->wrap_instance     = $wrap_instance;
    }

    /**
     * Process Token to render specified View (or Theme) and Data
     *
     * @param   object $token
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     */
    public function processToken($token, array $data = array())
    {
        $this->initialiseData($token, $data);

        $this->scheduleEvent('onBeforeEvent');

        $method              = $this->render_types[ $this->token->type ]['method'];
        $this->rendered_view = $this->$method();

        $this->scheduleEvent('onAfterEvent');

        $this->replaceTokenWithRenderedOutput();

        return $this->rendered_page;
    }

    /**
     * Initialise Class Data
     *
     * @param   object $token
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function initialiseData($token, array $data = array())
    {
        $this->token = $token;

        $this->setClassProperties($data, true);

        $this->data = array();

        $token_type = $this->token->type;

        if ($this->render_types[ $this->token->type ]['getView'] === true) {
            $this->getView();
        }

        if ($this->render_types[ $this->token->type ]['getData'] === true) {
            $this->getData();
        }

        return $token_type;
    }

    /**
     * Schedule Event
     *
     * @param   string $event_name
     *
     * @return  $this
     * @since   1.0
     */
    public function scheduleEvent($event_name)
    {
        if ($this->render_types[ $this->token->type ][ $event_name ] === null) {
            return $this;
        }

        $render_instance = $this->render_types[ $this->token->type ]['render_instance'];

        $event_results
            = $this->$render_instance->scheduleEvent(
                $this->render_types[ $this->token->type ][ $event_name ],
                $this->setOptions()
        );

        $this->setClassProperties($event_results);

        if (isset($event_results['token'])) {
            $this->token = $event_results['token'];
        }

        return $this;
    }

    /**
     * Render Template View Token
     *
     * @return  string
     * @since   1.0
     */
    protected function renderTemplateView()
    {
        $rendered_view = $this->renderOutput();

        if ($this->token->wrap === '') {
        } else {
            $rendered_view = $this->renderWrapView($rendered_view);
        }

        return $rendered_view;
    }

    /**
     * Render Output for Token
     *
     * @return  string
     * @since   1.0
     */
    protected function renderOutput()
    {
        $render_instance = $this->render_types[ $this->token->type ]['render_instance'];

        return $this->$render_instance->renderOutput(
            $this->include_path,
            $this->setOptions()
        );
    }

    /**
     * Render Wrap View
     *
     * @param   string $rendered_view
     *
     * @return  string
     * @since   1.0
     */
    protected function renderWrapView($rendered_view)
    {
        $hold_token = $this->token;

        $this->initializeWrapViewObject($hold_token->wrap, $hold_token->attributes);

        $this->getView();

        return $this->wrap_instance->renderOutput(
            $this->include_path,
            $this->getWrapData()
        );
    }

    /**
     * Initialize Wrap View Object
     *
     * @return  $this
     * @since   1.0
     */
    protected function initializeWrapViewObject($wrap_name, $attributes)
    {
        $this->token               = new stdClass();
        $this->token->type         = 'wrap';
        $this->token->name         = $wrap_name;
        $this->token->wrap         = '';
        $this->token->attributes   = $attributes;
        $this->token->replace_this = '';

        return $this;
    }

    /**
     * Get Data for Wrap
     *
     * @return  array
     * @since   1.0
     */
    protected function getWrapData()
    {
        $this->include_path = $this->runtime_data->render->extension->include_path;

        $options = $this->setOptions();

        $options['model_registry'] = array();
        $options['query_results']  = array();

        $row           = new stdClass();
        $row->title    = '';
        $row->subtitle = '';
        $row->content  = $this->rendered_view;

        $options['row'] = $row;

        return $options;
    }

    /**
     * Get View for Token
     *
     * @return $this
     * @since 1.0
     */
    protected function getView()
    {
        $this->runtime_data->render = $this->view_instance->getView();

        if ($this->runtime_data->render->extension->title === $this->token->name) {
        } else {
            $this->token->name = $this->runtime_data->render->extension->title;
        }

        return $this;
    }

    /**
     * Get Data required to render token
     *
     * @return  $this
     * @since   1.0
     */
    protected function getData()
    {
        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        $data = $this->data_instance->getData($this->token, $options);

        $this->query_results  = $data->query_results;
        $this->model_registry = $data->model_registry;
        $this->parameters     = $data->parameters;
        $this->include_path   = $this->runtime_data->render->extension->include_path;

        return $this;
    }

    /**
     * Set Class Properties given array of data
     *
     * @param   array   $data
     * @param   boolean $initialise
     *
     * @return  $this
     * @since   1.0
     */
    protected function setClassProperties(array $data = array(), $initialise = false)
    {
        foreach ($this->property_array as $key) {
            if (isset($data[ $key ])) {
                $this->$key = $data[ $key ];
            } else {
                if ($initialise === true) {
                    $this->$key = null;
                }
            }
        }

        return $this;
    }

    /**
     * Set Option Properties for Events and Rendering
     *
     * @return  array
     * @since   1.0
     */
    protected function setOptions()
    {
        $options = array();

        foreach ($this->property_array as $key) {
            $options[ $key ] = $this->$key;
        }

        $options['token'] = $this->token;

        return $options;
    }

    /**
     * Replace Token with Rendered Output
     *
     * @return  string
     * @since   1.0
     */
    protected function replaceTokenWithRenderedOutput()
    {
        if ($this->token->type === 'theme') {
            $this->rendered_page = $this->rendered_view;
        } else {
            $this->rendered_page
                = str_replace($this->token->replace_this, trim($this->rendered_view), $this->rendered_page);
        }

        return $this->rendered_page;
    }
}
