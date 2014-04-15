<?php
/**
 * Page View Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\PageView;
use Molajito\Render;

/**
 * Page View Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class PageViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $pageview_instance
     */
    protected $pageview_instance;

    /**
     * Create PageView Instance
     */
    protected function setUp()
    {
        $this->pageview_instance = new PageView(new Render());
    }

    /**
     * Test Page View
     *
     * @return  $this
     * @since   1.0
     */
    public function testPageView()
    {
        $rendering_properties                  = array();
        $rendering_properties['query_results'] = 'a';
        $rendering_properties['row']           = 'b';
        $rendering_properties['runtime_data']  = 'c';

        $include_path = __DIR__ . '/Views/Include.phtml';

        ob_start();
        include $include_path;
        $collect = ob_get_clean();

        $results = $this->pageview_instance->render(
            $include_path,
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
