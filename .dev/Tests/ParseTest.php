<?php
/**
 * Parse Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Parse;

/**
 * Parse Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ParseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $parse
     */
    protected $parse;

    /**
     * Initialises Adapter
     */
    protected function setUp()
    {
        $this->parse = new Parse();
    }

    /**
     * Test Parse
     *
     * @return  $this
     * @since   1.0
     */
    public function testParse()
    {
        $include_path = __DIR__ . '/Parse/Include.phtml';

        ob_start();
        include $include_path;
        $rendered_page = ob_get_clean();

        $results = $this->parse->parse($rendered_page, array(), null);

        $this->assertEquals('page', $results[0]->type);
        $this->assertEquals('xyz', $results[0]->name);
        $this->assertEquals('', $results[0]->wrap);
        $this->assertEquals(array(), $results[0]->attributes);

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
