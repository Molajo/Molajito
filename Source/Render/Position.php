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
        $result = $this->getPositionTemplates('page', $position_name, $resource_extension);

        if ($result === false) {
            $result = $this->getPositionTemplates('theme', $position_name, $resource_extension);
        }

        if (is_array($result)) {
            $templates = $result;
        } else {
            $templates   = array();
            $templates[] = $position_name;
        }

        return $this->createIncludeStatements($templates);
    }

    /**
     * Match to Positions defined in the Page or Theme Menuitem or Extension Parameters
     *
     * @param   string $type
     * @param   string $position_name
     * @param   object $resource_extension
     *
     * @return  boolean|array
     * @since   1.0
     */
    protected function getPositionTemplates($type, $position_name, $resource_extension)
    {
        $positions = '';

        if (isset($resource_extension->$type->parameters->positions)) {
            $positions = $resource_extension->$type->parameters->positions;

        } elseif (isset($resource_extension->$type->menuitem->parameters->positions)) {
            $positions = $resource_extension->$type->menuitem->parameters->positions;
        }

        if ($positions === null || trim($positions) === '') {
            return false;
        }

        $positions = $this->buildPositionArray($positions);

        if (count($positions) > 0) {
            return $this->searchPositionArray($position_name, $positions);
        }

        return false;
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

        if (count($temp) > 0) {
        } else {
            return array();
        }

        $positions_array = array();

        foreach ($temp as $field) {

            $remove_brackets         = substr(trim($field), 0, strlen($field) - 2);
            $position_template_array = explode('=', $remove_brackets);

            if (count($position_template_array) === 2) {
                $positions_array[strtolower($position_template_array[0])] = explode(',', $position_template_array[1]);
            }
        }

        return $positions_array;
    }

    /**
     * Search the Position Array
     *
     * @param   string $position_name
     * @param   array  $positions
     *
     * @return  boolean|array
     * @since   1.0
     */
    protected function searchPositionArray($position_name, array $positions = array())
    {
        $position_name = strtolower($position_name);

        if (isset($positions[$position_name])) {
            return $positions[$position_name];
        }

        return false;
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

        try {
            $escaped = $this->escape_instance->escape($data, null);

        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Molajito Position createIncludeStatements method failed: ' . $e->getMessage()
            );
        }

        return $escaped[0]->name;
    }
}
