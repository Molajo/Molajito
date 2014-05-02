<?php
/**
 * Molajito Theme Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\RenderInterface;
use CommonApi\Render\EscapeInterface;
use Exception;

/**
 * Molajito Theme Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Theme implements RenderInterface
{
    /**
     * Escape Instance
     *
     * @var    object   CommonApi\Render\EscapeInterface
     * @since  1.0.0
     */
    protected $escape_instance = NULL;

    /**
     * Render Instance
     *
     * @var    object   CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $render_instance = NULL;

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $runtime_data = NULL;

    /**
     * Allowed Properties
     *
     * @var    object
     * @since  1.0.0
     */
    protected $property_array = array(
        'runtime_data'
    );

    /**
     * Constructor
     *
     * @param  EscapeInterface $escape_instance
     * @param  RenderInterface $render_instance
     *
     * @since  1.0.0
     */
    public function __construct(
        EscapeInterface $escape_instance,
        RenderInterface $render_instance
    ) {
        $this->escape_instance = $escape_instance;
        $this->render_instance = $render_instance;
    }

    /**
     * Render Theme output
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function render($include_path, array $data = array())
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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setProperties(array $data = array())
    {
        foreach ($this->property_array as $key) {
            if (isset($data[$key])) {
                $this->$key = $data[$key];
            } else {
                $this->$key = NULL;
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
        $include_path .= '/Index.phtml';

        if (file_exists($include_path)) {
        } else {
            throw new RuntimeException
            ('Molajito Theme Renderer - rendering file not found: ' . $include_path);
        }

        try {
            return $this->render_instance->render(
                $include_path,
                $this->getProperties()
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito Theme renderOutput: ' . $e->getMessage());
        }
    }

    /**
     * Set class properties for input data
     *
     * @return  array
     * @since   1.0
     */
    protected function getProperties()
    {
        $data = array();

        foreach ($this->property_array as $key) {
            $data[$key] = $this->$key;
        }

        return $data;
    }
}
