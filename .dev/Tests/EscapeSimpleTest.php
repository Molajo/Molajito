<?php
/**
 * Simple Escape Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Escape;
use Molajito\Escape\Simple;
use stdClass;

/**
 * Simple Escape Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class EscapeSimpleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $escape_instance
     */
    protected $escape_instance;

    /**
     * Construct Simple Escape Class and Proxy
     */
    protected function setUp()
    {
        $simple = new Simple();

        $this->escape_instance = new Escape($simple);
    }

    /**
     * Test Null Value without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testSimpleNull()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = null;
        $query_results[] = $row;

        $results = $this->escape_instance->escapeOutput($query_results);

        $this->assertEquals(null, $results[0]->test_field);

        return $this;
    }

    /**
     * Test Numeric Value without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testSimpleNumeric()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = 33;
        $query_results[] = $row;

        $results = $this->escape_instance->escapeOutput($query_results);

        $this->assertEquals(33, $results[0]->test_field);

        return $this;
    }

    /**
     * Test Array without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testSimpleArray()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = array(1, 2, 3);
        $query_results[] = $row;

        $results = $this->escape_instance->escapeOutput($query_results);

        $this->assertEquals(array(1, 2, 3), $results[0]->test_field);

        return $this;
    }

    /**
     * Test HTML without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testSimpleHtml()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = '<article><p>I am a dog.</p></article>';
        $query_results[] = $row;

        $results = $this->escape_instance->escapeOutput($query_results);

        $this->assertEquals('<p>I am a dog.</p>', $results[0]->test_field);

        return $this;
    }

    /**
     * Test HTML without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testCustomWhiteList()
    {
        $white_list = '<b>';

        $simple = new Simple($white_list);

        $escape_instance = new Escape($simple);

        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = '<article><p>I <b>am</b> a dog.</p></article>';
        $query_results[] = $row;

        $results = $escape_instance->escapeOutput($query_results);

        $this->assertEquals('I <b>am</b> a dog.', $results[0]->test_field);

        return $this;
    }

    /**
     * Test HTML without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testSimpleObject()
    {
        $query_results         = array();
        $row                   = new stdClass();
        $row->test_field       = new stdClass();
        $row->test_field->name = 'dog';

        $query_results[] = $row;

        $results = $this->escape_instance->escapeOutput($query_results);

        $this->assertEquals(null, $results[0]->test_field);

        return $this;
    }
}
