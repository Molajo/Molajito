<?php
/**
 * Molajito Engine Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Data;
use Molajito\Data\Molajo as MolajoData;
use Molajito\Event;
use Molajito\Event\Dummy;
use Molajito\Escape;
use Molajito\Escape\Simple;
use Molajito\Parse;
use Molajito\PageView;
use Molajito\Position;
use Molajito\Render;
use Molajito\TemplateView;
use Molajito\Theme;
use Molajito\View;
use Molajito\View\Molajo as MolajoView;
use Molajito\WrapView;
use Molajito\Engine;

/**
 * Molajito Engine Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class EngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  $engine
     */
    protected $engine;

    /**
     * @var  $event_option_keys
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
     * @var  $extension_resource
     */
    protected $extension_resource;

    /**
     * @var  $exclude_tokens
     */
    protected $exclude_tokens = array('exclude1');

    /**
     * Setup
     */
    protected function setUp()
    {
        /** $data_instance */
        $data_instance = new Data(new MolajoData());

        /** $view_instance */
        $view_instance = new View(new MolajoView(new ResourceMock2()));

        /** $event_instance */
        $event_callback = function ($event_name, array $options = array()) {
            return $options;
        };
        $dummy          = new Dummy($event_callback, $this->event_option_keys);
        $event_instance = new Event($dummy);

        /** $parse_instance */
        $parse_instance = new Parse();

        /** Reused */
        $simple = new Simple();
        $escape = new Escape($simple);
        $render = new Render();

        /** $position_instance */
        $position_instance = new Position($escape);

        /** $theme_instance */
        $theme_instance = new Theme($escape, $render);

        /** $page_instance */
        $page_instance = new PageView($render);

        /** $template_instance */
        $template_instance = new TemplateView($escape, $render, $event_instance, $this->event_option_keys);

        /** $wrap_instance */
        $wrap_instance = new WrapView($render);

        $this->engine = new Engine (
            $data_instance,
            $view_instance,
            $event_instance,
            $this->event_option_keys,
            $parse_instance,
            $this->exclude_tokens,
            $stop_loop_count = 100,
            $position_instance,
            $theme_instance,
            $page_instance,
            $template_instance,
            $wrap_instance
        );
    }

    /**
     * Test Theme
     *
     * @return  $this
     * @since   1.0
     */
    public function testTheme()
    {
        $data                  = array();
        $data['query_results'] = 'a';
        $data['row']           = 'b';
        $data['runtime_data']  = 'c';

        $include_path = __DIR__ . '/Views/';

        ob_start();
        include $include_path . '/Index.phtml';
        $collect = ob_get_clean();

        $results = $this->engine->render($include_path, $data);

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
 * Mock Resource Class
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ResourceMock2
{
    /**
     * Mock
     *
     * @param   string $event_name
     * @param   array  $options
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($request)
    {
        return $request;
    }
}

/**
 * Mock Event Class
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class EventMock3
{
    /**
     * Mock
     *
     * @param   string $event_name
     * @param   array  $options
     *
     * @return  mixed
     * @since   1.0
     */
    public function scheduleEvent($event_name, $options)
    {
        return $options;
    }
}
