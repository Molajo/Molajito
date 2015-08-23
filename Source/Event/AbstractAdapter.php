<?php
/**
 * Abstract Adapter for Molajito Event Processing
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Event;

use CommonApi\Render\EventInterface;

/**
 * Abstract Adapter for Molajito Event Processing
 *
 * @package    Molajito
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
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
    protected $event_option_keys
        = array(
            'exclude_tokens',
            'model_registry',
            'parameters',
            'plugin_data',
            'query_results',
            'rendered_page',
            'rendered_view',
            'row',
            'token_objects'
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
        callable $event_callback = null,
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
     * @since   1.0.0
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
     * @since   1.0.0
     */
    abstract public function scheduleEvent($event_name, array $options = array());
}
