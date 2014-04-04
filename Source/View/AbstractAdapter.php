<?php
/**
 * Abstract View Adapter for Molajito Package
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\View;

use CommonApi\Render\ViewInterface;

/**
 * Abstract View Adapter for Render
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
abstract class AbstractAdapter implements ViewInterface
{
    /**
     * Get View required for Rendering
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     */
    abstract public function getView($token);
}
