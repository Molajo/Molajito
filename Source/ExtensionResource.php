<?php
/**
 * Pagination Extension Resource
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Render\ExtensionResourceInterface;
use stdClass;

/**
 * Pagination Extension Resource
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ExtensionResource implements ExtensionResourceInterface
{
    /**
     * Resource
     *
     * @var    object
     * @since  1.0
     */
    protected $resource = null;

    /**
     * Theme ID
     *
     * @var    string
     * @since  1.0
     */
    protected $theme;

    /**
     * Page View ID
     *
     * @var    string
     * @since  1.0
     */
    protected $page_view;

    /**
     * Template View ID
     *
     * @var    string
     * @since  1.0
     */
    protected $template_view;

    /**
     * Wrap View ID
     *
     * @var    string
     * @since  1.0
     */
    protected $wrap_view;

    /**
     * Constructor
     *
     * @param  object $resource
     * @param  string $theme
     * @param  string $page_view
     * @param  string $template_view
     * @param  string $wrap_view
     *
     * @since  1.0
     */
    public function __construct(
        $resource,
        $theme,
        $page_view,
        $template_view,
        $wrap_view
    ) {
        $this->resource      = $resource;
        $this->theme         = $theme;
        $this->page_view     = $page_view;
        $this->template_view = $template_view;
        $this->wrap_view     = $wrap_view;
    }

    /**
     * Get Resource for Rendering
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getResourceExtension()
    {
        $extension = new stdClass();

        $extension->theme    = $this->getTheme();
        $extension->page     = $this->getPageView();
        $extension->template = $this->getTemplateView();
        $extension->wrap     = $this->getWrapView();

        return $extension;
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
            $this->page_view   = ucfirst(strtolower($token->name));
            $render->extension = $this->getPageView();

        } elseif ($scheme == 'Template') {
            $this->template_view = ucfirst(strtolower($token->name));
            $render->extension   = $this->getTemplateView();

        } elseif ($scheme == 'Wrap') {
            $this->wrap_view   = ucfirst(strtolower($token->name));
            $render->extension = $this->getWrapView();

        } else {
            throw new RuntimeException ('Pagination Extension Resource: getExtension Invalid Scheme: ' . $scheme);
        }

        return $render;
    }

    /**
     * Get Theme Resource Extension
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getTheme()
    {
        try {
            return $this->resource->get('Theme:///Molajo//Theme//' . $this->theme);

        } catch (Exception $e) {
            throw new RuntimeException('Pagination Extension Resource: getTheme Exception ' . $e->getMessage());
        }
    }

    /**
     * Get Page View Resource
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getPageView()
    {
        try {
            return $this->resource->get('Page:///Molajo//View//Page//' . $this->page_view);

        } catch (Exception $e) {
            throw new RuntimeException('Pagination Extension Resource: getPageView Exception ' . $e->getMessage());
        }
    }

    /**
     * Get Template View Resource
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getTemplateView()
    {
        try {
            return $this->resource->get('Template:///Molajo//View//Template//' . $this->template_view);

        } catch (Exception $e) {
            throw new RuntimeException('Pagination Extension Resource: getTemplateView Exception ' . $e->getMessage());
        }
    }

    /**
     * Get Wrap View Resource
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getWrapView()
    {
        try {
            return $this->resource->get('Wrap:///Molajo//View//Wrap//' . $this->wrap_view);

        } catch (Exception $e) {
            throw new RuntimeException('Pagination Extension Resource: getWrapView Exception ' . $e->getMessage());
        }
    }
}
