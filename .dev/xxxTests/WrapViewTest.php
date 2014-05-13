<?php
/**
 * Wrap View Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Render;
use Molajito\WrapView;

/**
 * Wrap View Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class WrapViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $wrap_instance
     */
    protected $wrap_instance;

    /**
     * Create Wrap Instance
     */
    protected function setUp()
    {
        $render              = new Render();
        $this->wrap_instance = new WrapView($render);
    }

    /**
     * Test Wrap Rendering
     *
     * @return  $this
     * @since   1.0
     */
    public function testWrapRender()
    {
        $rendering_properties                  = array();
        $rendering_properties['query_results'] = 'a';
        $rendering_properties['row']           = 'b';
        $rendering_properties['runtime_data']  = 'c';

        $include_path = __DIR__ . '/Views';

        ob_start();
        include $include_path . '/Header.phtml';
        include $include_path . '/Body.phtml';
        include $include_path . '/Footer.phtml';
        $collect = ob_get_clean();

        $results = $this->wrap_instance->renderOutput(
            $include_path,
            $rendering_properties
        );

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
