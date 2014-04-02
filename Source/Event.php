<?php
/**
 * Molajito Event Processing
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EventInterface;
use Exception;

/**
 * Molajito Event Handler
 *
 * @package    Molajito
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Event implements EventInterface
{
    /**
     * Schedule Event - anonymous function to event_callback method
     *
     * @var    callable
     * @since  1.0
     */
    protected $event_callback;

    /**
     * Event option keys
     *
     * @var    array
     * @since  1.0
     */
    protected $event_option_keys = array();

    /**
     * Constructor
     *
     * @param  callable $event_callback
     * @param  array    $event_option_keys
     *
     * @since  1.0
     */
    public function __construct(
        callable $event_callback = null,
        array $event_option_keys = array()
    ) {
        $this->event_callback    = $event_callback;
        $this->event_option_keys = $event_option_keys;
    }

    /**
     * Initialise Options Array for Event
     *
     * @return  array
     * @since   1.0
     */
    public function initializeEventOptions()
    {
        $options = array();

        foreach ($this->event_option_keys as $key) {
            $options[$key] = null;
        }

        return $options;
    }

    /**
     * Schedule Event
     *
     * @param   string $event_name
     * @param   array  $options
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function scheduleEvent($event_name, array $options = array())
    {
        $schedule_event = $this->event_callback;

        $temp = array();
        foreach ($this->event_option_keys as $key) {
            if (isset($options[$key])) {
                $temp[$key] = $options[$key];
            } else {
                $temp[$key] = null;
            }
        }

        try {
            return $schedule_event($event_name, $temp);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito Event scheduleEvent Failure: ' . $e->getMessage());
        }
    }
}
