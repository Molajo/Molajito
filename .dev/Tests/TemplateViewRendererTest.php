<?php
/**
 * Template View Renderer Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use stdClass;
use Molajito\TemplateViewRenderer;

/**
 * Pagination Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class TemplateViewRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Object
     */
    protected $render_view;

    /**
     * Initialises Adapter
     */
    protected function setUp()
    {
    }

    /**
     * Initialize Event Options
     *
     * @return  $this
     * @since   1.0
     */
    public function testCustomView()
    {
        $include_path = __DIR__ . '/CustomView';

        $event_option_keys = array(
            'runtime_data',
            'parameters',
            'query',
            'model_registry',
            'row',
            'rendered_view',
            'rendered_page'
        );

        $event_callback = function ($event_name, array $options = array()) {

            $event_mock = new EventMock2();

            return $event_mock->scheduleEvent($event_name, $options);
        };

        $rendering_properties                             = array();
        $rendering_properties['fieldhandler']             = null;
        $rendering_properties['date_controller']          = null;
        $rendering_properties['url_controller']           = null;
        $rendering_properties['language_controller']      = null;
        $rendering_properties['authorisation_controller'] = null;

        ob_start();
        include $include_path . '/Custom.phtml';
        $collect = ob_get_clean();

        $this->render_view = new TemplateViewRenderer(
            $include_path,
            $event_option_keys,
            $event_callback,
            $rendering_properties
        );

        $results = $this->render_view->render();

        $this->assertEquals($collect, $results);

        return $this;
    }

    /**
     * Initialize Event Options
     *
     * @return  $this
     * @since   1.0
     */
    public function testView()
    {
        $include_path = __DIR__ . '/View';

        $row             = new stdClass();
        $row->field      = 'value';
        $query_results   = array();
        $query_results[] = $row;

        $event_option_keys = array(
            'runtime_data',
            'parameters',
            'query_results',
            'model_registry',
            'row',
            'rendered_view',
            'rendered_page'
        );

        $event_callback = function ($event_name, array $options = array()) {

            $event_mock = new EventMock2();

            return $event_mock->scheduleEvent($event_name, $options);
        };

        $rendering_properties                             = array();
        $rendering_properties['fieldhandler']             = null;
        $rendering_properties['date_controller']          = null;
        $rendering_properties['url_controller']           = null;
        $rendering_properties['language_controller']      = null;
        $rendering_properties['authorisation_controller'] = null;
        $rendering_properties['query_results']            = $query_results;

        ob_start();
        include $include_path . '/Header.phtml';
        include $include_path . '/Body.phtml';
        include $include_path . '/Footer.phtml';
        $collect = ob_get_clean();

        $this->render_view = new TemplateViewRenderer(
            $include_path,
            $event_option_keys,
            $event_callback,
            $rendering_properties
        );

        $results = $this->render_view->render();

        $this->assertEquals($collect, $results);

        return $this;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}

/**
 * Mock Event Class
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0
 */
class EventMock2
{
    /**
     * Mock
     *
     * @param   string $event_name
     * @param   array  $rendering_properties
     *
     * @return  mixed
     * @since   1.0
     */
    public function scheduleEvent($event_name, $rendering_properties)
    {
        return $rendering_properties;
    }
}
