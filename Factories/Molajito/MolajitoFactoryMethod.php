<?php
/**
 * Molajito Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Molajito;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use CommonApi\Render\EscapeInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\RenderInterface;
use Molajo\IoC\FactoryMethodBase;
use stdClass;

/**
 * Molajito Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class MolajitoFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
{
    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['product_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['product_namespace']        = 'Molajito\\Engine';

        parent::__construct($options);
    }

    /**
     * Factory Method can use this method to define Service Dependencies
     *  or use the Service Dependencies automatically defined by Reflection processes
     *
     * @param   array $reflection
     *
     * @return  array
     * @since   1.0
     */
    public function setDependencies(array $reflection = null)
    {
        $this->reflection   = array();
        $this->dependencies = array();

        $this->dependencies['Resource']      = array();
        $this->dependencies['Fieldhandler']  = array();
        $this->dependencies['Date']          = array();
        $this->dependencies['Url']           = array();
        $this->dependencies['Authorisation'] = array();
        $this->dependencies['Runtimedata']   = array();
        $this->dependencies['Plugindata']    = array();
        $this->dependencies['Eventcallback'] = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateClass()
    {
        $escape_instance   = $this->getEscapeInstance();
        $render_instance   = $this->getRenderInstance();
        $data_instance     = $this->getDataInstance();
        $view_instance     = $this->getViewInstance();
        $event_instance    = $this->getEventInstance();
        $parse_instance    = $this->getParseInstance();
        $exclude_tokens    = $this->getExcludeTokens();
        $stop_loop_count   = $this->dependencies['Runtimedata']->reference_data->stop_loop_count;
        $position_instance = $this->getPositionInstance($escape_instance);
        $theme_instance    = $this->getThemeInstance($escape_instance, $render_instance);
        $page_instance     = $this->getPageInstance($render_instance);
        $template_instance = $this->getTemplateInstance(
            $escape_instance,
            $render_instance,
            $event_instance,
            $this->options['event_option_keys']
        );
        $wrap_instance     = $this->getWrapInstance($render_instance);

        $class = $this->product_namespace;

        try {
            $this->product_result = new $class (
                $data_instance,
                $view_instance,
                $event_instance,
                $this->options['event_option_keys'],
                $parse_instance,
                $exclude_tokens,
                $stop_loop_count,
                $position_instance,
                $theme_instance,
                $page_instance,
                $template_instance,
                $wrap_instance
            );
        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Driver Class: ' . $class);
        }
    }

    /**
     * Set Extension Data for Resource
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterInstantiation()
    {
        $this->setResourceExtensions();

        return $this;
    }

    /**
     * Instantiate Escape Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getEscapeInstance()
    {
        $class = 'Molajito\\Escape';

        try {
            return new $class ($this->dependencies['Fieldhandler']);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Escape Class: ' . $class);
        }
    }

    /**
     * Instantiate Render Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getRenderInstance()
    {
        $class = 'Molajito\\Render';

        try {
            return new $class ();

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Render Class: ' . $class);
        }
    }

    /**
     * Get Resource Data Instance -- used to retrieve data needed to render view
     *
     * @return  object  Molajito\View
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getDataInstance()
    {
        $class = 'Molajito\\Data\\Molajo';

        try {
            $adapter = new $class ();

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Data Adapter Class: ' . $class);
        }

        $class = 'Molajito\\Data';

        try {
            return new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Data Class: ' . $class);
        }
    }

    /**
     * Get Resource Extension Instance - used to retrieve View location and parameters
     *
     * @return  object  Molajito\View
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getViewInstance()
    {
        $class = 'Molajito\\View\\Molajo';

        try {
            $adapter = new $class(
                $this->dependencies['Resource']
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Extension Class: ' . $class);
        }

        /** Proxy */
        $class = 'Molajito\\View';

        try {
            $view_instance = new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Data Class: ' . $class);
        }

        $this->options['view_instance'] = $view_instance;

        return $view_instance;
    }

    /**
     * Get Event Handler Instance
     *
     * @return  object  Molajito\Event
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getEventInstance()
    {
        $this->options['event_option_keys'] = array(
            'runtime_data',
            'plugin_data',
            'parameters',
            'model_registry',
            'query_results',
            'row',
            'include_path',
            'rendered_view',
            'rendered_page'
        );

        $class = 'Molajito\\Event\\Molajo';

        try {
            $adapter = new $class(
                $this->dependencies['Eventcallback'],
                $this->options['event_option_keys']
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Event Class: ' . $class);
        }

        /** Proxy */
        $class = 'Molajito\\Event';

        try {
            return new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Data Class: ' . $class);
        }
    }

    /**
     * Instantiate Parse Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getParseInstance()
    {
        $class = 'Molajito\\Parse';

        try {
            return new $class ();

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Parse Class: ' . $class);
        }
    }

    /**
     * Get Exclude Tokens
     *
     * @return  object  Molajito\View
     * @since   1.0
     * @throws  FactoryInterface
     */
    protected function getExcludeTokens()
    {
        $x = (array)$this->dependencies['Resource']
            ->get('xml:///Molajo//Model//Application//Parse_final.xml')->include;

        $exclude_tokens = array();

        if (is_array($x) && count($x) > 0) {

            foreach ($x as $y) {
                $exclude_tokens[] = (string)$y;
            }
        }

        return $exclude_tokens;
    }

    /**
     * Instantiate Position Renderer Class
     *
     * @param   EscapeInterface $escape_instance
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getPositionInstance(EscapeInterface $escape_instance)
    {
        $class = 'Molajito\\Position';

        try {
            return new $class ($escape_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Position Renderer Class: ' . $class);
        }
    }

    /**
     * Instantiate Theme Renderer Class
     *
     * @param   EscapeInterface $escape_instance
     * @param   RenderInterface $render_instance
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getThemeInstance(EscapeInterface $escape_instance, RenderInterface $render_instance)
    {
        $class = 'Molajito\\Theme';

        try {
            return new $class ($escape_instance, $render_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Theme Renderer Class: ' . $class);
        }
    }

    /**
     * Instantiate Page View Renderer Class
     *
     * @param   RenderInterface $render_instance
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getPageInstance(RenderInterface $render_instance)
    {
        $class = 'Molajito\\PageView';

        try {
            return new $class ($render_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Page View Renderer Class: ' . $class);
        }
    }

    /**
     * Instantiate Template View Renderer Class
     *
     * @param   EscapeInterface $escape_instance
     * @param   RenderInterface $render_instance
     * @param   EventInterface  $escape_instance
     * @param   array           $event_option_keys
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getTemplateInstance(
        EscapeInterface $escape_instance,
        RenderInterface $render_instance,
        EventInterface $event_instance,
        array $event_option_keys = array()
    ) {
        $class = 'Molajito\\TemplateView';

        try {
            return new $class (
                $escape_instance,
                $render_instance,
                $event_instance,
                $event_option_keys
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Template View Renderer Class: ' . $class);
        }
    }

    /**
     * Instantiate Wrap View Renderer Class
     *
     * @param   RenderInterface $render_instance
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getWrapInstance(RenderInterface $render_instance)
    {
        $class = 'Molajito\\WrapView';

        try {
            return new $class ($render_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Wrap View Renderer Class: ' . $class);
        }
    }

    /**
     * Save View Data for Resource
     *
     * @return  $this
     * @since   1.0
     */
    protected function setResourceExtensions()
    {
        $page_type = strtolower($this->dependencies['Runtimedata']->route->page_type);

        if ($page_type == 'dashboard') {
            $theme_id         = 7010;
            $page_view_id     = 8265;
            $template_view_id = 9305;
            $wrap_view_id     = 10010;

        } elseif (isset($this->dependencies['Plugindata']->resource->menuitem->parameters)) {
            $theme_id         = $this->dependencies['Plugindata']->resource->menuitem->parameters->theme_id;
            $page_view_id     = $this->dependencies['Plugindata']->resource->menuitem->parameters->page_view_id;
            $template_view_id = $this->dependencies['Plugindata']->resource->menuitem->parameters->template_view_id;
            $wrap_view_id     = $this->dependencies['Plugindata']->resource->menuitem->parameters->wrap_view_id;

        } else {
            $theme_id         = $this->dependencies['Plugindata']->resource->parameters->theme_id;
            $page_view_id     = $this->dependencies['Plugindata']->resource->parameters->page_view_id;
            $template_view_id = $this->dependencies['Plugindata']->resource->parameters->template_view_id;
            $wrap_view_id     = $this->dependencies['Plugindata']->resource->parameters->wrap_view_id;
        }

        $resource_extensions                                    = new stdClass();
        $this->dependencies['Plugindata']->resource->extensions = new stdClass();

        /** Get Theme */
        $token               = new stdClass();
        $token->type         = 'theme';
        $token->name         = $theme_id;
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '';

        $this->dependencies['Plugindata']->resource->extensions->theme
            = $this->options['view_instance']->getView($token);

        /** Get Page */
        $token               = new stdClass();
        $token->type         = 'page';
        $token->name         = $page_view_id;
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '';

        $this->dependencies['Plugindata']->resource->extensions->page
            = $this->options['view_instance']->getView($token);

        /** Get Template */
        $token               = new stdClass();
        $token->type         = 'template';
        $token->name         = $template_view_id;
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '';

        $this->dependencies['Plugindata']->resource->extensions->template
            = $this->options['view_instance']->getView($token);

        /** Get Template */
        $token               = new stdClass();
        $token->type         = 'wrap';
        $token->name         = $wrap_view_id;
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '';

        $this->dependencies['Plugindata']->resource->extensions->wrap
            = $this->options['view_instance']->getView($token);

        $this->set_container_entries['Plugindata'] = $this->dependencies['Plugindata'];

        return $this;
    }
}
