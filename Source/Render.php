<?php
/**
 * Molajito Render
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\RenderInterface;

/**
 * Molajito Renderer - performs actual rendering
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Render implements RenderInterface
{
    /**
     * Plugin Data REMOVE
     *
     * @var    object
     * @since  1.0.0
     */
    protected $plugin_data;
// remove above

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
     * Query Results: for Custom.phtml files
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_results = array();

    /**
     * Single Row: Normal Header.phtml, Body.phtml, Footer.phtml files
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
            'plugin_data',
            'runtime_data',
            'parameters',
            'query_results',
            'row'
        );

    /**
     * Render output for specified file and data
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderOutput($include_path, array $data = array())
    {
        $this->setProperties($data);

        return $this->includeFile($include_path);
    }

    /**
     * Set class properties for input data
     *
     * @param   array $data
     *
     * @return  $this
     * @since   1.0
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
     * @param   string $include_path
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function includeFile($include_path)
    {
        $this->include_path = $include_path;

        if (file_exists($this->include_path)) {
        } else {
            throw new RuntimeException(
                'Molajito Render - rendering file not found: ' . $include_path
            );
        }

        ob_start();
        include $this->include_path;
        $collect = ob_get_clean();

        return $collect;
    }
}
