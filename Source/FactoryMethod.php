<?php
/**
 * Molajito Factory Method
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EscapeInterface;
use CommonApi\Render\EventInterface;
use CommonApi\Render\DataInterface;
use CommonApi\Render\ParseInterface;
use CommonApi\Render\PositionInterface;
use CommonApi\Render\RenderInterface;
use CommonApi\Language\TranslateInterface;
use CommonApi\Render\ViewInterface;

/**
 * Molajito Factory Method
 *
 * Isolates the complexity of dependency injection so that using the
 * package in multiple environments is a matter of setting parameters and
 * allowing this process to handle class construction.
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class FactoryMethod
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
            $this->options['exclude_tokens'] = array();
        }
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
        if (isset($this->options['molajito_base_folder'])
            && is_dir($this->options['molajito_base_folder'])
        ) {
        } else {
            throw new RuntimeException(
                'MolajitoFactoryMethod instantiateClass: Must provide options entry to molajito_base_folder.'
            );
        }

        $escape_instance = $this->getEscapeInstance();
        $render_instance = $this->getInstance('Molajo\\Render');
        $data_instance   = $this->getDataInstance();
        $view_instance   = $this->getViewInstance();
        $event_instance  = $this->getEventInstance();
        $parse_instance  = $this->getInstance('Molajo\\Parse');
        $exclude_tokens  = $this->options['exclude_tokens'];
        $stop_loop_count = 100;

        $views = $this->getRenderInstance($render_instance, $escape_instance, $event_instance);

        $token_instance = $this->instantiateRenderToken(
            $escape_instance,
            $render_instance,
            $event_instance,
            $data_instance,
            $view_instance,
            $views[0],
            $views[1],
            $views[2],
            $views[3],
            $views[4]
        );

        $translate_instance = $this->getTranslateInstance($escape_instance);

        return $this->instantiateEngineClass(
            $token_instance,
            $translate_instance,
            $parse_instance,
            $exclude_tokens,
            $stop_loop_count
        );
    }

    /**
     * Instantiate Engine Class
     *
     * @param  FactoryMethod             $token_instance
     * @param  TranslateInterface $translate_instance
     * @param  ParseInterface     $parse_instance
     * @param  array              $exclude_tokens
     * @param  integer            $stop_loop_count
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateEngineClass(
        $token_instance,
        $translate_instance,
        $parse_instance,
        $exclude_tokens,
        $stop_loop_count
    ) {
        $class = 'Molajito\\Render\\Token';

        return new $class ($token_instance, $translate_instance, $parse_instance, $exclude_tokens, $stop_loop_count);
    }

    /**
     * Instantiate Render Token Class
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
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateRenderToken(
        $escape_instance,
        $render_instance,
        $event_instance,
        $data_instance,
        $view_instance,
        $theme_instance,
        $position_instance,
        $page_instance,
        $template_instance,
        $wrap_instance
    ) {
        $class = 'Molajito\\Render\\Token';
        return new $class (
            $escape_instance, $render_instance, $event_instance,
            $data_instance, $view_instance,
            $theme_instance, $position_instance, $page_instance, $template_instance, $wrap_instance
        );
    }

    /**
     * Instantiate Escape Class with Adapter
     *
     * @return  \CommonApi\Render\EscapeInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getEscapeInstance()
    {
        $class = 'simple';

        if (isset($this->options['escape_class'])) {
            $class = strtolower($this->options['escape_class']);
        }

        if ($class === 'molajo'
            && file_exists($this->options['molajito_base_folder'] . '/vendor/molajo/Fieldhandler/Source/Driver.php')
        ) {
            $fieldhandler = $this->getInstance('Molajo\\Fieldhandler\\Driver');
            $adapter      = $this->getInstance('Molajito\\Escape\\Molajo', $fieldhandler);
        } else {
            $adapter = $this->getInstance('Molajito\\Escape\\Simple');
        }

        return $this->getInstance('Molajito\\Escape', $adapter);
    }

    /**
     * Instantiate Data Class with Adapter
     *
     * @return  \CommonApi\Render\DataInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getDataInstance()
    {
        if (file_exists($this->options['molajito_base_folder'] . '/vendor/molajo/pagination/Source/Pagination.php')) {
            $pagination = $this->getInstance('Molajo\\Pagination');
        } else {
            $pagination = null;
        }

        $class = 'Molajo';

        if (isset($this->options['data_class'])) {
            $class = $this->options['data_class'];
        }

        $adapter = $this->getInstance('Molajito\\Data\\' . ucfirst(strtolower($class)), $pagination);

        return $this->getInstance('Molajito\\Data', $adapter);
    }

    /**
     * Instantiate View Class with Adapter
     *
     * @return  \CommonApi\Render\ViewInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getViewInstance()
    {
        $class = 'Molajo';

        if (isset($this->options['view_class'])) {
            $class = $this->options['view_class'];
        }

        if (strtolower($class) === 'filesystem') {
            $adapter = $this->getInstance(
                'Molajito\\View\\Filesystem',
                $this->options['theme_base_folder'],
                $this->options['view_base_folder']
            );
        } else {
            $adapter = $this->getInstance(
                'Molajito\\View\\' . ucfirst(strtolower($class)),
                $this->options['Resource']
            );
        }

        $this->options['view_instance'] = $this->getInstance('Molajito\\View', $adapter);

        return $this->options['view_instance'];
    }

    /**
     * Instantiate Event Class with Adapter
     *
     * @return  EventInterface
     * @since   1.0
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
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getRenderInstance(
        $render_instance,
        EscapeInterface $escape_instance = null,
        EventInterface $event_instance = null
    ) {

        $class_array   = array();
        $class_array[] = 'Molajito\\Render\\Theme';
        $class_array[] = 'Molajito\\Render\\Position';
        $class_array[] = 'Molajito\\Render\\Page';
        $class_array[] = 'Molajito\\Render\\Template';
        $class_array[] = 'Molajito\\Render\\Wrap';

        $new_array = array();

        foreach ($class_array as $class) {
            $new_array[] = new $class ($escape_instance, $render_instance, $event_instance);
        }

        return $new_array;
    }

    /**
     * Instantiate Translate Class with Adapter
     *
     * @param   $escape_instance \CommonApi\Render\EscapeInterface
     *
     * @return  object
     * @since   1.0
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
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getInstance($class, $property1 = null, $property2 = null)
    {
        try {
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

    /**
     * Return the saved view instance for external class (hence, the 'public'
     *
     * @return  object
     * @since   1.0
     */
    public function getSavedViewInstance()
    {
        return $this->options['view_instance'];
    }
}
