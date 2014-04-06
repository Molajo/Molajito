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

/**
 * Dummy Adapter for Molajito Event Processing
 *
 * @package    Molajito
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Dummy extends AbstractAdapter implements EventInterface
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
        $temp = array();
        foreach ($this->event_option_keys as $key) {
            if (isset($options[$key])) {
                $temp[$key] = $options[$key];
            } else {
                $temp[$key] = null;
            }
        }

        return $temp;
    }
}
