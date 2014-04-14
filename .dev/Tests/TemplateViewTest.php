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
use Molajito\Escape;
use Molajito\Escape\Simple;
use Molajito\Event;
use Molajito\Event\Dummy;
use Molajito\Render;
use Molajito\TemplateView;

/**
 * Template View Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class TemplateViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Object
     */
    protected $template_view;

    /**
     * Initialises Adapter
     */
    protected function setUp()
    {
        /** Escape Instance */
        $simple = new Simple();
        $escape = new Escape($simple);

        /** Render Instance */
        $render = new Render();

        /** Event Instance */
        $dummy = new Dummy();
        $event = new Event($dummy);

        /** $event_option_keys */
        $event_option_keys = array(
            'runtime_data',
            'parameters',
            'query',
            'model_registry',
            'row',
            'rendered_view',
            'rendered_page'
        );

        $this->template_view = new TemplateView(
            $escape,
            $render,
            $event,
            $event_option_keys
        );
    }

    /**
     * Initialise Event Options
     *
     * @return  $this
     * @since   1.0
     */
    public function testCustomView()
    {
        $data = array();

        $data['query_results']   = array();
        $row                     = new stdClass();
        $row->id                 = 0;
        $row->title              = 'I am a title';
        $row->content_text       = '<p>I am a paragraph</p>';
        $data['query_results'][] = $row;

        $data['parameters'] = array();

        $data['model_registry'] = array(
            'id'           => array('name' => 'id', 'type' => 'integer'),
            'title'        => array('name' => 'title', 'type' => 'string'),
            'content_text' => array('name' => 'content_text', 'type' => 'html')
        );

        $include_path = __DIR__ . '/CustomView';

        ob_start();
        include $include_path . '/Custom.phtml';
        $collect = ob_get_clean();

        $results = $this->template_view->render($include_path, $data);

        $this->assertEquals($collect, $results);

        return $this;
    }


    /**
     * Initialise Event Options
     *
     * @return  $this
     * @since   1.0
     */
    public function testHFBView()
    {
        $data = array();

        $data['query_results']   = array();
        $row                     = new stdClass();
        $row->id                 = 0;
        $row->title              = 'I am a title';
        $row->content_text       = '<p>I am a paragraph</p>';
        $data['query_results'][] = $row;

        $data['parameters'] = array();

        $data['model_registry'] = array(
            'id'           => array('name' => 'id', 'type' => 'integer'),
            'title'        => array('name' => 'title', 'type' => 'string'),
            'content_text' => array('name' => 'content_text', 'type' => 'html')
        );

        $include_path = __DIR__ . '/Views';

        ob_start();
        include $include_path . '/Header.phtml';
        include $include_path . '/Body.phtml';
        include $include_path . '/Footer.phtml';
        $collect = ob_get_clean();

        $results = $this->template_view->render($include_path, $data);

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
