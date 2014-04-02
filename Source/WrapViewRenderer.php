<?php
/**
 * Molajito Wrap View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\RenderInterface;
use Exception;

/**
 * Molajito Wrap View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class WrapViewRenderer implements RenderInterface
{
    /**
     * Render Instance
     *
     * @var    object   CommonApi\Render\RenderInterface
     * @since  1.0
     */
    protected $render_instance = null;

    /**
     * Path to Include File
     *
     * @var    string
     * @since  1.0
     */
    protected $include_path;

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data = null;

    /**
     * Row
     *
     * @var    object
     * @since  1.0
     */
    protected $row = null;

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_view = null;

    /**
     * Allowed Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'runtime_data',
        'rendered_view',
        'row'
    );

    /**
     * Constructor
     *
     * @param  RenderInterface $render_instance
     *
     * @since  1.0
     */
    public function __construct(
        RenderInterface $render_instance
    ) {
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
        $this->include_path = $include_path;

        $this->setProperties($data);

        $this->rendered_view = '';

        $this->renderViewHead();
        $this->renderViewBody();
        $this->renderViewFooter();

        return $this->rendered_view;
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
                $this->$key = null;
            }
        }

        return $this;
    }

    /**
     * Render Template View Head
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewHead()
    {
        $file_path = $this->include_path . '/Header.phtml';

        if (file_exists($file_path)) {
            $this->rendered_view = $this->renderOutput($file_path);
        }

        return $this;
    }

    /**
     * Render Template View Body
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewBody()
    {
        $file_path = $this->include_path . '/Body.phtml';

        if (file_exists($file_path)) {
            $this->rendered_view .= $this->renderOutput($file_path);
        }

        return $this;
    }

    /**
     * Render Template View Body
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewFooter()
    {
        $file_path = $this->include_path . '/Footer.phtml';

        if (file_exists($file_path)) {
            $this->rendered_view .= $this->renderOutput($file_path);
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
    protected function renderOutput($include_path)
    {
        if (file_exists($include_path)) {
        } else {
            throw new RuntimeException
            ('Molajito Wrap Renderer - rendering file not found: ' . $include_path);
        }

        try {
            return $this->render_instance->render(
                $include_path,
                $this->getProperties()
            );

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito WrapRenderer renderOutput Failed. '
            . ' File path: ' . $include_path . ' ' . $e->getMessage());
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
