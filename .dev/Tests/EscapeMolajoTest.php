<?php
/**
 * Molajo Escape Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use CommonApi\Model\FieldhandlerInterface;
use Exception;
use Molajito\Escape;
use Molajito\Escape\Molajo;
use stdClass;

/**
 * Molajo Escape Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class EscapeMolajoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $escape_instance
     */
    protected $escape_instance;

    /**
     * Construct Molajo Escape Class and Proxy
     */
    protected function setUp()
    {
        $molajo = new Molajo(new MockFieldHandler());

        $this->escape_instance = new Escape($molajo);
    }

    /**
     * Test Null Value without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Molajo::__construct
     * @covers  Molajito\Escape\Molajo::escapeOutput
     * @covers  Molajito\Escape\Molajo::escapeDataElement
     * @covers  Molajito\Escape\Molajo::setEscapeDataType
     * @covers  Molajito\Escape\Molajo::setDefaultEscapeDataType
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testMolajoNull()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = null;
        $query_results[] = $row;

        $model_registry = array(
            'test_field' => array('name' => 'test_field', 'type' => 'url')
        );

        $results = $this->escape_instance->escapeOutput($query_results, $model_registry);

        $this->assertEquals(null, $results[0]->test_field);

        return $this;
    }

    /**
     * Test Numeric Value without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Molajo::__construct
     * @covers  Molajito\Escape\Molajo::escapeOutput
     * @covers  Molajito\Escape\Molajo::escapeDataElement
     * @covers  Molajito\Escape\Molajo::setEscapeDataType
     * @covers  Molajito\Escape\Molajo::setDefaultEscapeDataType
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testMolajoNumeric()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = 33;
        $query_results[] = $row;

        $model_registry = array(
            'test_field' => array('name' => 'test_field', 'type' => 'integer')
        );

        $results = $this->escape_instance->escapeOutput($query_results, $model_registry);

        $this->assertEquals(33, $results[0]->test_field);

        return $this;
    }

    /**
     * Test Array without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Molajo::__construct
     * @covers  Molajito\Escape\Molajo::escapeOutput
     * @covers  Molajito\Escape\Molajo::escapeDataElement
     * @covers  Molajito\Escape\Molajo::setEscapeDataType
     * @covers  Molajito\Escape\Molajo::setDefaultEscapeDataType
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testMolajoArray()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = array(1, 2, 3);
        $query_results[] = $row;

        $model_registry = array(
            'test_field' => array('name' => 'test_field', 'type' => 'array')
        );

        $results = $this->escape_instance->escapeOutput($query_results, $model_registry);

        $this->assertEquals(array(1, 2, 3), $results[0]->test_field);

        return $this;
    }

    /**
     * Test HTML without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Molajo::__construct
     * @covers  Molajito\Escape\Molajo::escapeOutput
     * @covers  Molajito\Escape\Molajo::escapeDataElement
     * @covers  Molajito\Escape\Molajo::setEscapeDataType
     * @covers  Molajito\Escape\Molajo::setDefaultEscapeDataType
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testMolajoHtml()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = '<article><p>I am a dog.</p></article>';
        $query_results[] = $row;

        $model_registry = array(
            'test_field' => array('name' => 'test_field', 'type' => 'html')
        );

        $results = $this->escape_instance->escapeOutput($query_results, $model_registry);

        $this->assertEquals('<p>I am a dog.</p>', $results[0]->test_field);

        return $this;
    }

    /**
     * Test HTML without Model Registry
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Molajo::__construct
     * @covers  Molajito\Escape\Molajo::escapeOutput
     * @covers  Molajito\Escape\Molajo::escapeDataElement
     * @covers  Molajito\Escape\Molajo::setEscapeDataType
     * @covers  Molajito\Escape\Molajo::setDefaultEscapeDataType
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testMolajoNoEscapeKey()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->numeric    = 123;
        $row->isnull     = null;
        $row->isarray    = array();
        $row->string     = 'string';
        $query_results[] = $row;

        $model_registry = array();

        $results = $this->escape_instance->escapeOutput($query_results, $model_registry);

        $this->assertEquals(123, $results[0]->numeric);
        $this->assertEquals(null, $results[0]->isnull);
        $this->assertEquals(array(), $results[0]->isarray);
        $this->assertEquals('string', $results[0]->string);

        return $this;
    }

    /**
     * Test Null Value without Model Registry
     *
     * @covers                   Molajito\Escape::__construct
     * @covers                   Molajito\Escape::escapeOutput
     * @covers                   Molajito\Escape\Molajo::__construct
     * @covers                   Molajito\Escape\Molajo::escapeOutput
     * @covers                   Molajito\Escape\Molajo::escapeDataElement
     * @covers                   Molajito\Escape\Molajo::setEscapeDataType
     * @covers                   Molajito\Escape\Molajo::setDefaultEscapeDataType
     * @covers                   Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers                   Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @expectedException        \CommonApi\Exception\RuntimeException
     * @expectedExceptionMessage Escape Driver escape Method Failed: Molajito Escape Molajo: Fieldhandler class Failed for Key: test_field Fieldhandler: pancake Molajito Escape Molajo
     *
     * @return  $this
     * @since                    1.0
     */
    public function testMolajoException()
    {
        $query_results   = array();
        $row             = new stdClass();
        $row->test_field = 'pancake';
        $query_results[] = $row;

        $model_registry = array(
            'test_field' => array('name' => 'test_field', 'type' => 'exception')
        );

        $results = $this->escape_instance->escapeOutput($query_results, $model_registry);

        return $this;
    }
}


class MockFieldHandler implements FieldhandlerInterface
{

    protected $white_list = '<b><em><i><img><p><u><strong>';

    protected $field_value = null;

    public function validate(
        $field_name,
        $field_value = null,
        $constraint,
        array $options = array()
    ) {

    }

    public function sanitize(
        $field_name,
        $field_value = null,
        $constraint,
        array $options = array()
    ) {
        if (is_numeric($field_value)) {
            $this->field_value = $field_value;

        } elseif (is_null($field_value)) {
            $this->field_value = $field_value;

        } elseif (is_array($field_value)) {
            $this->field_value = $field_value;

        } elseif ($constraint === 'exception') {
            throw new Exception('Molajito Escape Molajo');

        } else {
            $this->field_value = strip_tags($field_value, $this->white_list);
        }

        $instance = new MockFieldHandlerResponse($this->field_value);

        return $instance;
    }

    public function format(
        $field_name,
        $field_value = null,
        $constraint,
        array $options = array()
    ) {
        if (is_numeric($field_value)) {
            return $field_value;

        } elseif (is_null($field_value)) {
            return $field_value;

        } elseif (is_array($field_value)) {
            return $field_value;
        }

        $this->field_value = strip_tags($field_value, $this->white_list);
    }
}

class MockFieldHandlerResponse
{
    public function __construct(
        $field_value
    ) {
        $this->field_value = $field_value;
    }

    protected $field_value = null;

    public function getFieldValue()
    {
        return $this->field_value;
    }
}
