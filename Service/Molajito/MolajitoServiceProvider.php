<?php
/**
 * Molajito Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Molajito;

use CommonApi\IoC\ServiceProviderInterface;
use CommonApi\Exception\RuntimeException;
use Exception;
use Molajito\ExtensionResource;
use Molajito\EventHandler;
use Molajo\IoC\AbstractServiceProvider;

/**
 * Molajito Service Provider
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class MolajitoServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
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
        $options['service_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['service_namespace']        = 'Molajito\\Molajito';

        parent::__construct($options);
    }

    /**
     * Service Provider can use this method to define Service Dependencies
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
    public function instantiateService()
    {
        $exclude_tokens                                         = $this->getExcludeTokens();
        $event_handler                                          = $this->getMolajitoEventHandlerInstance();
        $extension_resource                                     = $this->getResourceExtensionInstance();
        $this->dependencies['Plugindata']->resource->extension = $extension_resource->getResourceExtension();
        $stop_loop_count                                        = $this->dependencies['Runtimedata']->reference_data->stop_loop_count;
        $theme_include_path                                     = $this->dependencies['Plugindata']->resource->extension->theme->include_path;
        $page_name                                              = $this->dependencies['Plugindata']->resource->extension->page->id;

        $rendering_properties                             = array();
        $rendering_properties['resource']                 = $this->dependencies['Resource'];
        $rendering_properties['fieldhandler']             = $this->dependencies['Fieldhandler'];
        $rendering_properties['date_controller']          = $this->dependencies['Date'];
        $rendering_properties['url_controller']           = $this->dependencies['Url'];
        $rendering_properties['language_controller']      = $this->dependencies['Language'];
        $rendering_properties['authorisation_controller'] = $this->dependencies['Authorisation'];

        $class = $this->service_namespace;

        try {
            $this->service_instance = new $class (
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
     * @throws  ServiceProviderInterface
     */
    protected function getExcludeTokens()
    {
        $exclude_tokens = array();
        $x              = $this->dependencies['Resource']
            ->get('xml:///Molajo//Application//Parse_final.xml')->include;

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
     * @throws  ServiceProviderInterface
     */
    protected function getResourceExtensionInstance()
    {
        return new ExtensionResource(
            $this->dependencies['Resource'],
            $this->dependencies['Plugindata']->resource->parameters->theme_id,
            $this->dependencies['Plugindata']->resource->parameters->page_view_id,
            $this->dependencies['Plugindata']->resource->parameters->template_view_id,
            $this->dependencies['Plugindata']->resource->parameters->wrap_view_id
        );
    }

    /**
     * Get Event Handler Instance
     *
     * @return  object  Molajito\EventHandler
     * @since   1.0
     * @throws  ServiceProviderInterface
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
