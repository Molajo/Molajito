<?php
/**
 * Molajito Escape Class Simple Adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Escape;

use CommonApi\Render\EscapeInterface;

/**
 * Molajito Escape Class Simple Adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class Simple extends AbstractAdapter implements EscapeInterface
{
    /**
     * Data
     *
     * @var    array
     * @since  1.0.0
     */
    protected $data = array();

    /**
     * Data
     *
     * @var    string
     * @since  1.0.0
     */
    protected $white_list = '<b><br><em><h1><h2><h3><h4><h5><h6><hr><i><img><li><ol><p><ul><strong>';

    /**
     * Constructor
     *
     * @param  null $white_list
     *
     * @since  1.0.0
     */
    public function __construct(
        $white_list = null
    ) {
        if ($white_list === null) {
        } else {
            $this->white_list = $white_list;
        }
    }

    /**
     * Simple Query Output prior to Rendering
     *
     * @param   array $data
     * @param   array $model_registry
     *
     * @return  array
     * @since   1.0.0
     */
    public function escapeOutput(array $data = array(), array $model_registry = array())
    {
        $this->data = parent::escapeOutput($data, $model_registry);

        return $this->data;
    }

    /**
     * Process Field - sanitize each value
     *
     * @param   string     $data_key
     * @param   null|mixed $data_value
     * @param   array      $fields
     *
     * @return  object
     * @since   1.0.0
     */
    protected function escapeDataElement($data_key, $data_value = null, array $fields = array())
    {
        if (is_numeric($data_value)) {
            return $data_value;
        }

        if (is_array($data_value)) {
            return $data_value;
        }

        if (is_string($data_value)) {
            return strip_tags($data_value, $this->white_list);
        }

        return null;
    }
}
