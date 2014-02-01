<?php
/**
 * Pagination Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Molajito;

use Exception;
use CommonApi\IoC\ServiceProviderInterface;
use CommonApi\Exception\RuntimeException;
use Molajo\IoC\AbstractServiceProvider;
use Molajito\ExtensionResource;
use Molajito\EventHandler;

/**
 * Pagination Service Provider
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
        $options['service_name']      = basename(__DIR__);
        $options['service_namespace'] = 'Pagination\\Pagination';

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
        $this->options['event_option_keys'] = array(
            'runtime_data',
            'parameters',
            'model_registry',
            'query_results',
            'row',
            'include_path',
            'rendered_view',
            'rendered_page'
        );

        $extension_resource                                     = $this->getResourceExtensionInstance();
        $this->dependencies['Runtimedata']->resource->extension = $extension_resource->getResource();

        $rendering_properties                             = array();
        $rendering_properties['resource']                 = $this->dependencies['Resource'];
        $rendering_properties['fieldhandler']             = $this->dependencies['Fieldhandler'];
        $rendering_properties['date_controller']          = $this->dependencies['Date'];
        $rendering_properties['url_controller']           = $this->dependencies['Url'];
        $rendering_properties['language_controller']      = $this->dependencies['Language'];
        $rendering_properties['authorisation_controller'] = $this->dependencies['Authorisation'];

        $class = $this->service_namespace;

        try {
            return new $class (
                $this->getExcludeTokens(),
                $this->getMolajitoEventHandlerInstance(),
                $extension_resource,
                $this->dependencies['Runtimedata']->reference_data->stop_loop_count,
                $this->dependencies['Runtimedata']->resource->theme->include_path,
                $this->dependencies['Runtimedata']->resource->page->id,
                $this->dependencies['Runtimedata'],
                $rendering_properties
            );
        } catch (Exception $e) {
            throw new RuntimeException
            ('Pagination: Could not instantiate Class: ' . $class);
        }
    }

    /**
     * Get Exclude Tokens
     *
     * @return  object  Pagination\ExtensionResource
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
     * @return  object  Pagination\ExtensionResource
     * @since   1.0
     * @throws  ServiceProviderInterface
     */
    protected function getResourceExtensionInstance()
    {
        return new ExtensionResource(
            $this->dependencies['Resource'],
            $this->dependencies['Runtimedata']->resource->parameters->theme_id,
            $this->dependencies['Runtimedata']->resource->parameters->page_view_id,
            $this->dependencies['Runtimedata']->resource->parameters->template_view_id,
            $this->dependencies['Runtimedata']->resource->parameters->wrap_view_id
        );
    }

    /**
     * Get Event Handler Instance
     *
     * @return  object  Pagination\EventHandler
     * @since   1.0
     * @throws  ServiceProviderInterface
     */
    protected function getMolajitoEventHandlerInstance()
    {
        return new EventHandler(
            $this->dependencies['Eventcallback'],
            $this->options['event_option_keys']
        );
    }
}
