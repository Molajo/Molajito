<?php
/**
 * Abstract Data Adapter for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Data;

use CommonApi\Render\PaginationInterface;
use CommonApi\Render\DataInterface;

/**
 * Abstract Data Adapter for Molajito
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class AbstractAdapter implements DataInterface
{
    /**
     * Pagination Class
     *
     * @var    object
     * @since  1.0.0
     */
    protected $pagination = null;

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0.0
     */
    protected $parameters = null;

    /**
     * Class Constructor
     *
     * @param  PaginationInterface $pagination
     *
     * @since  1.0.0
     */
    public function __construct(
        PaginationInterface $pagination = null
    ) {
        $this->pagination = $pagination;
    }

    /**
     * Get Data for Rendering
     *
     * @param   object $token
     * @param   array  $options
     *
     * @return  object
     * @since   1.0
     */
    abstract public function getData($token, array $options = array());

    /**
     * Set Parameters from Token Attributes
     *
     * @param   object $token
     * @param   object $parameters
     *
     * @return  object
     * @since   1.0
     */
    protected function setParametersFromToken($token, $parameters)
    {
        if (count($token->attributes) > 0) {
            foreach ($token->attributes as $key => $value) {
                $parameters->$key = $value;
            }
        }

        return $parameters;
    }
}
