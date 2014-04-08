<?php
/**
 * Proxy Class for Molajito View Adapters
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito;

use CommonApi\Render\ViewInterface;
use CommonApi\Exception\RuntimeException;
use Exception;

/**
 * Proxy Class for Molajito View Adapters
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class View implements ViewInterface
{
    /**
     * View Adapter
     *
     * @var     object  CommonApi\Render\ViewInterface
     * @since  1.0
     */
    protected $view_adapter = null;

    /**
     * Class Constructor
     *
     * @param   ViewInterface $view_adapter
     *
     * @since   1.0
     */
    public function __construct(
        ViewInterface $view_adapter
    ) {
        $this->view_adapter = $view_adapter;
    }

    /**
     * Get View required for Rendering
     *
     * @param   object $token
     *
     * @return  $this
     * @since   1.0
     */
    public function getView($token)
    {
        try {
            return $this->view_adapter->getView($token);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Render Driver getView Method Failed: ' . $e->getMessage());
        }
    }
}
