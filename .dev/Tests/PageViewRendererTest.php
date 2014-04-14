<?php
/**
 * Page View Renderer Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\PageView;
use Molajito\Render;

/**
 * Pagination Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class PageViewTest extends \PHPUnit_Framework_TestCase
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
     * Initialise Event Options
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetResourceExtension()
    {
        /** Render Instance */
        $render = new Render();

        $rendering_properties                  = array();
        $rendering_properties['query_results'] = 'a';
        $rendering_properties['row']           = 'b';
        $rendering_properties['runtime_data']  = 'c';

        $include_path = __DIR__ . '/View/Include.phtml';

        ob_start();
        include $include_path;
        $collect = ob_get_clean();

        $this->render_view = new PageView($render);

        $results = $this->render_view->render($include_path,
            $rendering_properties);

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
