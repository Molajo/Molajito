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
class PositionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $position
     */
    protected $position;

    /**
     * Create Theme Instance
     */
    protected function setUp()
    {
        /** Escape Instance */
        $simple = new Simple();
        $escape = new Escape($simple);

        /** Position Instance */
        $this->position = new Position($escape);
    }

    /**
     * Test Position
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
     * @covers Molajito\Render\Position::getPositionTemplateViews
     * @covers Molajito\Render\Position::getPositionTemplates
     * @covers Molajito\Render\Position::buildPositionArray
     * @covers Molajito\Render\Position::searchPositionArray
     * @covers Molajito\Render\Position::createIncludeStatements
     * @covers Molajito\Render\Position::escapeTemplateName
     *
     * @return  $this
     * @since   1.0
     */
    public function testPositionRender()
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
}
