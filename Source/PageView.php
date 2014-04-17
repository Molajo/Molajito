<?php
/**
 * Molajito Page View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\RenderInterface;
use Exception;

/**
 * Molajito Page View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class PageView implements RenderInterface
{
    /**
     * Render Instance
     *
     * @var    object   CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $render_instance = null;

    /**
     * Constructor
     *
     * @param  RenderInterface $render_instance
     *
     * @since  1.0.0
     */
    public function __construct(
        RenderInterface $render_instance
    ) {
        $this->render_instance = $render_instance;
    }

    /**
     * Render Page View
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     */
    public function render($include_path, array $data = array())
    {
        if (file_exists($include_path)) {
        } else {
            throw new RuntimeException
            ('Molajito PageView render Failed for File Path: ' . $include_path);
        }

        try {
            return $this->render_instance->render(
                $include_path,
                $data
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito PageView renderOutput Failed: '
            . ' for File path: ' . $include_path . ' Message: ' . $e->getMessage());
        }
    }
}
