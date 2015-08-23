<?php
/**
 * Molajito Render
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\RenderInterface;

/**
 * Molajito Renderer - performs actual rendering
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class Render implements RenderInterface
{
    /**
     * Plugin Data REMOVE
     *
     * @var    object
     * @since  1.0.0
     */
    protected $plugin_data;
// remove above - plugin data should NOT be in templates

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $runtime_data;

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0.0
     */
    protected $parameters = null;

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_results = array();

    /**
     * Single Row
     *
     * @var    object
     * @since  1.0.0
     */
    protected $row = null;

    /**
     * Include File
     *
     * @var    string
     * @since  1.0.0
     */
    protected $include_path = null;

    /**
     * Allowed Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'include_path',
            'plugin_data',  // remove
            'runtime_data',
            'parameters',
            'query_results',
            'row'
        );

    /**
     * Render output for specified file and data
     *
     * @param   array $data
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderOutput(array $data = array())
    {
        $this->setProperties($data);

        return $this->includeFile();
    }

    /**
     * Set class properties for input data
     *
     * @param   array $data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setProperties(array $data = array())
    {
        foreach ($data as $key => $value) {

            if (in_array($key, $this->property_array)) {
                $this->$key = $data[$key];
            }
        }

        return $this;
    }

    /**
     * Include rendering file
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function includeFile()
    {
        if (file_exists($this->include_path)) {
        } else {
            throw new RuntimeException(
                'Molajito Render - rendering file not found: ' . $this->include_path
            );
        }

        ob_start();
        include $this->include_path;
        $collect = ob_get_clean();

        return $collect;
    }
}
