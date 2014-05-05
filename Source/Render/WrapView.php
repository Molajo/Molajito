<?php
/**
 * Molajito Wrap View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Render\RenderInterface;

/**
 * Molajito Wrap View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class WrapView extends AbstractRenderer implements RenderInterface
{
    /**
     * Allowed Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array = array(
        'runtime_data',
        'rendered_view',
        'row'
    );

    /**
     * Render Theme output
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     */
    public function render($include_path, array $data = array())
    {
        $this->include_path = $include_path;

        $this->setProperties($data, $this->property_array);

        $this->rendered_view = '';

        $this->renderViewPart('/Header.phtml');
        $this->renderViewPart('/Body.phtml');
        $this->renderViewPart('/Footer.phtml');

        return $this->rendered_view;
    }

    /**
     * Render View Part: Header, Body, Footer
     *
     * @param   string  $file
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewPart($file)
    {
        $file_path = $this->include_path . $file;

        if (file_exists($file_path)) {
            $this->rendered_view .= $this->renderOutput($file_path, $this->getProperties());
        }

        return $this;
    }
}
