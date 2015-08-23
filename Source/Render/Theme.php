<?php
/**
 * Molajito Theme Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Render\RenderInterface;

/**
 * Molajito Theme Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class Theme extends AbstractRenderer implements RenderInterface
{
    /**
     * Render output
     *
     * @param   array $data
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderOutput(array $data = array())
    {
        $data['on_before_event'] = 'onBeforeRenderTheme';
        $data['on_after_event']  = 'onAfterRenderTheme';
        $data['suffix']          = '/Index.phtml';

        return parent::renderOutput($data);
    }
}
