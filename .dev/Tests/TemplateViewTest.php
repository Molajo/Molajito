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
use Molajito\Render\TemplateView;
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
class TemplateViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $template_view
     */
    protected $template_view;

    /**
     * Create Theme Instance
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

        /** TemplateView Instance */
        $this->template_view = new TemplateView($escape, $render, $event);
    }

    /**
     * Test Template View
     *
     * @covers Molajito\Event::initializeEventOptions
     * @covers Molajito\Event::scheduleEvent
     * @covers Molajito\Event\Dummy::initializeEventOptions
     * @covers Molajito\Event\Dummy::scheduleEvent
     * @covers Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers Molajito\Event\AbstractAdapter::initializeEventOptions
     *
     * @covers Molajito\Escape::__construct
     * @covers Molajito\Escape::escapeOutput
     * @covers Molajito\Escape\Simple::__construct
     * @covers Molajito\Escape\Simple::escapeOutput
     * @covers Molajito\Escape\Simple::escapeDataElement
     * @covers Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @covers Molajito\Render\TemplateView::renderOutput
     * @covers Molajito\Render\TemplateView::renderViewCustom
     * @covers Molajito\Render\TemplateView::renderLoop
     * @covers Molajito\Render\TemplateView::renderViewNormal
     * @covers Molajito\Render\TemplateView::renderViewCustom
     * @covers Molajito\Render\TemplateView::renderViewPart
     *
     * @covers Molajito\Render\AbstractRenderer::renderOutput
     * @covers Molajito\Render\AbstractRenderer::setProperties
     * @covers Molajito\Render\AbstractRenderer::getProperties
     * @covers Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     * @covers Molajito\Render\AbstractRenderer::performRendering
     *
     * @covers Molajito\Render::renderOutput
     * @covers Molajito\Render::setProperties
     * @covers Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0
     */
    public function testCustomView()
    {
        $data = array();

        $data['query_results']   = array();
        $row                     = new stdClass();
        $row->id                 = 0;
        $row->title              = 'I am a title';
        $row->content_text       = '<p>I am a paragraph</p>';
        $data['query_results'][] = $row;

        $data['parameters'] = array();

        $data['model_registry'] = array(
            'id'           => array('name' => 'id', 'type' => 'integer'),
            'title'        => array('name' => 'title', 'type' => 'string'),
            'content_text' => array('name' => 'content_text', 'type' => 'html')
        );

        $include_path = __DIR__ . '/CustomView';

        ob_start();
        include $include_path . '/Custom.phtml';
        $collect = ob_get_clean();

        $results = $this->template_view->renderOutput($include_path, $data);

        $this->assertEquals($collect, $results);

        return $this;
    }

    /**
     * Test 'Normal' Template View
     *
     * @covers Molajito\Event::initializeEventOptions
     * @covers Molajito\Event::scheduleEvent
     * @covers Molajito\Event\Dummy::initializeEventOptions
     * @covers Molajito\Event\Dummy::scheduleEvent
     * @covers Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers Molajito\Event\AbstractAdapter::initializeEventOptions
     *
     * @covers Molajito\Escape::__construct
     * @covers Molajito\Escape::escapeOutput
     * @covers Molajito\Escape\Simple::__construct
     * @covers Molajito\Escape\Simple::escapeOutput
     * @covers Molajito\Escape\Simple::escapeDataElement
     * @covers Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @covers Molajito\Render\TemplateView::renderOutput
     * @covers Molajito\Render\TemplateView::renderViewCustom
     * @covers Molajito\Render\TemplateView::renderLoop
     * @covers Molajito\Render\TemplateView::renderViewNormal
     * @covers Molajito\Render\TemplateView::renderViewCustom
     * @covers Molajito\Render\TemplateView::renderViewPart
     *
     * @covers Molajito\Render\AbstractRenderer::renderOutput
     * @covers Molajito\Render\AbstractRenderer::setProperties
     * @covers Molajito\Render\AbstractRenderer::getProperties
     * @covers Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     * @covers Molajito\Render\AbstractRenderer::performRendering
     *
     * @covers Molajito\Render::renderOutput
     * @covers Molajito\Render::setProperties
     * @covers Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0
     */
    public function testHFBView()
    {
        $data = array();

        $data['query_results']   = array();
        $row                     = new stdClass();
        $row->id                 = 1;
        $row->title              = 'I am a title 1';
        $row->content_text       = '<p>I am a paragraph 1</p>';
        $data['query_results'][] = $row;

        $data['parameters'] = array();

        $data['model_registry'] = array(
            'id'           => array('name' => 'id', 'type' => 'integer'),
            'title'        => array('name' => 'title', 'type' => 'string'),
            'content_text' => array('name' => 'content_text', 'type' => 'html')
        );

        $include_path = __DIR__ . '/Views';

        ob_start();
        include $include_path . '/Header.phtml';
        include $include_path . '/Body.phtml';
        include $include_path . '/Footer.phtml';
        $collect = ob_get_clean();

        $results = $this->template_view->renderOutput($include_path, $data);

        $this->assertEquals($collect, $results);

        return $this;
    }
}
