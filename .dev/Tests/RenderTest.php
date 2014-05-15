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
use stdClass;

/**
 * Render Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class RenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $render
     */
    protected $render;

    /**
     * Create Render Instance
     *
     * @covers  Molajito\Render\TemplateView::__construct
     */
    protected function setUp()
    {
        $this->render = new Render();
    }

    /**
     * No input data
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0
     */
    public function testRenderNoData()
    {
        $include_path = __DIR__ . '/Views/Include.phtml';

        ob_start();
        include $include_path;
        $collect = ob_get_clean();

        $results = $this->render->renderOutput($include_path, array());

        $this->assertEquals($collect, $results);

        return $this;
    }

    /**
     * Test Render
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
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
        $options['plugin_data']   = array();
        $options['runtime_data']  = array();
        $options['parameters']    = array();
        $options['query_results'] = array();
        $options['row']           = new stdClass();

        $results = $this->render->renderOutput($include_path, $options);

        $this->assertEquals($collect, $results);

        return $this;
    }

    /**
     * Test Render
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @expectedException        \CommonApi\Exception\RuntimeException
     * @expectedExceptionRequest Molajito Render - rendering file not found: /Users/amystephen/Sites/Molajo/Molajito/.dev/Tests/Views/DoesNotExist.phtml
     *
     * @return  $this
     * @since   1.0
     */
    public function testFileDoesNotExist()
    {
        $include_path = __DIR__ . '/Views/DoesNotExist.phtml';

        $this->render->renderOutput($include_path, array());

        return $this;
    }
}
