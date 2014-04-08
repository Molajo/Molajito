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
 * @since      1.0.0
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
        $render                   = new stdClass();
        $render->token            = $token;
        $render->scheme           = ucfirst(strtolower($token->type));
        $render->extension        = new stdClass();
        $render->extension->title = ucfirst(strtolower($token->name));

        if ($render->scheme == 'Page') {
            $render->extension->include_path = $this->getPageView(ucfirst(strtolower($token->name)));

        } elseif ($render->scheme == 'Template') {
            $render->extension->include_path = $this->getTemplateView(ucfirst(strtolower($token->name)));

        } elseif ($render->scheme == 'Wrap') {
            $render->extension->include_path = $this->getWrapView(ucfirst(strtolower($token->name)));

        } elseif ($render->scheme == 'Theme') {
            $render->extension->include_path = $this->getTheme(ucfirst(strtolower($token->name)));

        } else {
            throw new RuntimeException (
                'Filesystem View Adapter: getExtension Invalid Scheme: '
                . $render->scheme
            );
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

        throw new RuntimeException(
            'Filesystem View Adapter: getTheme Failed: '
            . $theme . ' Not found at: ' . $this->theme_base_folder . '/' . $theme . '/Index.phtml'
        );
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
        if (is_file($this->view_base_folder . '/Pages/' . $page_view . '/Index.phtml')) {
            return $this->view_base_folder . '/Pages/' . $page_view . '/Index.phtml';
        }

        throw new RuntimeException(
            'Filesystem View Adapter: getPageView Failed: '
            . $page_view . ' Not found at: ' . $this->view_base_folder
            . '/Pages/' . $page_view . '/Index.phtml'
        );
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
        if (is_dir($this->view_base_folder . '/Templates/' . $template_view)) {
            return $this->view_base_folder . '/Templates/' . $template_view;
        }

        throw new RuntimeException(
            'Filesystem View Adapter: getTemplateView Failed: '
            . $template_view . ' Not found at: ' . $this->view_base_folder
            . '/Templates/' . $template_view
        );
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
        if (is_dir($this->view_base_folder . '/Wraps/' . $wrap_view)) {
            return $this->view_base_folder . '/Wraps/' . $wrap_view;
        }

        throw new RuntimeException(
            'Filesystem View Adapter: getTemplateView Failed: '
            . $wrap_view . ' Not found at: ' . $this->view_base_folder
            . '/Wraps/' . $wrap_view
        );
    }
}
