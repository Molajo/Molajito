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
    protected $render_array = array(
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

        $render_array = array_unique(array_merge($this->render_array, $this->event_option_keys));

        $this->setProperties($data, $render_array);

        if (file_exists($this->include_path . '/Custom.phtml')) {
            $this->renderViewCustom();
        } else {
            $this->renderLoop();
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

        return $this->renderViewPart(NULL, $file_path, FALSE);
    }

    /**
     * Render Template Views Loop
     *
     * @return  string
     * @since   1.0
     */
    public function renderLoop()
    {
        $total_rows          = count($this->query_results);
        $row_count           = 1;
        $first               = 1;
        $even_or_odd         = 'odd';
        $this->rendered_view = '';

        if (count($this->query_results) > 0) {
        } else {
            return $this;
        }

        foreach ($this->query_results as $this->row) {

            if ($row_count == $total_rows) {
                $last_row = 1;
            } else {
                $last_row = 0;
            }

            $this->row->row_count   = $row_count;
            $this->row->even_or_odd = $even_or_odd;
            $this->row->total_rows  = $total_rows;
            $this->row->last_row    = $last_row;
            $this->row->first       = $first;
            $temp                   = $this->escape_instance->escape(array($this->row), $this->model_registry);
            $this->row              = $temp[0];

            if ($first === 1) {
                $this->renderViewHead();
            }

            $this->renderViewBody();

            if ($last_row == 1) {
                $this->renderViewFooter();
            }

            if ($even_or_odd == 'odd') {
                $even_or_odd = 'even';
            } else {
                $even_or_odd = 'odd';
            }

            $row_count++;

            $first = 0;
        }

        return $this->rendered_view;
    }

    /**
     * Render Template View Head
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewHead()
    {
        return $this->renderViewPart('onBeforeRenderViewHead', '/Header.phtml', FALSE);
    }

    /**
     * Render Template View Body
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewBody()
    {
        return $this->renderViewPart('onBeforeRenderViewItem', '/Body.phtml', FALSE);
    }

    /**
     * Render Template View Body
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderViewFooter()
    {
        return $this->renderViewPart('onBeforeRenderViewFooter', '/Footer.phtml', FALSE);
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
    protected function renderViewPart($event = NULL, $file, $custom = FALSE)
    {
        if ($event === NULL) {
            $file_path = $file;

        } else {
            $options                   = $this->initializeEventOptions();
            $options['parameters']     = $this->parameters;
            $options['model_registry'] = $this->model_registry;
            $options['row']            = $this->row;
            $options['rendered_view']  = $this->rendered_view;
            $options['rendered_page']  = $this->rendered_page;

            $this->scheduleEvent($event, $options);

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

        if ($custom === FALSE) {
            $options['row'] = $this->row;
        } else {
            $options['query_results'] = $this->query_results;
        }

        try {
            $this->rendered_view .= $this->renderOutput($file_path, $options);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito TemplateView renderTemplateViewOutput: '
                . ' File path: ' . $file_path . 'Message: ' . $e->getMessage());
        }

        return $this;
    }
}
