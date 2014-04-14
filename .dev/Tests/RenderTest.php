<?php
/**
 * Render Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Render;

/**
 * Pagination Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class RenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Object
     */
    protected $render;

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
    public function testRender()
    {
        $include_path = __DIR__ . '/Views/Include.phtml';

        ob_start();
        include $include_path;
        $collect = ob_get_clean();

        $options                  = array();
        $options['query_results'] = array();

        $this->render = new Render();

        $results = $this->render->render($include_path, $options);

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
