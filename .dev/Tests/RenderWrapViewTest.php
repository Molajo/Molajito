<?php
/**
 * Template View Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Render;
use Molajito\Render\WrapView;
use Molajito\Event;
use Molajito\Event\Dummy;
use Molajito\Escape;
use Molajito\Escape\Simple;
use stdClass;

/**
 * Template View Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class WrapViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $wrap_view
     */
    protected $wrap_view;

    /**
     * Create Wrap Instance
     *
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape::__construct
     *
     * @covers  Molajito\Render\AbstractRenderer::__construct
     *
     * @covers  Molajito\Event\Dummy::__construct
     * @covers  Molajito\Event::__construct
     *
     * @covers  Molajito\Data\AbstractAdapter::__construct
     * @covers  Molajito\Data\Molajo::__construct
     * @covers  Molajito\Data::__construct
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View::__construct
     *
     * @covers  Molajito\Render\WrapView::__construct
     */
    protected function setUp()
    {
        /** Escape Instance */
        $simple = new Simple();
        $escape = new Escape($simple);

        /** Render Instance */
        $render = new Render();

        /** Event */
        $adapter = new Dummy();
        $event = new Event($adapter);

        /** WrapView Instance */
        $this->wrap_view = new WrapView($escape, $render, $event);
    }

    /**
     * Test Template View
     *
     * @covers  Molajito\Event::initializeEventOptions
     * @covers  Molajito\Event::scheduleEvent
     * @covers  Molajito\Event\Dummy::initializeEventOptions
     * @covers  Molajito\Event\Dummy::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers  Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @covers  Molajito\Render\WrapView::renderOutput
     * @covers  Molajito\Render\WrapView::renderViewWrap
     *
     * @covers  Molajito\Render\AbstractRenderer::renderOutput
     * @covers  Molajito\Render\AbstractRenderer::setProperties
     * @covers  Molajito\Render\AbstractRenderer::getProperties
     * @covers  Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::performRendering
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0
     */
    public function testCustomView()
    {
        $rendering_properties                  = array();
        $rendering_properties['query_results'] = 'a';
        $rendering_properties['row']           = 'b';
        $rendering_properties['runtime_data']  = 'c';

        $include_path = __DIR__ . '/Views';

        ob_start();
        include $include_path . '/Header.phtml';
        include $include_path . '/Body.phtml';
        include $include_path . '/Footer.phtml';
        $collect = ob_get_clean();

        $results = $this->wrap_view->renderOutput(
            $include_path,
            $rendering_properties
        );

        $this->assertEquals($collect, $results);

        return $this;
    }
}
