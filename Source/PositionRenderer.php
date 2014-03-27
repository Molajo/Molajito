<?php
/**
 * Molajito Position Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Render\RenderInterface;

/**
 * Molajito Position Renderer
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class PositionRenderer implements RenderInterface
{
    /**
     * Token
     *
     * @var    string
     * @since  1.0
     */
    protected $token;

    /**
     * Resource data
     *
     * @var    object
     * @since  1.0
     */
    protected $resource_extension = null;

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_view = null;

    /**
     * Constructor
     *
     * @param  string $token
     * @param  object $resource_extension
     *
     * @since  1.0
     */
    public function __construct(
        $token,
        $resource_extension
    ) {
        $this->token              = $token;
        $this->resource_extension = $resource_extension;
        $this->rendered_view      = '';
    }

    /**
     * Render Template View
     *
     * @return  string
     * @since   1.0
     */
    public function render()
    {
        $position_array = $this->getResourcePositions('page');

        if (is_array($position_array) && count($position_array) > 0) {
            return $this->renderView($position_array);
        }

        $position_array = $this->getResourcePositions('theme');

        if (is_array($position_array) && count($position_array) > 0) {
            return $this->renderView($position_array);
        }

        return $this->renderView(array($this->token->name));
    }

    /**
     * Match to Resource Positions
     *
     * @param   string $type
     *
     * @return  array
     * @since   1.0
     */
    protected function getResourcePositions($type)
    {
        $positions = '';

        if (isset($this->resource_extension->$type->parameters->positions)) {
            $positions = $this->resource_extension->$type->parameters->positions;
        }

        if (trim($positions) == ''
            && isset($this->resource_extension->$type->menuitem->parameters->positions)
        ) {
            $positions = $this->resource_extension->$type->menuitem->parameters->positions;

        }

        if (trim($positions) == '') {
            return array();
        }

        $position_array = $this->buildPositionArray($positions);

        if (is_array($position_array) && count($position_array) > 0) {
            return $this->searchPositionArray($position_array);
        }

        return array();
    }

    /**
     * Build the Position Array
     *
     * @param   string $positions
     *
     * @return  array
     * @since   1.0
     */
    protected function buildPositionArray($positions)
    {
        $temp = explode('{{', $positions);

        if (is_array($temp) && count($temp) > 0) {
        } else {
            return array();
        }

        $positions_array = array();

        foreach ($temp as $field) {
            if (trim($field) == '') {
            } else {
                $new_field = substr(trim($field), 0, strlen($field) - 2);
                $temp2     = explode('=', $new_field);
                if (is_array($temp2) && count($temp2) == 2) {
                    $templates = explode(',', $temp2[1]);
                    if (is_array($templates) && count($templates) > 0) {
                        $positions_array[$temp2[0]] = $templates;
                    }
                }
            }
        }

        return $positions_array;
    }

    /**
     * Search the Position Array
     *
     * @param   array $position_array
     *
     * @return  array
     * @since   1.0
     */
    protected function searchPositionArray($position_array)
    {
        if (isset($position_array[$this->token->name])) {
            return $position_array[$this->token->name];
        }

        return array();
    }

    /**
     * Render View
     *
     * @param   array $position_array
     *
     * @return  string
     * @since   1.0
     */
    protected function renderView(array $position_array = array())
    {
        foreach ($position_array as $template) {
            $this->rendered_view .= '<include template=' . ucfirst(strtolower(trim($template))) . '/> ';
        }

        return $this->rendered_view;
    }
}
