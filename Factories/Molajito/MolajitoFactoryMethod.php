<?php
/**
 * Molajito Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Molajito;

use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethodBase;
use stdClass;

/**
 * Molajito Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class MolajitoFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
{
    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0.0
     */
    public function __construct(array $options = array())
    {
        $options['product_name']      = basename(__DIR__);
        $options['product_namespace'] = 'Molajito\\Engine';

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

        $this->dependencies['Language']      = array();
        $this->dependencies['Resource']      = array();
        $this->dependencies['Fieldhandler']  = array();
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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $options = array();

        foreach ($this->dependencies as $key => $value) {
            $options[ $key ] = $value;
        }

        $options['molajito_base_folder'] = $this->dependencies['Runtimedata']->base_path;
        $options['escape_class']         = 'molajo';
        $options['data_class']           = 'molajo';
        $options['data_options']         = array();
        $options['view_class']           = 'molajo';
        $class                           = 'Molajito\\FactoryMethod';
        $factory                         = new $class($options);
        $this->product_result            = $factory->instantiateClass();
        $this->options['view_instance']  = $factory->getSavedViewInstance();

        return $this;
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

        } elseif (isset($this->dependencies['Runtimedata']->resource->menuitem->parameters)) {

            $theme_id         = $this->dependencies['Runtimedata']->resource->menuitem->parameters->theme_id;
            $page_view_id     = $this->dependencies['Runtimedata']->resource->menuitem->parameters->page_view_id;
            $template_view_id = $this->dependencies['Runtimedata']->resource->menuitem->parameters->template_view_id;
            $wrap_view_id     = $this->dependencies['Runtimedata']->resource->menuitem->parameters->wrap_view_id;

        } else {
            $theme_id         = $this->dependencies['Runtimedata']->resource->parameters->theme_id;
            $page_view_id     = $this->dependencies['Runtimedata']->resource->parameters->page_view_id;
            $template_view_id = $this->dependencies['Runtimedata']->resource->parameters->template_view_id;
            $wrap_view_id     = $this->dependencies['Runtimedata']->resource->parameters->wrap_view_id;
        }

        $this->dependencies['Runtimedata']->resource->extensions = new stdClass();

        /** Get Theme */
        $token               = new stdClass();
        $token->type         = 'theme';
        $token->name         = $theme_id;
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '';

        $this->dependencies['Runtimedata']->resource->extensions->theme
            = $this->options['view_instance']->getView($token);

        /** Get Page */
        $token               = new stdClass();
        $token->type         = 'page';
        $token->name         = $page_view_id;
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '';

        $this->dependencies['Runtimedata']->resource->extensions->page
            = $this->options['view_instance']->getView($token);

        /** Get Template */
        $token               = new stdClass();
        $token->type         = 'template';
        $token->name         = $template_view_id;
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '';

        $this->dependencies['Runtimedata']->resource->extensions->template
            = $this->options['view_instance']->getView($token);

        /** Get Template */
        $token               = new stdClass();
        $token->type         = 'wrap';
        $token->name         = $wrap_view_id;
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '';

        $this->dependencies['Runtimedata']->resource->extensions->wrap
            = $this->options['view_instance']->getView($token);

        $this->set_container_entries['Runtimedata'] = $this->dependencies['Runtimedata'];

        return $this;
    }
}
