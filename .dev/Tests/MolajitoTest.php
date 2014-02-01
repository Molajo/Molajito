<?php
/**
 * Pagination Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\ExtensionResource;
use Molajito\EventHandler;
use Molajito\Molajito;
use stdClass;

/**
 * Pagination Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class MolajitoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Object
     */
    protected $molajito;

    /**
     * @var Object
     */
    protected $event_handler;

    /**
     * @var Object
     */
    protected $event_option_keys = array(
        'runtime_data',
        'parameters',
        'query',
        'model_registry',
        'row',
        'rendered_view',
        'rendered_page'
    );

    /**
     * @var Object
     */
    protected $extension_resource;

    /**
     * @var array
     */
    protected $exclude_tokens = array('exclude1');

    /**
     * Setup
     */
    protected function setUp()
    {
        $event_callback = function ($event_name, array $options = array()) {
            $event_mock = new EventMock3();
            return $event_mock->scheduleEvent($event_name, $options);
        };

        $this->event_handler = new EventHandler(
            $event_callback,
            $this->event_option_keys
        );

        $this->extension_resource = new ExtensionResource(
            new stdClass(),
            $theme_id = 1,
            $page_view_id = 2,
            $template_view_id = 3,
            $wrap_view_id = 4
        );

        $runtime_data              = new stdClass();
        $runtime_data->plugin_data = new stdClass();
        $collection                = new stdClass();
        $collection->field_a       = 'a';
        $collection->field_b       = 'b';
        $collection->field_c       = 'c';
        $row                       = array();
        $row[]                     = $collection;

        $rendering_properties                             = array();
        $rendering_properties['resource']                 = null;
        $rendering_properties['fieldhandler']             = null;
        $rendering_properties['date_controller']          = null;
        $rendering_properties['url_controller']           = null;
        $rendering_properties['language_controller']      = null;
        $rendering_properties['authorisation_controller'] = null;

        $this->molajito = new Molajito (
            $this->exclude_tokens,
            $this->event_handler,
            $this->event_option_keys,
            $this->extension_resource,
            $stop_loop_count = 10,
            $include_path = $include_path = __DIR__ . '/View/Include.phtml',
            $page_view_id,
            $runtime_data,
            $rendering_properties
        );
    }

    /**
     * Initialize Event Options
     *
     * @return  $this
     * @since   1.0
     */
    public function testParse()
    {
/**
        $results = $this->molajito->parse();

        $this->assertEquals('page', $results[0]->type);
        $this->assertEquals('xyz', $results[0]->name);
        $this->assertEquals('', $results[0]->wrap);
        $this->assertEquals(array(), $results[0]->attributes);
*/
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
class EventMock3
{
    /**
     * Mock
     *
     * @param   string  $event_name
     * @param   array   $options
     *
     * @return  mixed
     * @since   1.0
     */
    public function scheduleEvent($event_name, $options)
    {
        return $options;
    }
}
