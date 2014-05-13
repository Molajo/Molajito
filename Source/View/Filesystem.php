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
     * @since  1.0.0
     */
    protected $theme_base_folder = null;

    /**
     * Views Base Folder
     *
     * @var    string
     * @since  1.0.0
     */
    protected $view_base_folder = null;

    /**
     * Constructor
     *
     * @param  string $theme_base_folder
     * @param  string $view_base_folder
     *
     * @since  1.0.0
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
     * @return  stdClass
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

        if ($render->scheme === 'Theme') {
            $parts = $this->getThemeView();

        } elseif ($render->scheme === 'Page') {
            $parts = $this->getPageView();

        } else {
            $parts = $this->getTemplateWrapView($render);
        }

        $render->extension->include_path
            = $parts['base'] . $parts['folder'] . $render->extension->title . '/' . $parts['file'];

        return $render;
    }

    /**
     * Get Theme Information
     *
     * @return  object
     * @since   1.0
     */
    public function getThemeView()
    {
        $base   = $this->theme_base_folder;
        $folder = '/';
        $file   = 'Index.phtml';

        return array('base' => $base, 'folder' => $folder, 'file' => $file);
    }

    /**
     * Get PageView Information
     *
     * @return  object
     * @since   1.0
     */
    public function getPageView()
    {
        $base   = $this->view_base_folder;
        $folder = '/Pages/';
        $file   = 'Index.phtml';

        return array('base' => $base, 'folder' => $folder, 'file' => $file);
    }

    /**
     * Get Template and Wrap View Information
     *
     * @param  object $render
     *
     * @return  object
     * @since   1.0
     */
    public function getTemplateWrapView($render)
    {
        $base   = $this->view_base_folder;
        $folder = '/' . $render->scheme . 's/';
        $file   = '';

        return array('base' => $base, 'folder' => $folder, 'file' => $file);
    }
}
