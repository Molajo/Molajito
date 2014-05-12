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
use Exception;

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
     * Render Theme output
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderOutput($include_path, array $data = array())
    {
        $this->setProperties($data, $this->property_array);

        return $this->includeFile($include_path);
    }

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
                'Molajito Theme Renderer - rendering file not found: ' . $file_path
            );
        }

        try {
            return $this->performRendering($file_path, $this->getProperties());

        } catch (Exception $e) {
            throw new RuntimeException(
                'Molajito Theme includeFile: ' . $e->getMessage()
            );
        }
    }
}
