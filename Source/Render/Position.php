<?php
/**
 * Molajito Position Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Render\RenderInterface;
use stdClass;

/**
 * Molajito Position Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class Position extends AbstractRenderer implements RenderInterface
{
    /**
     * Render output
     *
     * @param   array $data
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderOutput(array $data = array())
    {
        $data['on_before_event'] = 'onBeforeRenderPosition';
        $data['on_after_event']  = 'onAfterRenderPosition';

        $this->initialise($data);
        $this->scheduleEvent($this->on_before_event, array());
        $this->renderView('');
        $this->scheduleEvent($this->on_after_event, array());

        return $this->rendered_view;
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
        if (trim($this->parameters->token->name) === '') {
            return $this;
        }

        if (is_array($this->parameters->token->name)) {
            $templates = $this->parameters->token->name;
        } else {
            $templates = array($this->parameters->token->name);
        }

        if (count($templates) > 0) {
            $this->setTemplates($templates);
        }

        return $this;
    }

    /**
     * Set Templates
     *
     * @param   array $templates
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setTemplates(array $templates = array())
    {
        $attributes = $this->setAttributes();

        foreach ($templates as $template) {

            $template_name = $this->escapeTemplateName($template);

            if ($this->rendered_view === '') {
            } else {
                $this->rendered_view .= PHP_EOL;
            }

            $this->rendered_view .= '{I template=' . ucfirst(strtolower(trim($template_name)));

            if (trim($attributes) === '') {
            } else {
                $this->rendered_view .= ' ' . $attributes;
            }

            $this->rendered_view .= ' I} ';
        }

        return $this;
    }

    /**
     * Create Include Statements for Position Templates
     *
     * @param   string $template_name
     *
     * @return  string
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function escapeTemplateName($template_name)
    {
        $row       = new stdClass();
        $row->name = $template_name;
        $data      = array();
        $data[]    = $row;

        $escaped = $this->escape_instance->escapeOutput($data);

        return $escaped[0]->name;
    }

    /**
     * Set Attributes as String
     *
     * @return  string
     */
    protected function setAttributes()
    {
        $attributes = '';

        if (count($this->parameters->token->attributes) === 0) {
            return $attributes;
        }

        foreach ($this->parameters->token->attributes as $key => $value) {
            $attributes .= ' ' . $key . '=' . $value;
        }

        return trim($attributes);
    }
}
