<?php
/**
 * Molajito Wrap View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Render\RenderInterface;

/**
 * Molajito Wrap View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class WrapView extends AbstractRenderer implements RenderInterface
{
    /**
     * Render output for specified file and data
     *
     * @param   array $data
     *
     * @return  string
     * @since   1.0.0
     */
    public function renderOutput(array $data = array())
    {
        $data['on_before_event'] = 'onBeforeRenderWrap';
        $data['on_after_event']  = 'onAfterRenderWrap';

        $this->initialise($data);
        $this->scheduleEvent($this->on_before_event, array());
        $this->row = $this->query_results[0];

        $this->renderView('/Header.phtml');
        $this->renderView('/Body.phtml');
        $this->renderView('/Footer.phtml');
        $this->scheduleEvent($this->on_after_event, array());

        return $this->rendered_view;
    }
}
