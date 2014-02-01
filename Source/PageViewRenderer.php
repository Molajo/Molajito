<?php
/**
 * Pagination Page View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use Exception;
use CommonApi\Render\RenderInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Pagination Page View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class PageViewRenderer implements RenderInterface
{
    /**
     * Path to Include File
     *
     * @var    string
     * @since  1.0
     */
    protected $include_path;

    /**
     * Render option keys
     *
     * @var    array
     * @since  1.0
     */
    protected $rendering_properties = array();

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_view = null;

    /**
     * Constructor
     *
     * @param  string  $include_path
     * @param  array   $rendering_properties
     *
     * @since  1.0
     */
    public function __construct(
        $include_path,
        array $rendering_properties = array()
    ) {
        $this->include_path         = $include_path;
        $this->rendering_properties = $rendering_properties;
    }

    /**
     * Render Page View
     *
     * @return  string
     * @since   1.0
     */
    public function render()
    {
        $this->rendered_view = '';

        $this->renderView();

        return $this->rendered_view;
    }

    /**
     * Render Template
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderView()
    {
        $file_path = $this->include_path;

        if (file_exists($file_path)) {
            $this->rendered_view = $this->renderOutput($file_path);
        }

        return $this;
    }

    /**
     * Instantiate Render Class and Render Output
     *
     * @param   string  $file_path
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderOutput($file_path)
    {
        $options                 = $this->rendering_properties;
        $options['include_path'] = $file_path;

        try {
            $instance = new Render($options);

            return $instance->render();

        } catch (Exception $e) {
            throw new RuntimeException
            ('Pagination PageViewRenderer renderOutput: ' . $e->getMessage());
        }
    }
}
