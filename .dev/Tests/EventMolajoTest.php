<?php
/**
 * Event Handler Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Event;
use Molajito\Event\Molajo;

/**
 * Event Handler Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class EventMolajoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $event_instance
     */
    protected $event_instance;

    /**
     * @var $event_option_keys
     */
    protected $event_option_keys
        = array(
            'exclude_tokens',
            'model_registry',
            'parameters',
            'plugin_data',
            'query_results',
            'query',
            'rendered_page',
            'rendered_view',
            'row',
            'runtime_data',
            'token_objects',
            'user'
        );

    /**
     * Instantiate Event Instance
     *
     * @covers  Molajito\Event::initializeEventOptions
     * @covers  Molajito\Event::scheduleEvent
     * @covers  Molajito\Event\Molajo::__construct
     * @covers  Molajito\Event\Molajo::initializeEventOptions
     * @covers  Molajito\Event\Molajo::scheduleEvent
     * @covers  Molajito\Event\Molajo::setEventOptions
     * @covers  Molajito\Event\AbstractAdapter::__construct
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers  Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     *
     */
    protected function setUp()
    {
        $event_callback = function ($event_name, array $options = array()) {
            return $options;
        };

        $dummy = new Molajo($event_callback, $this->event_option_keys);

        $this->event_instance = new Event($dummy);
    }

    /**
     * Test Initialise Event Options
     *
     * @covers  Molajito\Event::initializeEventOptions
     * @covers  Molajito\Event::scheduleEvent
     * @covers  Molajito\Event\Molajo::__construct
     * @covers  Molajito\Event\Molajo::initializeEventOptions
     * @covers  Molajito\Event\Molajo::scheduleEvent
     * @covers  Molajito\Event\Molajo::setEventOptions
     * @covers  Molajito\Event\AbstractAdapter::__construct
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers  Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     *
     * @return  $this
     * @since   1.0.0
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
     * @covers  Molajito\Event::initializeEventOptions
     * @covers  Molajito\Event::scheduleEvent
     * @covers  Molajito\Event\Molajo::__construct
     * @covers  Molajito\Event\Molajo::initializeEventOptions
     * @covers  Molajito\Event\Molajo::scheduleEvent
     * @covers  Molajito\Event\Molajo::setEventOptions
     * @covers  Molajito\Event\AbstractAdapter::__construct
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers  Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     *
     * @return  $this
     * @since   1.0.0
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
}
