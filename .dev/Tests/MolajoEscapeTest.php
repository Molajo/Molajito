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
class MolajoEscapeTest extends \PHPUnit_Framework_TestCase
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
        'test_field'  => array('name' => 'test_field', 'type' => 'url')
        );

        $results = $this->escape_instance->escape($query_results, $model_registry);

        $this->assertEquals(null, $results[0]->test_field);

        return $this;
    }

    /**
     * Test Numeric Value without Model Registry
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
            'test_field'  => array('name' => 'test_field', 'type' => 'integer')
        );

        $results = $this->escape_instance->escape($query_results, $model_registry);

        $this->assertEquals(33, $results[0]->test_field);

        return $this;
    }

    /**
     * Test Array without Model Registry
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
            'test_field'  => array('name' => 'test_field', 'type' => 'array')
        );

        $results = $this->escape_instance->escape($query_results, $model_registry);

        $this->assertEquals(array(1, 2, 3), $results[0]->test_field);

        return $this;
    }

    /**
     * Test HTML without Model Registry
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
            'test_field'  => array('name' => 'test_field', 'type' => 'html')
        );

        $results = $this->escape_instance->escape($query_results, $model_registry);

        $this->assertEquals('<p>I am a dog.</p>', $results[0]->test_field);

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


class MockFieldHandler implements FieldhandlerInterface
{
    protected $white_list = '<b><em><i><img><p><u><strong>';

    public function validate(
        $field_name,
        $field_value = null,
        $fieldhandler_type_chain,
        $options = array())
    {

    }

    public function filter(
        $field_name,
        $field_value = null,
        $fieldhandler_type_chain,
        $options = array())
    {

    }

    public function escape(
        $field_name,
        $field_value = null,
        $fieldhandler_type_chain,
        $options = array() )
    {
        if (is_numeric($field_value)) {
            return $field_value;

        } elseif (is_null($field_value)) {
            return $field_value;

        } elseif (is_array($field_value)) {
            return $field_value;
        }

        return strip_tags($field_value, $this->white_list);
    }
}
