<?php
/**
 * Molajito Token Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\DataInterface;
use CommonApi\Render\EscapeInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\PositionInterface;
use CommonApi\Render\RenderInterface;
use CommonApi\Render\ViewInterface;
use stdClass;

/**
 * Molajito Token Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Token extends AbstractRenderer implements RenderInterface
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
     * Theme Path
     *
     * @var    string
     * @since  1.0.0
     */
    protected $theme_path = null;

    /**
     * Render Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
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
     * @param  EscapeInterface   $escape_instance
     * @param  RenderInterface   $render_instance
     * @param  EventInterface    $event_instance
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
        EscapeInterface $escape_instance,
        RenderInterface $render_instance,
        EventInterface $event_instance,
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
        $this->event_instance    = $event_instance;
        $this->theme_instance    = $theme_instance;
        $this->position_instance = $position_instance;
        $this->page_instance     = $page_instance;
        $this->template_instance = $template_instance;
        $this->wrap_instance     = $wrap_instance;

        parent::__construct($escape_instance, $render_instance, $event_instance);
    }

    /**
     * Render Theme
     *
     * @param   string $include_file
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderTheme($include_file, $data)
    {
        /** Step 1. Initialise Rendering Data */
        $this->initialiseData($data);

        /** Step 2. Schedule onBeforeRender Event */
        $this->scheduleEvent('onBeforeRender');

        /** Step 3. Render Theme */
        $this->rendered_page = $this->theme_instance->renderOutput(
            $include_file,
            array('runtime_data' => $this->runtime_data)
        );

        /** Step 4. Return Rendered Page to Parser */
        return $this->rendered_page;
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
    public function renderPosition($token)
    {
        /** Step 1. Initialise */
        $position_name = $token->name;

        /** Step 2. Render Position */
        $this->rendered_view = $this->position_instance->getPositionTemplateViews(
            $position_name,
            $this->runtime_data->render->extension
        );

        return $this;
    }

    /**
     * Render Token
     *
     * @param   object $token
     * @param   string $rendered_page
     *
     * @return  string
     * @since   1.0
     */
    public function renderToken($token, $rendered_page)
    {
        $this->rendered_page = $rendered_page;

        /** Step 1. Get Rendering Extension */
        $token = $this->getInput($token);

        /** Step 2. Schedule Event */
        $this->scheduleEvent('onBeforeRenderView');

        /** Step 3. Render View */
        $this->renderView($token);

        /** Step 4. Schedule onAfterRenderView Event */
        $this->scheduleEvent('onAfterRenderView');

        /** Step 5. Replace Token with Rendered View */
        $this->replaceTokenWithRenderedOutput($token);

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
            throw new RuntimeException('Molajito Token Renderer requires Runtime Data');
        }

        if (isset($data['page_name'])) {
            $this->runtime_data->page_name = $data['page_name'];
        }

        if (isset($data['plugin_data'])) {
            $this->plugin_data = $data['plugin_data'];
        } else {
            $this->plugin_data = new stdClass();
        }

        return $this;
    }

    /**
     * Render View
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderView($token)
    {
        if (strtolower($this->runtime_data->render->scheme) === 'page') {
            $this->renderViewType('page_instance');

        } else {

            $this->renderViewType('template_instance');

            if ($token->wrap === '') {
            } else {
                $this->renderWrapView($token);
            }
        }

        return $this;
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
        $options = $this->getProperties();

        $this->rendered_view = $this->$type->renderOutput($this->include_path, $options);

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
        $this->getView($this->initializeWrapViewObject($token));

        $this->include_path = $this->runtime_data->render->extension->include_path;

        /** Step 2. Data */
        $options        = $this->getProperties();
        $row            = new stdClass();
        $row->title     = '';
        $row->subtitle  = '';
        $row->content   = $this->rendered_view;
        $options['row'] = $row;

        /** Step 3. Render Wrap */
        $this->rendered_view = $this->wrap_instance->renderOutput(
            $this->include_path,
            $options
        );

        return $this;
    }

    /**
     * Initialize Wrap View Object
     *
     * @param   object $token
     *
     * @return  stdClass
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function initializeWrapViewObject($token)
    {
        $wrap_token               = new stdClass();
        $wrap_token->type         = 'wrap';
        $wrap_token->name         = $token->wrap;
        $wrap_token->wrap         = '';
        $wrap_token->attributes   = $token->attributes;
        $wrap_token->replace_this = '';

        return $wrap_token;
    }

    /**
     * Get View for Token
     *
     * @param   object $token
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getInput($token)
    {
        /** Step 1. Get Rendering Extension */
        $token = $this->getInput($token);

        /** Step 2. Get Query Data for Rendering Extension */
        $this->getData($token);

        return $token;
    }

    /**
     * Get View for Token
     *
     * @param   stdClass $token
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getView($token)
    {
        $this->runtime_data->render = $this->view_instance->getView($token);

        if ($this->runtime_data->render->extension->title === $token->name) {
        } else {
            $token->name = $this->runtime_data->render->extension->title;
        }

        return $token;
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

        $data = $this->data_instance->getData($token, $options);

        $this->query_results  = $data->query_results;
        $this->model_registry = $data->model_registry;
        $this->parameters     = $data->parameters;
        $this->include_path   = $this->runtime_data->render->extension->include_path;

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
        $this->rendered_page
            = str_replace($token->replace_this, $this->rendered_view, $this->rendered_page);

        return $this;
    }
}
