<?php
/**
 * Molajito Escape Proxy
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Render\EscapeInterface;

/**
 * Molajito Escape Proxy
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class Escape implements EscapeInterface
{
    /**
     * Event Adapter
     *
     * @var     object  CommonApi\Render\EventInterface
     * @since  1.0.0
     */
    protected $escape_adapter = null;

    /**
     * Class Constructor
     *
     * @param   EscapeInterface $escape_adapter
     *
     * @since   1.0.0
     */
    public function __construct(
        EscapeInterface $escape_adapter
    ) {
        $this->escape_adapter = $escape_adapter;
    }

    /**
     * Escape Query Output prior to Rendering
     *
     * @param   array $data
     * @param   array $model_registry
     *
     * @return  array
     * @since   1.0.0
     */
    public function escapeOutput(array $data = array(), array $model_registry = array())
    {
        return $this->escape_adapter->escapeOutput($data, $model_registry);
    }
}
