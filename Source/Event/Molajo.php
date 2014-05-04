<?php
/**
 * Molajo Adapter for Molajito Event Processing
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Event;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EventInterface;
use Exception;

/**
 * Molajo Adapter for Molajito Event Processing
 *
 * @package    Molajito
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Molajo extends AbstractAdapter implements EventInterface
{
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

        $temp = $this->setEventOptions($options);

        try {
            return $schedule_event($event_name, $temp);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito Event scheduleEvent Failure: ' . $e->getMessage()
            );
        }
    }
    /**
     * Set Event Options
     *
     * @param   array  $options
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setEventOptions(array $options = array())
    {
        $temp = array();

        foreach ($this->event_option_keys as $key) {
            if (isset($options[$key])) {
                $temp[$key] = $options[$key];
            }
        }

        return $temp;
    }
}
