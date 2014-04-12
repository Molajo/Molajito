<?php
/**
 * Bootstrap Dependency Injection for Sample Theme
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Factories;

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
class MolajitoFactoryMethod
{
    /**
     * Molajito Folder
     *
     * @var    string
     * @since  1.0
     */
    protected $molajito_base_folder = null;

    /**
     * Theme Base Folder
     *
     * @var    string
     * @since  1.0
     */
    protected $theme_base_folder = null;

    /**
     * View Base Folder
     *
     * @var    string
     * @since  1.0
     */
    protected $view_base_folder = null;

    /**
     * Posts Folder
     *
     * @var    string
     * @since  1.0
     */
    protected $posts_base_folder = null;

    /**
     * Authors Folder
     *
     * @var    string
     * @since  1.0
     */
    protected $author_base_folder = null;

    /**
     * Options
     *
     * @var    array
     * @since  1.0
     */
    protected $options = array();

    /**
     * Post Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $post_model_registry = array();

    /**
     * Post Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $author_model_registry = array();

    /**
     * Class Constructor
     *
     * @param  string  $molajito_base_folder
     * @param  string  $theme_base_folder
     * @param  string  $view_base_folder
     * @param  string  $posts_base_folder
     * @param  string  $author_base_folder
     * @param  string  $post_model_registry
     * @param  string  $author_model_registry
     *
     * @since  1.0
     */
    public function __construct(
        $molajito_base_folder,
        $theme_base_folder,
        $view_base_folder,
        $posts_base_folder,
        $author_base_folder,
        $post_model_registry,
        $author_model_registry
    ) {
        $this->molajito_base_folder  = $molajito_base_folder;
        $this->theme_base_folder     = $theme_base_folder;
        $this->view_base_folder      = $view_base_folder;
        $this->posts_base_folder     = $posts_base_folder;
        $this->author_base_folder    = $author_base_folder;
        $this->post_model_registry   = $post_model_registry;
        $this->author_model_registry = $author_model_registry;

        /** Event System is not hooked up in this example */
        $this->options['event_option_keys'] = array();
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
        $escape_instance       = $this->getEscapeInstance();
        $render_instance       = $this->getRenderInstance();
        $data_instance         = $this->getDataInstance();
        $view_instance         = $this->getViewInstance();
        $event_instance        = $this->getEventInstance();
        $parse_instance        = $this->getParseInstance();
        $exclude_tokens        = $this->getExcludeTokens();
        $stop_loop_count       = 100;
        $position_instance     = $this->getPositionInstance($escape_instance);
        $theme_instance        = $this->getThemeInstance($escape_instance, $render_instance);
        $page_instance         = $this->getPageInstance($render_instance);
        $template_instance     = $this->getTemplateInstance(
            $escape_instance,
            $render_instance,
            $event_instance,
            $this->options['event_option_keys']
        );
        $wrap_instance         = $this->getWrapInstance($render_instance);

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
                'Molajito: Could not instantiate Molajito Driver Class: ' . $class
            );
        }

        return $molajito;
    }

    /**
     * Instantiate Escape Class with installed Adapter
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getEscapeInstance()
    {
        if (file_exists($this->molajito_base_folder . '/vendor/molajo/Fieldhandler/Source/Driver.php')) {
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
                'Molajito: Could not instantiate Escape Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Molajo Escape Class
     *
     * @return  $this
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
                'Molajito: Could not instantiate Molajo Escape Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Escape Class
     *
     * @return  $this
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
                'Molajito: Could not instantiate Fieldhandler Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Simple Escape Class
     *
     * @return  $this
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
                'Molajito: Could not instantiate Simple Escape Class: ' . $class
            );
        }
    }

    /**
     * Instantiate Render Class
     *
     * @return  $this
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
                'Molajito: Could not instantiate Render Class: ' . $class
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
        if (file_exists($this->molajito_base_folder . '/vendor/molajo/Pagination/Source/Pagination.php')) {
            include $this->molajito_base_folder . '/vendor/molajo/Pagination/Source/Pagination.php';
            $pagination = new \Molajo\Pagination();
        } else {
            $pagination = null;
        }

        $class = 'Molajito\\Data\\FilesystemModel';

        try {
            $adapter = new $class (
                $this->posts_base_folder,
                $this->author_base_folder,
                $this->post_model_registry,
                $this->author_model_registry,
                $pagination
            );
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito: Could not instantiate Data Adapter Class: ' . $class
            );
        }

        $class = 'Molajito\\Data';

        try {
            return new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito: Could not instantiate Data Class: ' . $class
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
        $class = 'Molajito\\View\\Filesystem';

        try {
            $adapter = new $class(
                $this->theme_base_folder,
                $this->view_base_folder
            );

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito: Could not instantiate Extension Class: ' . $class
            );
        }

        /** Proxy */
        $class = 'Molajito\\View';

        try {
            $view_instance = new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito: Could not instantiate Data Class: ' . $class
            );
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
        $class = 'Molajito\\Event\\Dummy';

        try {
            $adapter = new $class(
                null,
                $this->options['event_option_keys']
            );

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito: Could not instantiate Event Class: ' . $class
            );
        }

        /** Proxy */
        $class = 'Molajito\\Event';

        try {
            return new $class ($adapter);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito: Could not instantiate Data Class: ' . $class
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
                'Molajito: Could not instantiate Parse Class: ' . $class
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
                'Molajito: Could not instantiate Position Renderer Class: ' . $class
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
                'Molajito: Could not instantiate Theme Renderer Class: ' . $class
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
                'Molajito: Could not instantiate Page View Renderer Class: ' . $class
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
            return new $class (
                $escape_instance,
                $render_instance,
                $event_instance,
                $event_option_keys
            );

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito: Could not instantiate Template View Renderer Class: ' . $class
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
                'Molajito: Could not instantiate Wrap View Renderer Class: ' . $class
            );
        }
    }
}

