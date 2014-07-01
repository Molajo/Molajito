<?php
/**
 * Position Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Render;
use Molajito\Render\Position;
use Molajito\Event;
use Molajito\Event\Dummy;
use Molajito\Escape;
use Molajito\Escape\Simple;
use stdClass;

/**
 * Position Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class RenderPositionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $position
     */
    protected $position;

    /**
     * Create Page View Instance
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
     * @covers  Molajito\Render\Position::__construct
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

        /** Position Instance */
        $this->position = new Position($escape, $render, $event);
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
     * @covers  Molajito\Render\PageView::renderOutput
     * @covers  Molajito\Render\PageView::setProperties
     * @covers  Molajito\Render\PageView::includeFile
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
     * @covers  Molajito\Render\Position::getPositionTemplateViews
     * @covers  Molajito\Render\Position::getPositionParameters
     * @covers  Molajito\Render\Position::matchPositionTemplates
     * @covers  Molajito\Render\Position::getPositionTemplates
     * @covers  Molajito\Render\Position::buildPositionArray
     * @covers  Molajito\Render\Position::buildPositionTemplatesArray
     * @covers  Molajito\Render\Position::getPositionTemplate
     * @covers  Molajito\Render\Position::searchPositionArray
     * @covers  Molajito\Render\Position::createIncludeStatements
     * @covers  Molajito\Render\Position::escapeTemplateName
     *
     * @return  $this
     * @since   1.0
     */
    public function testPositionNoMatch()
    {
        $expected = '{I template=Nomatch I} ';

        $resource_extension                                        = new stdClass();
        $resource_extension->page                                  = new stdClass();
        $resource_extension->page->menuitem                        = new stdClass();
        $resource_extension->page->menuitem->parameters            = new stdClass();
        $resource_extension->page->menuitem->parameters->positions = '{{test=dog,food}}{{more=not,used}}';

        $position_name = 'Nomatch';

        $results = $this->position->getPositionTemplateViews($position_name, $resource_extension);

        $this->assertEquals($expected, $results);

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
     * @covers  Molajito\Render\PageView::renderOutput
     * @covers  Molajito\Render\PageView::setProperties
     * @covers  Molajito\Render\PageView::includeFile
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
     * @covers  Molajito\Render\Position::getPositionTemplateViews
     * @covers  Molajito\Render\Position::getPositionParameters
     * @covers  Molajito\Render\Position::matchPositionTemplates
     * @covers  Molajito\Render\Position::getPositionTemplates
     * @covers  Molajito\Render\Position::buildPositionArray
     * @covers  Molajito\Render\Position::buildPositionTemplatesArray
     * @covers  Molajito\Render\Position::getPositionTemplate
     * @covers  Molajito\Render\Position::searchPositionArray
     * @covers  Molajito\Render\Position::createIncludeStatements
     * @covers  Molajito\Render\Position::escapeTemplateName
     *
     * @return  $this
     * @since   1.0
     */
    public function testPositionThemeParameters()
    {
        $expected = '{I template=Dog I} ';
        $expected .= PHP_EOL;
        $expected .= '{I template=Food I} ';

        $resource_extension                               = new stdClass();
        $resource_extension->theme                        = new stdClass();
        $resource_extension->theme->parameters            = new stdClass();
        $resource_extension->theme->parameters->positions = '{{test=dog,food}}{{more=not,used}}';

        $position_name = 'Test';

        $results = $this->position->getPositionTemplateViews($position_name, $resource_extension);

        $this->assertEquals($expected, $results);

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
     * @covers  Molajito\Render\PageView::renderOutput
     * @covers  Molajito\Render\PageView::setProperties
     * @covers  Molajito\Render\PageView::includeFile
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
     * @covers  Molajito\Render\Position::getPositionTemplateViews
     * @covers  Molajito\Render\Position::getPositionParameters
     * @covers  Molajito\Render\Position::matchPositionTemplates
     * @covers  Molajito\Render\Position::getPositionTemplates
     * @covers  Molajito\Render\Position::buildPositionArray
     * @covers  Molajito\Render\Position::buildPositionTemplatesArray
     * @covers  Molajito\Render\Position::getPositionTemplate
     * @covers  Molajito\Render\Position::searchPositionArray
     * @covers  Molajito\Render\Position::createIncludeStatements
     * @covers  Molajito\Render\Position::escapeTemplateName
     *
     * @return  $this
     * @since   1.0
     */
    public function testPositionMenuitem()
    {
        $expected = '{I template=Dog I} ';
        $expected .= PHP_EOL;
        $expected .= '{I template=Food I} ';

        $resource_extension                                        = new stdClass();
        $resource_extension->page                                  = new stdClass();
        $resource_extension->page->menuitem                        = new stdClass();
        $resource_extension->page->menuitem->parameters            = new stdClass();
        $resource_extension->page->menuitem->parameters->positions = '{{test=dog,food}}{{more=not,used}}';

        $position_name = 'Test';

        $results = $this->position->getPositionTemplateViews($position_name, $resource_extension);

        $this->assertEquals($expected, $results);

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
     * @covers  Molajito\Render\PageView::renderOutput
     * @covers  Molajito\Render\PageView::setProperties
     * @covers  Molajito\Render\PageView::includeFile
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
     * @covers  Molajito\Render\Position::getPositionTemplateViews
     * @covers  Molajito\Render\Position::getPositionParameters
     * @covers  Molajito\Render\Position::matchPositionTemplates
     * @covers  Molajito\Render\Position::getPositionTemplates
     * @covers  Molajito\Render\Position::buildPositionArray
     * @covers  Molajito\Render\Position::buildPositionTemplatesArray
     * @covers  Molajito\Render\Position::getPositionTemplate
     * @covers  Molajito\Render\Position::searchPositionArray
     * @covers  Molajito\Render\Position::createIncludeStatements
     * @covers  Molajito\Render\Position::escapeTemplateName
     *
     * @return  $this
     * @since   1.0
     */
    public function testPositionMenuitemLastEntry()
    {
        $expected = '{I template=Dog I} ';
        $expected .= PHP_EOL;
        $expected .= '{I template=Food I} ';

        $resource_extension                             = new stdClass();
        $resource_extension->page                       = new stdClass();
        $resource_extension->page->menuitem             = new stdClass();
        $resource_extension->page->menuitem->parameters = new stdClass();
        $resource_extension->page->menuitem->parameters->positions
                                                        = '{{test=dog1,food1}}{{more=not,used}}{{test=dog,food}}';

        $position_name = 'Test';

        $results = $this->position->getPositionTemplateViews($position_name, $resource_extension);

        $this->assertEquals($expected, $results);

        return $this;
    }
}
