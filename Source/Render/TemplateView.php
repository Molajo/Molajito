<?php
/**
 * Molajito Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\RenderInterface;
use Exception;

/**
 * Molajito Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class TemplateView extends AbstractRenderer implements RenderInterface
{
    /**
     * Render Properties
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'plugin_data',
            'runtime_data',
            'model_registry',
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
     */
    public function render($include_path, array $data = array())
    {
        $this->rendered_view = '';

        $this->include_path = $include_path;

        $this->setProperties($data, $this->property_array);

        if (file_exists($this->include_path . '/Custom.phtml')) {
            $this->renderViewCustom();
        } else {
            if (count($this->query_results) > 0) {
                $this->renderLoop();
            }
        }

        return $this->rendered_view;
    }

    /**
     * Render Template View Head
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewCustom()
    {
        $this->query_results = $this->escape_instance->escape($this->query_results, $this->model_registry);
        $file_path           = $this->include_path . '/Custom.phtml';

        return $this->renderViewPart(null, $file_path, false);
    }

    /**
     * Render Template Views Loop
     *
     * @return  $this
     * @since   1.0
     */
    public function renderLoop()
    {
        $row_count   = 1;
        $even_or_odd = 'odd';

        foreach ($this->query_results as $this->row) {

            $this->row->row_count   = $row_count;
            $this->row->even_or_odd = $even_or_odd;
            $this->row->total_rows  = count($this->query_results);

            if ($row_count === 1) {
                $this->row->first = 1;
            } else {
                $this->row->first = 0;
            }

            if ($row_count === count($this->query_results)) {
                $this->row->last_row = 1;
            } else {
                $this->row->last_row = 0;
            }

            $this->renderViewNormal();

            if ($even_or_odd == 'odd') {
                $even_or_odd = 'even';
            } else {
                $even_or_odd = 'odd';
            }

            $row_count++;
        }

        return $this;
    }

    /**
     * Render Normal Template
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewNormal()
    {
        $temp      = $this->escape_instance->escape(array($this->row), $this->model_registry);
        $this->row = $temp[0];

        if ($this->row->first === 1) {
            $this->renderViewPart('onBeforeRenderViewHead', '/Header.phtml', false);
        }

        $this->renderViewPart('onBeforeRenderViewItem', '/Body.phtml', false);

        if ($this->row->last_row === 1) {
            $this->renderViewPart('onBeforeRenderViewFooter', '/Footer.phtml', false);
        }

        return $this;
    }

    /**
     * Render View Part: Header, Body, Footer
     *
     * @param   null|string $event
     * @param   string      $file
     * @param   boolean     $custom
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function renderViewPart($event = null, $file, $custom = false)
    {
        if ($event === null) {
            $file_path = $file;

        } else {
            $this->scheduleEvent($event);
            $file_path = $this->include_path . $file;
        }

        if (file_exists($file_path)) {
        } else {
            return $this;
        }

        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['parameters']   = $this->parameters;
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        if ($custom === false) {
            $options['row'] = $this->row;
        } else {
            $options['query_results'] = $this->query_results;
        }

        try {
            $this->rendered_view .= $this->renderOutput($file_path, $options);

        } catch (Exception $e) {
            throw new RuntimeException(
                'Molajito TemplateView renderTemplateViewOutput: '
                . ' File path: ' . $file_path . 'Message: ' . $e->getMessage()
            );
        }

        return $this;
    }
}
