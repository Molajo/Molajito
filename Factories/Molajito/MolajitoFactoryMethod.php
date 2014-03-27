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
use Molajito\ExtensionResource;
use Molajito\EventHandler;
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
        $this->dependencies['Language']      = array();
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
        $exclude_tokens     = $this->getExcludeTokens();
        $event_handler      = $this->getMolajitoEventHandlerInstance();
        $extension_resource = $this->getResourceExtensionInstance();

        $this->dependencies['Plugindata']->resource->extension = $extension_resource->getResourceExtension();
        $stop_loop_count                                       = $this->dependencies['Runtimedata']->reference_data->stop_loop_count;
        $theme_include_path                                    = $this->dependencies['Plugindata']->resource->extension->theme->include_path;
        $page_name                                             = $this->dependencies['Plugindata']->resource->extension->page->id;

        $rendering_properties                             = array();
        $rendering_properties['resource']                 = $this->dependencies['Resource'];
        $rendering_properties['fieldhandler']             = $this->dependencies['Fieldhandler'];
        $rendering_properties['date_controller']          = $this->dependencies['Date'];
        $rendering_properties['url_controller']           = $this->dependencies['Url'];
        $rendering_properties['language_controller']      = $this->dependencies['Language'];
        $rendering_properties['authorisation_controller'] = $this->dependencies['Authorisation'];

        $class = $this->product_namespace;

        try {
            $this->product_result = new $class (
                $exclude_tokens,
                $event_handler,
                $this->options['event_option_keys'],
                $extension_resource,
                $stop_loop_count,
                $theme_include_path,
                $page_name,
                $this->dependencies['Runtimedata'],
                $this->dependencies['Plugindata'],
                $rendering_properties
            );
        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito: Could not instantiate Class: ' . $class);
        }
    }

    /**
     * Get Exclude Tokens
     *
     * @return  object  Molajito\ExtensionResource
     * @since   1.0
     * @throws  FactoryInterface
     */
    protected function getExcludeTokens()
    {
        $exclude_tokens = array();
        $x              = $this->dependencies['Resource']
            ->get('xml:///Molajo//Model//Application//Parse_final.xml')->include;

        foreach ($x as $y) {
            $exclude_tokens[] = (string)$y;
        }

        return $exclude_tokens;
    }

    /**
     * Get Resource Extension Instance
     *
     * @return  object  Molajito\ExtensionResource
     * @since   1.0
     * @throws  FactoryInterface
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

/**
        echo '<pre>';
        var_dump(array(
        $theme_id,
        $page_view_id,
        $template_view_id,
        $wrap_view_id
        ));
        die;
*/
        return new ExtensionResource($this->dependencies['Resource'],
            $theme_id,
            $page_view_id,
            $template_view_id,
            $wrap_view_id
        );
    }

    /**
     * Get Event Handler Instance
     *
     * @return  object  Molajito\EventHandler
     * @since   1.0
     * @throws  FactoryInterface
     */
    protected function getMolajitoEventHandlerInstance()
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

        return new EventHandler(
            $this->dependencies['Eventcallback'],
            $this->options['event_option_keys']
        );
    }
}
