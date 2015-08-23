<?php
/**
 * Molajito Factory Method
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EscapeInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\ParseInterface;
use CommonApi\Render\TokenInterface;
use CommonApi\Language\TranslateInterface;

/**
 * Molajito Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class Factory
{
    /**
     * Options
     *
     * @var    array
     * @since  1.0.0
     */
    protected $options = array();

    /**
     * Constructor
     *
     * @param  array $options
     *
     * @since  1.0.0
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;

        if (isset($this->options['event_option_keys'])) {
        } else {
            $this->options['event_option_keys'] = array();
        }

        if (isset($this->options['exclude_tokens'])) {
        } else {
            $this->options['exclude_tokens'] = array('Messages');
        }

        if (isset($this->options['get_cache_callback'])) {
        } else {
            $this->options['get_cache_callback'] = null;
        }

        if (isset($this->options['set_cache_callback'])) {
        } else {
            $this->options['set_cache_callback'] = null;
        }

        if (isset($this->options['delete_cache_callback'])) {
        } else {
            $this->options['delete_cache_callback'] = null;
        }
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
        if (isset($this->options['molajito_base_folder'])
            && is_dir($this->options['molajito_base_folder'])
        ) {
        } else {
            throw new RuntimeException(
                'MolajitoFactoryMethod instantiateClass: Must provide options entry to molajito_base_folder.'
            );
        }

        $escape_instance = $this->getEscapeInstance();
        $render_instance = $this->getInstance('Molajito\\Render');
        $event_instance  = $this->getEventInstance();
        $parse_instance  = $this->getInstance('Molajito\\Parse');
        $exclude_tokens  = $this->options['exclude_tokens'];
        $stop_loop_count = 100;

        $render_array = $this->getRenderInstance(
            $render_instance,
            $escape_instance,
            $event_instance
        );

        $token_instance = $this->instantiateToken(
            $render_array
        );

        $translate_instance = $this->getTranslateInstance($escape_instance);

        return $this->instantiateEngineClass(
            $token_instance,
            $translate_instance,
            $event_instance,
            $parse_instance,
            $exclude_tokens,
            $stop_loop_count
        );
    }

    /**
     * Instantiate Engine Class
     *
     * @param  TokenInterface     $token_instance
     * @param  TranslateInterface $translate_instance
     * @param  EventInterface     $event_instance
     * @param  ParseInterface     $parse_instance
     * @param  array              $exclude_tokens
     * @param  integer            $stop_loop_count
     *
     * @return  $this
     * @since   1.0.0
     */
    public function instantiateEngineClass(
        $token_instance,
        $translate_instance,
        $event_instance,
        $parse_instance,
        $exclude_tokens,
        $stop_loop_count
    ) {
        $class = 'Molajito\\Engine';

        return new $class (
            $token_instance, $translate_instance, $event_instance, $parse_instance, $exclude_tokens, $stop_loop_count
        );
    }

    /**
     * Instantiate Render Token Class
     *
     * @param  array $render_array
     *
     * @return  $this
     * @since   1.0.0
     */
    public function instantiateToken(
        array $render_array = array()
    ) {
        $class = 'Molajito\\Token';

        return new $class (
            $render_array
        );
    }

    /**
     * Instantiate Escape Class with Adapter
     *
     * @return  object
     * @since   1.0.0
     */
    protected function getEscapeInstance()
    {
        $class = 'simple';

        if (isset($this->options['escape_class'])) {
            $class = strtolower($this->options['escape_class']);
        }

        if ($class === 'molajo'
            && file_exists($this->options['molajito_base_folder'] . '/vendor/molajo/Fieldhandler/Source/Request.php')
        ) {
            $fieldhandler = $this->getInstance('Molajo\\Fieldhandler\\Request', array());
            $adapter      = $this->getInstance('Molajito\\Escape\\Molajo', $fieldhandler);
        } else {
            $adapter = $this->getInstance('Molajito\\Escape\\Simple');
        }

        return $this->getInstance('Molajito\\Escape', $adapter);
    }

    /**
     * Instantiate Event Class with Adapter
     *
     * @return  EventInterface
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getEventInstance()
    {
        $class = 'Molajito\\Event\\Dummy';
        if (file_exists($this->options['molajito_base_folder'] . '/vendor/molajo/event/Source/Scheduled.php')) {
            $class = 'Molajito\\Event\\Molajo';
        }

        $adapter = $this->getInstance(
            $class,
            $this->options['Eventcallback'],
            $this->options['event_option_keys']
        );

        return $this->getInstance('Molajito\\Event', $adapter);
    }

    /**
     * Instantiate Theme and View Render Classes
     *
     * @param   object               $render_instance
     * @param   null|EscapeInterface $escape_instance
     * @param   null|EventInterface  $event_instance
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getRenderInstance(
        $render_instance,
        EscapeInterface $escape_instance = null,
        EventInterface $event_instance = null
    ) {
        $class_array             = array();
        $class_array['theme']    = 'Molajito\\Render\\Theme';
        $class_array['position'] = 'Molajito\\Render\\Position';
        $class_array['page']     = 'Molajito\\Render\\PageView';
        $class_array['template'] = 'Molajito\\Render\\TemplateView';
        $class_array['wrap']     = 'Molajito\\Render\\WrapView';

        $new_array = array();

        foreach ($class_array as $key => $class) {
            $instance        = new $class (
                $escape_instance,
                $render_instance,
                $event_instance,
                $this->options['runtime_data'],
                $this->options['get_cache_callback'],
                $this->options['set_cache_callback'],
                $this->options['delete_cache_callback']
            );
            $new_array[$key] = $instance;
        }

        return $new_array;
    }

    /**
     * Instantiate Translate Class with Adapter
     *
     * @param   $escape_instance \CommonApi\Render\EscapeInterface
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getTranslateInstance(EscapeInterface $escape_instance)
    {
        if (isset($this->options['language_strings'])) {
            $language = $this->options['language_strings'];
            $class    = 'Molajito\\Translate\\StringArrayAdapter';
        } else {
            $language = $this->options['Language'];
            $class    = 'Molajito\\Translate\\MolajoLanguageAdapter';
        }

        try {
            $adapter = new $class ($escape_instance, $parse_mask = null, $model_registry = array(), $language);
        } catch (Exception $e) {
            throw new RuntimeException(
                'MolajitoFactoryMethod getTranslateInstance: Could not instantiate Translate Adapter: ' . $class
            );
        }

        return $this->getInstance('Molajito\\Translate', $adapter);
    }

    /**
     * Get Instance of Class with no Constructor Parameters
     *
     * @param   string      $class
     * @param   null|object $property1
     * @param   null|object $property2
     *
     * @return  object
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getInstance($class, $property1 = null, $property2 = null)
    {
        try {

            if ($property1 === null && $property2 === null) {
                return new $class ();
            }

            if ($property2 === null) {
                return new $class ($property1);
            }

            return new $class ($property1, $property2);

        } catch (Exception $e) {
            throw new RuntimeException(
                'MolajitoFactoryMethod getInstance: Could not instantiate class: ' . $class
            );
        }
    }
}
