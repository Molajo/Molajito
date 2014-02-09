<?php
/**
 * Event Handler Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\EventHandler;

/**
 * Event Handler Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class EventHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Object
     */
    protected $event_callback;

    /**
     * @var Object
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
     * @var Object
     */
    protected $event_handler;

    /**
     * Initialises Adapter
     */
    protected function setUp()
    {
        $this->event_callback = function ($event_name, array $options = array()) {

            $event_mock = new EventMock();

            return $event_mock->scheduleEvent($event_name, $options);
        };

        $this->event_handler = new EventHandler(
            $this->event_callback,
            $this->event_option_keys
        );
    }

    /**
     * Initialize Event Options
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function testInitializeEventOptions()
    {
        $options = $this->event_handler->initializeEventOptions();

        foreach ($this->event_option_keys as $event) {
            $this->assertEquals($options[$event], null);
        }

        return $this;
    }

    /**
     * Initialize Event Options
     *
     * @param   array $options
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
        $options    = $this->event_handler->scheduleEvent($event_name, $options);

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

/**
 * Mock Event Class
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0
 */
class EventMock
{
    /**
     * Mock
     *
     * @param   string $event_name
     * @param   array  $options
     *
     * @return  mixed
     * @since   1.0
     */
    public function scheduleEvent($event_name, $options)
    {
        return $options;
    }
}
