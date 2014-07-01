<?php
/**
 * Molajo Adapter for Molajito Event Processing
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Event;

use CommonApi\Render\EventInterface;

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
     */
    public function scheduleEvent($event_name, array $options = array())
    {
        $schedule_event = $this->event_callback;

        $temp = $this->setEventOptions($options);

        return $schedule_event($event_name, $temp);
    }

    /**
     * Set Event Options
     *
     * @param   array $options
     *
     * @return  array
     * @since   1.0
     */
    protected function setEventOptions(array $options = array())
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
