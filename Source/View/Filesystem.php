<?php
/**
 * Filesystem View Adapter for Molajito Package
 *
 * @package    Molajito
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\View;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\ViewInterface;
use stdClass;

/**
 * Filesystem View Adapter for Rendering Package
 *
 * @package    Filesystem
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Filesystem extends AbstractAdapter implements ViewInterface
{
    /**
     * Theme Base Folder
     *
     * @var    string
     * @since  1.0
     */
    protected $theme_base_folder = null;

    /**
     * Views Base Folder
     *
     * @var    string
     * @since  1.0
     */
    protected $view_base_folder = null;

    /**
     * Constructor
     *
     * @param  string $theme_base_folder
     * @param  string $view_base_folder
     *
     * @since  1.0
     */
    public function __construct(
        $theme_base_folder,
        $view_base_folder
    ) {
        $this->theme_base_folder = $theme_base_folder;
        $this->view_base_folder  = $view_base_folder;
    }

    /**
     * Get View required for Rendering
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
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
            throw new RuntimeException ('Filesystem View Adapter: getExtension Invalid Scheme: ' . $scheme);
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
        if (is_file($this->theme_base_folder . '/' . $theme . '/Index.phtml')) {
            return $this->theme_base_folder . '/' . $theme . '/Index.phtml';
        }

        throw new RuntimeException('Filesystem View Adapter: getTheme Failed: '
        . $theme . ' Not found at: ' . $this->theme_base_folder . '/' . $theme . '/Index.phtml');
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
        if (is_file($this->view_base_folder . '/Views/Pages/' . $page_view . '/Index.phtml')) {
            return $this->view_base_folder . '/Views/Pages/' . $page_view . '/Index.phtml';
        }

        throw new RuntimeException('Filesystem View Adapter: getPageView Failed: '
        . $page_view . ' Not found at: ' . $this->view_base_folder
        . '/Views/Pages/' . $page_view . '/Index.phtml');
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
        if (is_dir($this->view_base_folder . '/Views/Templates/' . $template_view)) {
            return $this->view_base_folder . '/Views/Templates/' . $template_view;
        }

        throw new RuntimeException('Filesystem View Adapter: getTemplateView Failed: '
        . $template_view . ' Not found at: ' . $this->view_base_folder
        . '/Views/Templates/' . $template_view);
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
        if (is_dir($this->view_base_folder . '/Views/Wraps/' . $wrap_view)) {
            return $this->view_base_folder . '/Views/Wraps/' . $wrap_view;
        }

        throw new RuntimeException('Filesystem View Adapter: getTemplateView Failed: '
        . $wrap_view . ' Not found at: ' . $this->view_base_folder
        . '/Views/Wraps/' . $wrap_view);
    }
}
