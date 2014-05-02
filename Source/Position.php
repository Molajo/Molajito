<?php
/**
 * Molajito Position Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

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
    protected $escape_instance = NULL;

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
        $templates = $this->getPositionTemplates('page', $position_name, $resource_extension);

        if (is_array($templates) && count($templates) > 0) {
        } else {
            $templates = $this->getPositionTemplates('theme', $position_name, $resource_extension);
        }

        if (is_array($templates) && count($templates) > 0) {
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
     * @return  string
     * @since   1.0
     */
    protected function getPositionTemplates($type, $position_name, $resource_extension)
    {
        $positions = '';

        if (isset($resource_extension->$type->menuitem->parameters->positions)) {
            $positions = $resource_extension->$type->menuitem->parameters->positions;
        }

        if (isset($resource_extension->$type->parameters->positions)) {
            $positions = $resource_extension->$type->parameters->positions;
        }

        if ($positions === NULL || trim($positions) == '') {
            return '';
        }

        $positions = $this->buildPositionArray($positions);

        if (is_array($positions) && count($positions) > 0) {
            return $this->searchPositionArray($position_name, $positions);
        }

        return '';
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
                        $positions_array[strtolower($temp2[0])] = $templates;
                    }
                }
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
     * @return  array
     * @since   1.0
     */
    protected function searchPositionArray($position_name, array $positions = array())
    {
        $position_name = strtolower($position_name);

        if (isset($positions[$position_name])) {
            return $positions[$position_name];
        }

        return array();
    }

    /**
     * Create Include Statements for Position Templates
     *
     * @param   array $templates
     *
     * @return string
     * @throws \CommonApi\Exception\RuntimeException
     */
    protected function createIncludeStatements(array $templates = array())
    {
        $rendered_page = '';

        foreach ($templates as $template) {

            if ($rendered_page == '') {
            } else {
                $rendered_page .= PHP_EOL;
            }

            /** Escape Template Names */
            $row       = new stdClass();
            $row->name = $template;
            $data      = array();
            $data[]    = $row;

            try {
                $escaped = $this->escape_instance->escape($data, NULL);

            } catch (Exception $e) {
                throw new RuntimeException
                ('Molajito Position createIncludeStatements method failed: ' . $e->getMessage());
            }

            $template = $escaped[0]->name;

            /** Create Rendered Output */
            $rendered_page .= '{I template=' . ucfirst(strtolower(trim($template))) . ' I} ';
        }

        return $rendered_page;
    }
}
