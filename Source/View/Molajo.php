<?php
/**
 * Molajo View Adapter for Rendering Package
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\View;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Render\ViewInterface;
use stdClass;

/**
 * Molajo View Adapter for Rendering Package
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Molajo extends AbstractAdapter implements ViewInterface
{
    /**
     * Resource
     *
     * @var    object
     * @since  1.0.0
     */
    protected $resource = null;

    /**
     * Constructor
     *
     * @param  object $resource
     *
     * @since  1.0.0
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get View required for Rendering
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     */
    public function getView($token)
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

        } elseif ($scheme == 'Theme') {
            $render->extension = $this->getTheme(ucfirst(strtolower($token->name)));

        } else {
            throw new RuntimeException ('Molajo View Adapter: getExtension Invalid Scheme: ' . $scheme);
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
            throw new RuntimeException('Molajo View Adapter: getTheme Failed: '
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
            throw new RuntimeException('Molajo View Adapter: getPageView Exception '
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
            throw new RuntimeException('Molajo View Adapter: getTemplateView Exception '
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
            throw new RuntimeException('Molajo View Adapter: getWrapView Exception '
            . $wrap_view . ' Message: ' . $e->getMessage());
        }
    }
}
