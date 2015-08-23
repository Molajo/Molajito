<?php
/**
 * Molajito Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Molajito;

use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;

/**
 * Molajito Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
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
     * @since   1.0.0
     */
    public function setDependencies(array $reflection = array())
    {
        $this->reflection   = array();
        $this->dependencies = array();

        $this->dependencies['Language']            = array();
        $this->dependencies['Resource']            = array();
        $this->dependencies['Fieldhandler']        = array();
        $this->dependencies['Eventcallback']       = array();
        $this->dependencies['Getcachecallback']    = array();
        $this->dependencies['Setcachecallback']    = array();
        $this->dependencies['Deletecachecallback'] = array();
        $this->dependencies['Runtimedata']         = array();
        $this->dependencies['Plugindata']          = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $options = array();

        foreach ($this->dependencies as $key => $value) {
            $options[$key] = $value;
        }

        $options['molajito_base_folder']  = $this->dependencies['Runtimedata']->base_path;
        $options['escape_class']          = 'molajo';
        $options['data_class']            = 'molajo';
        $options['data_options']          = array();
        $options['view_class']            = 'molajo';
        $options['runtime_data']          = $this->dependencies['Runtimedata'];
        $options['get_cache_callback']    = $this->dependencies['Getcachecallback'];
        $options['set_cache_callback']    = $this->dependencies['Setcachecallback'];
        $options['delete_cache_callback'] = $this->dependencies['Deletecachecallback'];

        $class   = 'Molajito\\Factory';
        $factory = new $class($options);

        $this->product_result = $factory->instantiateClass();

        return $this;
    }
}
