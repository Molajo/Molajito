<?php
/**
 * Theme Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Theme;
use Molajito\Escape;
use Molajito\Escape\Simple;
use Molajito\Render;

/**
 * Theme Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ThemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $theme_instance
     */
    protected $theme_instance;

    /**
     * Create Theme Instance
     */
    protected function setUp()
    {
        /** Escape Instance */
        $simple = new Simple();
        $escape = new Escape($simple);

        /** Render Instance */
        $render = new Render();

        $this->theme_instance = new Theme($escape, $render);
    }

    /**
     * Initialise Event Options
     *
     * @return  $this
     * @since   1.0
     */
    public function testTheme()
    {
        $rendering_properties                  = array();
        $rendering_properties['query_results'] = 'a';
        $rendering_properties['row']           = 'b';
        $rendering_properties['runtime_data']  = 'c';

        $include_path = __DIR__ . '/Views/';

        ob_start();
        include $include_path . '/Index.phtml';
        $collect = ob_get_clean();

        $results = $this->theme_instance->render(
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
