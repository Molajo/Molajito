<?php
/**
 * Render Template View Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
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
 * Render Template View Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class RenderTemplateViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $template_view
     */
    protected $template_view;

    /**
     * Create Template View Instance
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
     * @covers  Molajito\Render\TemplateView::__construct
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
        $event   = new Event($adapter);

        /** TemplateView Instance */
        $this->template_view = new TemplateView($escape, $render, $event);
    }

    /**
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
     * @covers  Molajito\Render\TemplateView::renderOutput
     * @covers  Molajito\Render\TemplateView::renderViewCustom
     * @covers  Molajito\Render\TemplateView::renderLoop
     * @covers  Molajito\Render\TemplateView::initializeRenderLoop
     * @covers  Molajito\Render\TemplateView::renderViewNormal
     * @covers  Molajito\Render\TemplateView::renderViewCustom
     * @covers  Molajito\Render\TemplateView::renderViewPart
     * @covers  Molajito\Render\TemplateView::setRenderViewOptions
     * @covers  Molajito\Render\TemplateView::includeFile
     *
     * @covers  Molajito\Render\AbstractRenderer::renderOutput
     * @covers  Molajito\Render\AbstractRenderer::setProperties
     * @covers  Molajito\Render\AbstractRenderer::getProperties
     * @covers  Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::performRendering
     * @covers  Molajito\Render\AbstractRenderer::includeFile
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0.0
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
     * @covers  Molajito\Render\TemplateView::renderOutput
     * @covers  Molajito\Render\TemplateView::renderViewCustom
     * @covers  Molajito\Render\TemplateView::renderLoop
     * @covers  Molajito\Render\TemplateView::initializeRenderLoop
     * @covers  Molajito\Render\TemplateView::renderViewNormal
     * @covers  Molajito\Render\TemplateView::renderViewCustom
     * @covers  Molajito\Render\TemplateView::renderViewPart
     * @covers  Molajito\Render\TemplateView::setRenderViewOptions
     * @covers  Molajito\Render\TemplateView::includeFile
     *
     * @covers  Molajito\Render\AbstractRenderer::renderOutput
     * @covers  Molajito\Render\AbstractRenderer::setProperties
     * @covers  Molajito\Render\AbstractRenderer::getProperties
     * @covers  Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::performRendering
     * @covers  Molajito\Render\AbstractRenderer::includeFile
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0.0
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

    /**
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
     * @covers  Molajito\Render\TemplateView::renderOutput
     * @covers  Molajito\Render\TemplateView::renderViewCustom
     * @covers  Molajito\Render\TemplateView::renderLoop
     * @covers  Molajito\Render\TemplateView::initializeRenderLoop
     * @covers  Molajito\Render\TemplateView::renderViewNormal
     * @covers  Molajito\Render\TemplateView::renderViewCustom
     * @covers  Molajito\Render\TemplateView::renderViewPart
     * @covers  Molajito\Render\TemplateView::setRenderViewOptions
     * @covers  Molajito\Render\TemplateView::includeFile
     *
     * @covers  Molajito\Render\AbstractRenderer::renderOutput
     * @covers  Molajito\Render\AbstractRenderer::setProperties
     * @covers  Molajito\Render\AbstractRenderer::getProperties
     * @covers  Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::performRendering
     * @covers  Molajito\Render\AbstractRenderer::includeFile
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testOnlyHeaderExists()
    {
        $data = array();

        // two rows but only one header
        $data['query_results'] = array();

        $row                     = new stdClass();
        $row->id                 = 1;
        $row->title              = 'I am a title 1';
        $row->content_text       = '<p>I am a paragraph 1</p>';
        $data['query_results'][] = $row;

        $row                     = new stdClass();
        $row->id                 = 2;
        $row->title              = 'I am a title 2';
        $row->content_text       = '<p>I am a paragraph 2</p>';
        $data['query_results'][] = $row;

        $data['parameters'] = array();

        $data['model_registry'] = array(
            'id'           => array('name' => 'id', 'type' => 'integer'),
            'title'        => array('name' => 'title', 'type' => 'string'),
            'content_text' => array('name' => 'content_text', 'type' => 'html')
        );

        $include_path = __DIR__ . '/ViewFilesystem/Views/Templates/Headeronly';

        ob_start();
        include $include_path . '/Header.phtml';
        $collect = ob_get_clean();

        $results = $this->template_view->renderOutput($include_path, $data);

        $this->assertEquals($collect, $results);

        return $this;
    }

    /**
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
     * @covers  Molajito\Render\TemplateView::renderOutput
     * @covers  Molajito\Render\TemplateView::renderViewCustom
     * @covers  Molajito\Render\TemplateView::renderLoop
     * @covers  Molajito\Render\TemplateView::initializeRenderLoop
     * @covers  Molajito\Render\TemplateView::renderViewNormal
     * @covers  Molajito\Render\TemplateView::renderViewCustom
     * @covers  Molajito\Render\TemplateView::renderViewPart
     * @covers  Molajito\Render\TemplateView::setRenderViewOptions
     * @covers  Molajito\Render\TemplateView::includeFile
     *
     * @covers  Molajito\Render\AbstractRenderer::renderOutput
     * @covers  Molajito\Render\AbstractRenderer::setProperties
     * @covers  Molajito\Render\AbstractRenderer::getProperties
     * @covers  Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::performRendering
     * @covers  Molajito\Render\AbstractRenderer::includeFile
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testOnlyBodyExists()
    {
        $data = array();

        $data['query_results'] = array();

        $row                     = new stdClass();
        $row->id                 = 1;
        $row->title              = 'I am a title 1';
        $row->content_text       = '<p>I am a paragraph 1</p>';
        $data['query_results'][] = $row;

        $row                     = new stdClass();
        $row->id                 = 2;
        $row->title              = 'I am a title 2';
        $row->content_text       = '<p>I am a paragraph 2</p>';
        $data['query_results'][] = $row;

        $data['parameters'] = array();

        $data['model_registry'] = array(
            'id'           => array('name' => 'id', 'type' => 'integer'),
            'title'        => array('name' => 'title', 'type' => 'string'),
            'content_text' => array('name' => 'content_text', 'type' => 'html')
        );

        $include_path = __DIR__ . '/ViewFilesystem/Views/Templates/Bodyonly';

        ob_start();
        include $include_path . '/Body.phtml';
        include $include_path . '/Body.phtml';
        $collect = ob_get_clean();

        $results = $this->template_view->renderOutput($include_path, $data);

        $this->assertEquals($collect, $results);

        return $this;
    }
}
