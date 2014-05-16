<?php
/**
 * Molajito Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Render\RenderInterface;

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
     * Render output for specified file and data
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     */
    public function renderOutput($include_path, array $data = array())
    {
        $this->setProperties($data, $this->property_array);

        $this->include_path = $include_path;

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
        $this->query_results = $this->escape_instance->escapeOutput(
            $this->query_results,
            $this->model_registry
        );

        $file_path = $this->include_path . '/Custom.phtml';

        return $this->renderViewPart($file_path, null, true);
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

            $this->initializeRenderLoop($row_count, $even_or_odd);

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
     * Initialize Render Loop
     *
     * @param   integer $row_count
     * @param   string  $even_or_odd
     *
     * @return  $this
     * @since   1.0
     */
    public function initializeRenderLoop($row_count, $even_or_odd)
    {
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
        $temp      = $this->escape_instance->escapeOutput(array($this->row), $this->model_registry);
        $this->row = $temp[0];

        if ($this->row->first === 1) {
            $this->renderViewPart('/Header.phtml', 'onBeforeRenderViewHead', false);
        }

        $this->renderViewPart('/Body.phtml', 'onBeforeRenderViewItem', false);

        if ($this->row->last_row === 1) {
            $this->renderViewPart('/Footer.phtml', 'onBeforeRenderViewFooter', false);
        }

        return $this;
    }

    /**
     * Render View Part: Header, Body, Footer
     *
     * @param   string      $file
     * @param   null|string $event
     * @param   boolean     $custom
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewPart($file, $event = null, $custom = false)
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

        $options = $this->setRenderViewOptions($custom);

        $this->rendered_view .= $this->render_instance->renderOutput($file_path, $options);

        return $this;
    }

    /**
     * Set Render View Options
     *
     * @param   boolean $custom
     *
     * @return  $this
     * @since   1.0
     */
    protected function setRenderViewOptions($custom = false)
    {
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

        return $options;
    }
}
