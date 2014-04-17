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
use CommonApi\Render\RenderInterface;

/**
 * Molajito Factory Method
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
            throw new RuntimeException
            (
                'MolajitoFactoryMethod instantiateClass: Must provide options entry to molajito_base_folder.'
            );
        }

        $escape_instance   = $this->getEscapeInstance();
        $render_instance   = $this->getRenderInstance();
        $data_instance     = $this->getDataInstance();
        $view_instance     = $this->getViewInstance();
        $event_instance    = $this->getEventInstance();
        $parse_instance    = $this->getParseInstance();
        $exclude_tokens    = $this->getExcludeTokens();
        $stop_loop_count   = 100;
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

        $class = 'Molajito\\Engine';

        try {
            $molajito = new $class (
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
            (
                'MolajitoFactoryMethod instantiateClass: Could not instantiate Molajito Engine: ' . $class
            );
        }

        return $molajito;
    }

    /**
     * Instantiate Escape Class with installed Adapter
     *
     * @return  EscapeInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getEscapeInstance()
    {
        $escape_class = 'simple';

        if (isset($this->options['escape_class'])) {
            $escape_class = $this->options['escape_class'];
        }

        if ($escape_class === 'molajo') {
            if (file_exists($this->options['molajito_base_folder'] . '/vendor/molajo/Fieldhandler/Source/Driver.php')) {
            } else {
                $escape_class = 'simple';
            }
        }

        if ($escape_class === 'molajo') {
            $adapter = $this->getMolajoEscapeInstance();
        } else {
            $adapter = $this->getSimpleEscapeInstance();
        }

        $class = 'Molajito\\Escape';

        try {
            return new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getEscapeInstance: Could not instantiate Escape Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Molajo Escape Class
     *
     * @return  EscapeInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getMolajoEscapeInstance()
    {
        $fieldhandler_instance = $this->getFieldhandlerInstance();

        $class = 'Molajito\\Escape\\Molajo';

        try {
            return new $class ($fieldhandler_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getMolajoEscapeInstance: Could not instantiate Molajo Escape Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Escape Class
     *
     * @return  EscapeInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getFieldhandlerInstance()
    {
        $class = 'Molajo\\Fieldhandler\\Driver';

        try {
            return new $class ();

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getFieldhandlerInstance: Could not instantiate Fieldhandler Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Simple Escape Class
     *
     * @return  EscapeInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getSimpleEscapeInstance()
    {
        $class = 'Molajito\\Escape\\Simple';

        try {
            return new $class ();

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getSimpleEscapeInstance: Could not instantiate Simple Escape Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Render Class
     *
     * @return  RenderInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getRenderInstance()
    {
        $class = 'Molajito\\Render';

        try {
            return new $class ();

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getRenderInstance: Could not instantiate Render Class: ' . $class
            );
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
        if (file_exists($this->options['molajito_base_folder'] . '/vendor/molajo/Pagination/Source/Pagination.php')) {
            $class      = 'Molajo\\Pagination';
            $pagination = new $class();
        } else {
            $pagination = null;
        }

        $data_options = array();
        if (isset($this->options['data_options'])) {
            $data_options = $this->options['data_options'];
        }

        $class = 'Molajito\\Data\\' . ucfirst(strtolower($this->options['data_class']));

        try {
            $adapter = new $class ($data_options, $pagination);
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getDataInstance: Could not instantiate Data Adapter: ' . $class
            );
        }

        $class = 'Molajito\\Data';

        try {
            return new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getDataInstance: Could not instantiate Data Proxy Class: ' . $class
            );
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
        if (isset($this->options['view_class'])) {
            $view_class = $this->options['view_class'];
        } else {
            $view_class = 'filesystem';
        }

        if (strtolower($view_class) === 'filesystem') {

            $class = 'Molajito\\View\\Filesystem';

            try {
                $adapter = new $class(
                    $this->options['theme_base_folder'],
                    $this->options['view_base_folder']
                );

            } catch (Exception $e) {
                throw new RuntimeException
                (
                    'MolajitoFactoryMethod getViewInstance: Could not instantiate View Adapter: ' . $class
                );
            }
        } else {

            $class = 'Molajito\\View\\Molajo';

            try {

                $adapter = new $class(
                    $this->options['resource']
                );

            } catch (Exception $e) {
                throw new RuntimeException
                (
                    'MolajitoFactoryMethod getViewInstance: Could not instantiate View Adapter: ' . $class
                );
            }
        }

        /** Proxy */
        $class = 'Molajito\\View';

        try {
            $view_instance = new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getViewInstance: Could not instantiate Proxy View Class: ' . $class
            );
        }

        $this->options['view_instance'] = $view_instance;

        return $view_instance;
    }

    /**
     * Get Event Handler Instance
     *
     * @return  EventInterface
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getEventInstance()
    {
        if (file_exists($this->options['molajito_base_folder'] . '/vendor/molajo/event/Source/Scheduled.php')) {
            $class          = 'Molajito\\Event\\Molajo';
            $event_callback = $this->options['event_callback'];
        } else {
            $class          = 'Molajito\\Event\\Dummy';
            $event_callback = null;
        }

        try {
            $adapter = new $class($event_callback, array());

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getEventInstance: Could not instantiate Event Adapter: ' . $class
            );
        }

        /** Proxy */
        $class = 'Molajito\\Event';

        try {
            return new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getEventInstance: Could not instantiate Event Proxy Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Parse Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getParseInstance()
    {
        $class = 'Molajito\\Parse';

        try {
            return new $class ();

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getParseInstance: Could not instantiate Parse Class: ' . $class
            );
        }
    }

    /**
     * Get Exclude Tokens
     *
     * @return  array
     * @since   1.0
     */
    protected function getExcludeTokens()
    {
        if (isset($this->options['exclude_tokens'])) {
            return $this->options['exclude_tokens'];
        }

        return array();
    }

    /**
     * Instantiate Position Renderer Class
     *
     * @param   EscapeInterface $escape_instance
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getPositionInstance(EscapeInterface $escape_instance)
    {
        $class = 'Molajito\\Position';

        try {
            return new $class ($escape_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getPositionInstance: Could not instantiate Position Class: ' . $class
            );
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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getThemeInstance(EscapeInterface $escape_instance, RenderInterface $render_instance)
    {
        $class = 'Molajito\\Theme';

        try {
            return new $class ($escape_instance, $render_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getThemeInstance: Could not instantiate Theme Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Page View Renderer Class
     *
     * @param   RenderInterface $render_instance
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getPageInstance(RenderInterface $render_instance)
    {
        $class = 'Molajito\\PageView';

        try {
            return new $class ($render_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getPageInstance: Could not instantiate PageView Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Template View Renderer Class
     *
     * @param   EscapeInterface $escape_instance
     * @param   RenderInterface $render_instance
     * @param   EventInterface  $event_instance
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
            return new $class ($escape_instance, $render_instance, $event_instance, $event_option_keys);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getTemplateInstance: Could not instantiate TemplateView Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Wrap View Renderer Class
     *
     * @param   RenderInterface $render_instance
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getWrapInstance(RenderInterface $render_instance)
    {
        $class = 'Molajito\\WrapView';

        try {
            return new $class ($render_instance);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'MolajitoFactoryMethod getWrapInstance: Could not instantiate WrapView Class: ' . $class
            );
        }
    }
}

