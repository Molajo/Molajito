<?php
/**
 * Molajito Escape Proxy
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EscapeInterface;
use Exception;

/**
 * Molajito Escape Proxy
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Escape implements EscapeInterface
{
    /**
     * Event Adapter
     *
     * @var     object  CommonApi\Render\EventInterface
     * @since  1.0.0
     */
    protected $escape_adapter = NULL;

    /**
     * Class Constructor
     *
     * @param   EscapeInterface $escape_adapter
     *
     * @since   1.0
     */
    public function __construct(
        EscapeInterface $escape_adapter
    ) {
        $this->escape_adapter = $escape_adapter;
    }

    /**
     * Escape Query Output prior to Rendering
     *
     * @param   array  $data
     * @param   object $model_registry
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function escape(array $data = array(), $model_registry)
    {
        try {
            return $this->escape_adapter->escape($data, $model_registry);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Escape Driver escape Method Failed: ' . $e->getMessage()
            );
        }
    }
}
