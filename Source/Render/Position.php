<?php
/**
 * Molajito Position Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Render\EscapeInterface;
use CommonApi\Render\PositionInterface;
use stdClass;

/**
 * Molajito Position Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Position implements PositionInterface
{
    /**
     * Escape Instance
     *
     * @var    object   CommonApi\Render\EscapeInterface
     * @since  1.0.0
     */
    protected $escape_instance = null;

    /**
     * Constructor
     *
     * @param  EscapeInterface $escape_instance
     *
     * @since  1.0.0
     */
    public function __construct(
        EscapeInterface $escape_instance
    ) {
        $this->escape_instance = $escape_instance;
    }

    /**
     * Retrieve all Template Views for Position searching Page, first, then Theme
     *
     * @param   string $position_name
     * @param   object $resource_extension
     *
     * @return  string
     * @since   1.0
     */
    public function getPositionTemplateViews($position_name, $resource_extension)
    {
        $template_array = $this->matchPositionTemplates('page', $position_name, $resource_extension);

        if (count($template_array) === 0) {
            $template_array = $this->matchPositionTemplates('theme', $position_name, $resource_extension);
        }

        if (count($template_array) === 0) {
            $template_array   = array();
            $template_array[] = $position_name;
        }

        return $this->createIncludeStatements($template_array);
    }

    /**
     * Match to Positions defined in the Page or Theme Menuitem or Extension Parameters
     *
     * @param   string $type
     * @param   string $position_name
     * @param   object $resource_extension
     *
     * @return  array
     * @since   1.0
     */
    protected function matchPositionTemplates($type, $position_name, $resource_extension)
    {
        $position_parameters = $this->getPositionParameters($type, $resource_extension);

        $template_array = array();

        if (count($position_parameters) > 0) {
            foreach ($position_parameters as $position_parameter) {

                $template_array = $this->getPositionTemplates($position_name, $position_parameter);

                if (count($template_array) > 0) {
                    break;
                }
            }
        }

        return $template_array;
    }

    /**
     * Get Position Parameters for Type
     *
     * @param   string $type
     * @param   object $resource_extension
     *
     * @return  array
     * @since   1.0
     */
    protected function getPositionParameters($type, $resource_extension)
    {
        $position_parameters = array();

        if (isset($resource_extension->$type->parameters->positions)) {
            $position_parameters[] = $resource_extension->$type->parameters->positions;
        }

        if (isset($resource_extension->$type->menuitem->parameters->positions)) {
            $position_parameters[] = $resource_extension->$type->menuitem->parameters->positions;
        }

        return $position_parameters;
    }

    /**
     * Match to Positions defined in the Page or Theme Menuitem or Extension Parameters
     *
     * @param   string $position_name
     * @param   string $position_parameter
     *
     * @return  array
     * @since   1.0
     */
    protected function getPositionTemplates($position_name, $position_parameter)
    {
        $positions_array = $this->buildPositionArray($position_parameter);

        return $this->searchPositionArray($position_name, $positions_array);
    }

    /**
     * Build the Position Array
     *
     * @param   string $position
     *
     * @return  array
     * @since   1.0
     */
    protected function buildPositionArray($position)
    {
        $positions_array = array();

        $position_templates = explode('{{', $position);

        if (count($position_templates) > 0) {
            $positions_array = $this->buildPositionTemplatesArray($position_templates);
        }

        return $positions_array;
    }

    /**
     * Build Position Templates array
     *
     * @param   array $position_templates
     *
     * @return  array
     * @since   1.0
     */
    protected function buildPositionTemplatesArray(array $position_templates = array())
    {
        $positions_array = array();

        foreach ($position_templates as $field) {

            $position_template = $this->getPositionTemplate($field);

            if (count($position_template) === 2) {
                $position       = $position_template['position'];
                $template_array = $position_template['templates'];

                $positions_array[ $position ] = $template_array;
            }
        }

        return $positions_array;
    }

    /**
     * Build Position Templates array
     *
     * @param   string $position_template
     *
     * @return  array
     * @since   1.0
     */
    protected function getPositionTemplate($position_template)
    {
        $remove_brackets = substr(trim($position_template), 0, strlen($position_template) - 2);

        $template_array = explode('=', $remove_brackets);

        if (count($template_array) === 2) {
            $position        = strtolower($template_array[0]);
            $template_array  = explode(',', $template_array[1]);
            $positions_array = array('position' => $position, 'templates' => $template_array);
        } else {
            $positions_array = array();
        }

        return $positions_array;
    }

    /**
     * Search the Position Array
     *
     * @param   string $position_name
     * @param   array  $positions
     *
     * @return  array
     * @since   1.0
     */
    protected function searchPositionArray($position_name, array $positions = array())
    {
        $position_name = strtolower($position_name);

        if (isset($positions[ $position_name ])) {
            return $positions[ $position_name ];
        }

        return array();
    }

    /**
     * Create Include Statements for Position Templates
     *
     * @param   array $templates
     *
     * @return  string
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function createIncludeStatements(array $templates = array())
    {
        $rendered_page = '';

        foreach ($templates as $template) {

            $template_name = $this->escapeTemplateName($template);

            if ($rendered_page === '') {
            } else {
                $rendered_page .= PHP_EOL;
            }

            $rendered_page .= '{I template=' . ucfirst(strtolower(trim($template_name))) . ' I} ';
        }

        return $rendered_page;
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
}
