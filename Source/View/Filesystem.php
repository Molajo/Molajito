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

        $render->extension->include_path
            = $this->getBase($render->scheme)
            . $this->getFolder($render->scheme)
            . $render->extension->title
            . '/'
            . $this->getFile($render->scheme);

        return $render;
    }

    /**
     * Get Base Folder
     *
     * @param   string $scheme
     *
     * @return  string
     * @since   1.0
     */
    public function getBase($scheme)
    {
        if ($scheme === 'Theme') {
            $base = $this->theme_base_folder;
        } else {
            $base = $this->view_base_folder;
        }

        return $base;
    }

    /**
     * Get Base Folder
     *
     * @param   string $scheme
     *
     * @return  string
     * @since   1.0
     */
    public function getFolder($scheme)
    {
        if ($scheme === 'Theme') {
            $folder = '/';
        } else {
            $folder = '/' . $scheme . 's/';
        }

        return $folder;
    }

    /**
     * Get File
     *
     * @param   string $scheme
     *
     * @return  string
     * @since   1.0
     */
    public function getFile($scheme)
    {
        if ($scheme === 'Theme' || $scheme === 'Page') {
            $file = 'Index.phtml';
        } else {
            $file = '';
        }

        return $file;
    }
}
