<?php
/**
 * Event Handler Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Event;
use Molajito\Event\Dummy;

/**
 * Event Handler Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $event_instance
     */
    protected $event_instance;

    /**
     * @var $event_option_keys
     */
    protected $event_option_keys = array(
        'runtime_data',
        'parameters',
        'query',
        'model_registry',
        'row',
        'rendered_view',
        'rendered_page'
    );

    /**
     * Instantiate Event Instance
     */
    protected function setUp()
    {
        $event_callback = function ($event_name, array $options = array()) {

            return $options;
        };

        $dummy = new Dummy($event_callback, $this->event_option_keys);

        $this->event_instance = new Event($dummy);
    }

    /**
     * Test Initialise Event Options
     *
     * @return  $this
     * @since   1.0
     */
    public function testInitialiseEventOptions()
    {
        $options = $this->event_instance->initializeEventOptions();

        foreach ($this->event_option_keys as $event) {
            $this->assertEquals($options[$event], null);
        }

        return $this;
    }

    /**
     * Test Schedule Event
     *
     * @return  $this
     * @since   1.0
     */
    public function testScheduleEvent()
    {
        $options = array();

        foreach ($this->event_option_keys as $event) {
            $options[$event] = $event;
        }

        $event_name = 'test';
        $options    = $this->event_instance->scheduleEvent($event_name, $options);

        foreach ($options as $key => $value) {
            $this->assertEquals($key, $value);
        }

        foreach ($this->event_option_keys as $event) {
            $this->assertEquals($options[$event], $event);
        }

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
