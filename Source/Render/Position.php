<?php
/**
 * Molajito Position Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Render;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\EscapeInterface;
use CommonApi\Render\PositionInterface;
use Exception;
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
        $template_array = $this->getPositionTemplates($type, $position_name, $resource_extension, 'Parameters');

        if (count($template_array) === 0) {
            $template_array = $this->getPositionTemplates($type, $position_name, $resource_extension, 'Menuitem');
        }

        return $template_array;
    }

    /**
     * Match to Positions defined in the Page or Theme Menuitem or Extension Parameters
     *
     * @param   string $type
     * @param   string $position_name
     * @param   object $resource_extension
     * @param   string $method_partial (Parameters or Menuitems)
     *
     * @return  array
     * @since   1.0
     */
    protected function getPositionTemplates($type, $position_name, $resource_extension, $method_partial)
    {
        $method = 'getPositionTemplates' . $method_partial;

        $positions = $this->$method($type, $resource_extension);

        $positions_array = $this->buildPositionArray($positions);

        return $this->searchPositionArray($position_name, $positions_array);
    }

    /**
     * Retrieve Position data from Parameters, if existing
     *
     * @param   string $type
     * @param   object $resource_extension
     *
     * @return  boolean|array
     * @since   1.0
     */
    protected function getPositionTemplatesParameters($type, $resource_extension)
    {
        if (isset($resource_extension->$type->parameters->positions)) {
            return $resource_extension->$type->parameters->positions;
        }

        return false;
    }

    /**
     * Retrieves all position data (unnmatched) from Menuitem, if existing
     *
     * @param   string $type
     * @param   object $resource_extension
     *
     * @return  boolean|array
     * @since   1.0
     */
    protected function getPositionTemplatesMenuitem($type, $resource_extension)
    {
        if (isset($resource_extension->$type->menuitem->parameters->positions)) {
            return $resource_extension->$type->menuitem->parameters->positions;
        }

        return false;
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

        $temp = explode('{{', $position);

        if (count($temp) > 0) {
            foreach ($temp as $field) {
                $positions_array = $this->getPositionTemplate($positions_array, $field);
            }
        }

        return $positions_array;
    }

    /**
     * Get a single Template from the Position Array
     *
     * @param   array  $positions_array
     * @param   string $field
     *
     * @return  array
     * @since   1.0
     */
    protected function getPositionTemplate($positions_array, $field)
    {
        $remove_brackets = substr(trim($field), 0, strlen($field) - 2);

        $template_array = explode('=', $remove_brackets);

        if (count($template_array) === 2) {
            $positions_array[ strtolower($template_array[0]) ]
                = explode(',', $template_array[1]);
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
