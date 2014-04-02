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
        $options['product_namespace']        = 'Molajito\\Driver';

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
        $parse_instance     = $this->getMolajitoParseClass();
        $exclude_tokens     = $this->getExcludeTokens();
        $stop_loop_count    = $this->dependencies['Runtimedata']->reference_data->stop_loop_count;
        $event_instance     = $this->getMolajitoEventInstance();
        $extension_instance = $this->getResourceExtensionInstance();
        $data_instance      = $this->getResourceDataInstance();
        $escape_instance    = $this->getMolajitoEscapeClass();
        $render_instance    = $this->getMolajitoRenderClass();
        $position_instance  = $this->getMolajitoPositionClass($escape_instance);
        $theme_instance     = $this->getMolajitoThemeClass($escape_instance, $render_instance);
        $page_instance      = $this->getMolajitoPageClass($render_instance);
        $template_instance  = $this->getMolajitoTemplateClass(
            $escape_instance,
            $render_instance,
            $event_instance,
            $this->options['event_option_keys']
        );
        $wrap_instance      = $this->getMolajitoWrapClass($render_instance);
        $theme_path         = $this->dependencies['Plugindata']->resource->extension->theme->include_path;
        $page_name          = $this->dependencies['Plugindata']->resource->extension->page->id;

        $class = $this->product_namespace;

        try {
            $this->product_result = new $class (
                $parse_instance,
                $exclude_tokens,
                $stop_loop_count,
                $event_instance,
                $this->options['event_option_keys'],
                $extension_instance,
                $data_instance,
                $position_instance,
                $theme_instance,
                $page_instance,
                $template_instance,
                $wrap_instance,
                $theme_path,
                $page_name,
                $this->dependencies['Runtimedata'],
                $this->dependencies['Plugindata']
            );
        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Driver Class: ' . $class);
        }
    }

    /**
     * Get Exclude Tokens
     *
     * @return  object  Molajito\Extension
     * @since   1.0
     * @throws  FactoryInterface
     */
    protected function getExcludeTokens()
    {
        $x = $this->dependencies['Resource']->get('xml:///Molajo//Model//Application//Parse_final.xml')->include;

        $exclude_tokens = array();

        if (is_array($x) && count($x) > 0) {
            foreach ($x as $y) {
                $exclude_tokens[] = (string)$y;
            }
        }

        return $exclude_tokens;
    }

    /**
     * Get Resource Extension Instance - used to retrieve View location and parameters
     *
     * @return  object  Molajito\Extension
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getResourceExtensionInstance()
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

        $options                  = array();
        $options['theme']         = $theme_id;
        $options['page_view']     = $page_view_id;
        $options['template_view'] = $template_view_id;
        $options['wrap_view']     = $wrap_view_id;

        /** Adapter */
        $class = 'Molajito\\Extension\\Molajo';

        try {
            $adapter = new $class(
                $this->dependencies['Resource']
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Extension Class: ' . $class);
        }

        /** Proxy */
        $class = 'Molajito\\Extension';

        try {
            $extension_instance = new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Data Class: ' . $class);
        }

        /** Get Resource Extension */
        $this->getResourceExtension($extension_instance, $options);

        return $extension_instance;
    }

    /**
     * Set Extension Data for Resource
     *
     * @param   object $extension_instance
     * @param   array  $options
     *
     * @return  $this
     * @since   1.0
     */
    protected function getResourceExtension($extension_instance, array $options = array())
    {
        $this->dependencies['Plugindata']->resource->extension
            = $extension_instance->getResourceExtension($options);

        return $this;
    }

    /**
     * Get Resource Data Instance -- used to retrieve data needed to render view
     *
     * @return  object  Molajito\Extension
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getResourceDataInstance()
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
     * Get Event Handler Instance
     *
     * @return  object  Molajito\Event
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getMolajitoEventInstance()
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

        $class = 'Molajito\\Event';

        try {
            return new $class(
                $this->dependencies['Eventcallback'],
                $this->options['event_option_keys']
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Event Class: ' . $class);
        }
    }

    /**
     * Instantiate Parse Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getMolajitoParseClass()
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
     * Instantiate Escape Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getMolajitoEscapeClass()
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
    protected function getMolajitoRenderClass()
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
     * Instantiate Position Renderer Class
     *
     * @param   EscapeInterface $escape_instance
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getMolajitoPositionClass(EscapeInterface $escape_instance)
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
    protected function getMolajitoThemeClass(EscapeInterface $escape_instance, RenderInterface $render_instance)
    {
        $class = 'Molajito\\ThemeRenderer';

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
    protected function getMolajitoPageClass(RenderInterface $render_instance)
    {
        $class = 'Molajito\\PageViewRenderer';

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
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getMolajitoTemplateClass(
        EscapeInterface $escape_instance,
        RenderInterface $render_instance,
        EventInterface $event_instance,
        array $event_option_keys = array()
    ) {
        $class = 'Molajito\\TemplateViewRenderer';

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
    protected function getMolajitoWrapClass(RenderInterface $render_instance)
    {
        $class = 'Molajito\\WrapViewRenderer';

        try {
            return new $class ($render_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Wrap View Renderer Class: ' . $class);
        }
    }
}
