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
use Exception;
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
    protected $data_instance = NULL;

    /**
     * Retrieve View information for rendering
     *
     * @var    object  CommonApi\Render\ViewInterface
     * @since  1.0.0
     */
    protected $view_instance = NULL;

    /**
     * Theme Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $theme_instance = NULL;

    /**
     * Position Instance
     *
     * @var    object  CommonApi\Render\PositionInterface
     * @since  1.0.0
     */
    protected $position_instance = NULL;

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
     * Theme Path
     *
     * @var    string
     * @since  1.0.0
     */
    protected $theme_path = NULL;

    /**
     * Render Properties
     *
     * @var    array
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
    protected function renderTheme($include_file, $data)
    {
        /** Step 1. Initialise Rendering Data */
        $this->initialiseData($data);

        /** Step 2. Schedule onBeforeRender Event */
        $this->scheduleEvent('onBeforeRender');

        /** Step 3. Render Theme */
        try {
            $this->rendered_page = $this->theme_instance->render(
                $include_file,
                array('runtime_data' => $this->runtime_data)
            );

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito Token Renderer renderTheme Method Failed for '
                . ' Theme: ' . $include_file . ' ' . $e->getMessage()
            );
        }

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
            throw new RuntimeException ('Molajito Token Renderer requires Runtime Data');
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
        $this->include_path   = NULL;

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
    protected function renderToken($token, $rendered_page)
    {
//echo '<br><br>View:  ' . $token->name . '<br>';
//echo '<pre>';
//var_dump($token);
//echo '<pre>';

        $this->rendered_page = $rendered_page;

        /** Step 1. Get Rendering Extension */
        $this->getView($token);

        if ($this->runtime_data->render->extension->title === $token->name) {
        } else {
            $token->name = $this->runtime_data->render->extension->title;
        }

        /** Step 2. Get Query Data for Rendering Extension */
        $this->getData($token);

        $this->include_path = $this->runtime_data->render->extension->include_path;

        /** Step 3. Schedule Event */
        $this->scheduleEvent('onBeforeRenderView');

        /** Step 4. Render View */
        if (strtolower($this->runtime_data->render->scheme) === 'page') {
            $this->renderPageView();

        } else {

            $this->renderTemplateView();

            if ($token->wrap === '') {

            } else {
                $this->renderWrapView($token);
            }
        }

        /** Step 5. Schedule onAfterRenderView Event */
        $this->scheduleEvent('onAfterRenderView');

        /** Step 6. Replace Token with Rendered View */
        $this->replaceTokenWithRenderedOutput($token);

        return $this->rendered_page;
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
        $options        = $this->getProperties();
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
            (
                'Molajito Driver renderObject Method Failed. Type: Wrap '
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
}
