<?php
/**
 * Molajo Extension Adapter for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Extension;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Render\ExtensionInterface;
use stdClass;

/**
 * Molajo Extension Adapter for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Molajo extends AbstractAdapter implements ExtensionInterface
{
    /**
     * Resource
     *
     * @var    object
     * @since  1.0
     */
    protected $resource = null;

    /**
     * Constructor
     *
     * @param  object $resource
     *
     * @since  1.0
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get Resource for Rendering
     *
     * @param   array $options
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getResourceExtension(array $options = array())
    {
        $resource_extensions = new stdClass();

        $resource_extensions->theme    = $this->getTheme($options['theme']);
        $resource_extensions->page     = $this->getPageView($options['page_view']);
        $resource_extensions->template = $this->getTemplateView($options['template_view']);
        $resource_extensions->wrap     = $this->getWrapView($options['wrap_view']);

        return $resource_extensions;
    }

    /**
     * Get Data required to render token
     *
     * @param   object $token
     *
     * @return  stdClass
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getExtension($token)
    {
        $render         = new stdClass();
        $render->token  = $token;
        $scheme         = ucfirst(strtolower($token->type));
        $render->scheme = strtolower($scheme);

        if ($scheme == 'Page') {
            $render->extension = $this->getPageView(ucfirst(strtolower($token->name)));

        } elseif ($scheme == 'Template') {
            $render->extension = $this->getTemplateView(ucfirst(strtolower($token->name)));

        } elseif ($scheme == 'Wrap') {
            $render->extension = $this->getWrapView(ucfirst(strtolower($token->name)));

        } else {
            throw new RuntimeException ('Molajito Extension Resource: getExtension Invalid Scheme: ' . $scheme);
        }

        return $render;
    }

    /**
     * Get Theme Resource Extension
     *
     * @param   string $theme
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getTheme($theme)
    {
        try {
            return $this->resource->get('Theme:///Molajo//Themes//' . $theme);

        } catch (Exception $e) {
            throw new RuntimeException('Molajito Extension Resource: getTheme Failed: '
            . $theme . ' Message: ' . $e->getMessage());
        }
    }

    /**
     * Get Page View Resource
     *
     * @param   string $page_view
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getPageView($page_view)
    {
        try {
            return $this->resource->get('Page:///Molajo//Views//Pages//' . $page_view);

        } catch (Exception $e) {
            throw new RuntimeException('Molajito Extension Resource: getPageView Exception '
            . $page_view . ' Message: ' . $e->getMessage());
        }
    }

    /**
     * Get Template View Resource
     *
     * @param   string $template_view
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getTemplateView($template_view)
    {
        try {
            return $this->resource->get('Template:///Molajo//Views//Templates//' . $template_view);

        } catch (Exception $e) {
            throw new RuntimeException('Molajito Extension Resource: getTemplateView Exception '
            . $template_view . ' Message: ' . $e->getMessage());
        }
    }

    /**
     * Get Wrap View Resource
     *
     * @param   string $wrap_view
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getWrapView($wrap_view)
    {
        if (trim($wrap_view) == '') {
            return new stdClass();
        }

        try {
            return $this->resource->get('Wrap:///Molajo//Views//Wraps//' . $wrap_view);

        } catch (Exception $e) {
            throw new RuntimeException('Molajito Extension Resource: getWrapView Exception '
            . $wrap_view . ' Message: ' . $e->getMessage());
        }
    }
}
