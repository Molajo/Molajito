<?php
/**
 * Molajito Page View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\RenderInterface;

/**
 * Molajito Page View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class PageView extends AbstractRenderer implements RenderInterface
{
    /**
     * Include rendering file
     *
     * @param   string $include_path
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function includeFile($include_path)
    {
        $file_path = $include_path . '/Index.phtml';

        if (file_exists($file_path)) {
        } else {
            throw new RuntimeException(
                'Molajito Page Renderer - rendering file not found: ' . $file_path
            );
        }

        return $this->performRendering($file_path, $this->getProperties());
    }
}
