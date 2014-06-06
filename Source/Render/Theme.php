<?php
/**
 * Molajito Theme Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\RenderInterface;

/**
 * Molajito Theme Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Theme extends AbstractRenderer implements RenderInterface
{
    /**
     * Allowed Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'runtime_data'
        );

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
        return parent::includeFile($include_path . '/Index.phtml');
    }
}
