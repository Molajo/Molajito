<?php
/**
 * Position Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Position;
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
     * @var $position_instance
     */
    protected $position_instance;

    /**
     * Initialises Position Instance
     */
    protected function setUp()
    {
        $simple = new Simple();
        $escape = new Escape($simple);
        $this->position_instance = new Position($escape);
    }

    /**
     * Test rendering a position
     *
     * @return  $this
     * @since   1.0
     */
    public function testPositionRender()
    {
        $expected = '{I template=Dog I} ';
        $expected .= PHP_EOL;
        $expected .= '{I template=Food I} ';

        $resource_extension = new stdClass();
        $resource_extension->page = new stdClass();
        $resource_extension->page->menuitem = new stdClass();
        $resource_extension->page->menuitem->parameters = new stdClass();
        $resource_extension->page->menuitem->parameters->positions = '{{test=dog,food}}{{more=not,used}}';

        $position_name = 'Test';

        $results = $this->position_instance->getPositionTemplateViews($position_name, $resource_extension);

        $this->assertEquals($expected, $results);

        return $this;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}
