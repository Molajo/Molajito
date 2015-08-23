<?php
/**
 * Molajito Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use stdClass;

/**
 * Molajito Template View Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class TemplateView extends Cache
{
    /**
     * Render output for specified file and data
     *
     * @param   array $data
     *
     * @return  string
     * @since   1.0.0
     */
    public function renderOutput(array $data = array())
    {
        $data['on_before_event'] = 'onBeforeRenderTemplate';
        $data['on_after_event']  = 'onAfterRenderTemplate';

        $this->initialise($data);
        $this->scheduleEvent($this->on_before_event);

//        if ($this->getTemplateViewCache() === true) {
//            return $this->rendered_view;
//        }

        $this->scheduleEvent('onGetTemplateData');
        $this->renderView('/Custom.phtml');
        $this->scheduleEvent($this->on_after_event);
//        $this->setViewCache();

        return $this->rendered_view;
    }

    /**
     * Get TemplateView Cache
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function getTemplateViewCache()
    {
        $this->getViewCache();

        if ($this->rendered_view === '') {
            return false;
        }

        return true;
    }

    /**
     * Render View
     *
     * @param   string $suffix
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function renderView($suffix)
    {
        $this->query_results = $this->escape_instance->escapeOutput($this->query_results, $this->model_registry);

        if (file_exists($this->include_path . $suffix)) {
            $this->renderViewCustom();

        } else {

            if ((int)$this->parameters->token->display_view_on_no_results === 0
                && $this->query_results[0] === null) {

            } else {

                $this->renderLoop();
            }
        }

        return $this;
    }

    /**
     * Render Template View Head
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function renderViewCustom()
    {
        $this->renderViewPart('/Custom.phtml', null);

        return $this;
    }

    /**
     * Render Template Views Loop
     *
     * @return  $this
     * @since   1.0.0
     */
    public function renderLoop()
    {
        $row_count   = 1;
        $even_or_odd = 'odd';
        $total_rows  = count($this->query_results);

        foreach ($this->query_results as $this->row) {

            $this->initializeRenderLoop($row_count, $even_or_odd, $total_rows);

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
     * @param   integer $total_rows
     *
     * @return  $this
     * @since   1.0.0
     */
    public function initializeRenderLoop($row_count, $even_or_odd, $total_rows)
    {
        $this->row->row_count   = $row_count;
        $this->row->even_or_odd = $even_or_odd;
        $this->row->total_rows  = $total_rows;

        if ($row_count === 1) {
            $this->row->first = 1;
        } else {
            $this->row->first = 0;
        }

        if ($row_count === $total_rows) {
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
     * @since   1.0.0
     */
    protected function renderViewNormal()
    {
        if ($this->row->first === 1) {
            $this->renderViewPart('/Header.phtml', 'onBeforeRenderTemplateHead');
        }

        $this->renderViewPart('/Body.phtml', 'onBeforeRenderTemplateItem');

        if ($this->row->last_row === 1) {
            $this->renderViewPart('/Footer.phtml', 'onBeforeRenderTemplateFooter');
        }

        return $this;
    }

    /**
     * Render View Part: Header, Body, Footer
     *
     * @param   string      $file
     * @param   null|string $event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function renderViewPart($file = null, $event = null)
    {
        $file_path = $this->include_path . $file;

        if (file_exists($file_path)) {
        } else {
            return $this;
        }

        if ($event === null) {
        } else {
            $options = $this->setRenderOptions();
            $this->scheduleEvent($event, $options);
        }

        $this->includeFile($file_path);

        return $this;
    }

    /**
     * Set Render View Options
     *
     * @param   boolean $custom
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setRenderOptions($custom = false)
    {
        $options                  = array();
        $options['parameters']    = $this->parameters;
        $options['plugin_data']   = $this->plugin_data;
        $options['rendered_view'] = $this->rendered_view;

        if ($custom === false) {
            $options['row']           = $this->row;
            $options['query_results'] = array();
        } else {
            $options['row']           = new stdClass();
            $options['query_results'] = $this->query_results;
        }

        return $options;
    }
}
