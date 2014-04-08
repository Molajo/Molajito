<?php
/**
 * Proxy Class for Molajito Event Adapters
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito;

use CommonApi\Render\EventInterface;
use CommonApi\Exception\RuntimeException;
use Exception;

/**
 * Proxy Class for Molajito Event Adapters
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Event implements EventInterface
{
    /**
     * Event Adapter
     *
     * @var     object  CommonApi\Render\EventInterface
     * @since  1.0
     */
    protected $event_adapter = null;

    /**
     * Class Constructor
     *
     * @param   EventInterface $event_adapter
     *
     * @since   1.0
     */
    public function __construct(
        EventInterface $event_adapter
    ) {
        $this->event_adapter = $event_adapter;
    }

    /**
     * Initialise Options Array for Event
     *
     * @return  array
     * @since   1.0
     */
    public function initializeEventOptions()
    {
        try {
            return $this->event_adapter->initializeEventOptions();

        } catch (Exception $e) {
            throw new RuntimeException
            ('Render Driver initializeEventOptions Method Failed: ' . $e->getMessage());
        }
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
        try {
            return $this->event_adapter->scheduleEvent($event_name, $options);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Render Driver scheduleEvent Method Failed: ' . $e->getMessage());
        }
    }
}
