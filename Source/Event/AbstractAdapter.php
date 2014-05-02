<?php
/**
 * Abstract Adapter for Molajito Event Processing
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Event;

use CommonApi\Render\EventInterface;

/**
 * Abstract Adapter for Molajito Event Processing
 *
 * @package    Molajito
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class AbstractAdapter implements EventInterface
{
    /**
     * Schedule Event - anonymous function to event_callback method
     *
     * @var    callable
     * @since  1.0.0
     */
    protected $event_callback;

    /**
     * Event option keys
     *
     * @var    array
     * @since  1.0.0
     */
    protected $event_option_keys = array(
        'runtime_data',
        'plugin_data',
        'parameters',
        'model_registry',
        'query_results',
        'row',
        'rendered_view',
        'rendered_page'
    );

    /**
     * Constructor
     *
     * @param  callable $event_callback
     * @param  array    $event_option_keys
     *
     * @since  1.0.0
     */
    public function __construct(
        callable $event_callback = NULL,
        array $event_option_keys = array()
    ) {
        $this->event_callback = $event_callback;
        if (count($event_option_keys) > 0) {
            $this->event_option_keys = $event_option_keys;
        }
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
            $options[$key] = NULL;
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
    abstract public function scheduleEvent($event_name, array $options = array());
}
