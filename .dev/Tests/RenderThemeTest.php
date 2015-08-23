<?php
/**
 * Render Theme Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Render\Theme;
use Molajito\Escape;
use Molajito\Escape\Simple;
use Molajito\Render;
use stdClass;

/**
 * Render Theme Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class RenderThemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $theme_instance
     */
    protected $theme_instance;

    /**
     * Create Template View Instance
     *
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape::__construct
     *
     * @covers  Molajito\Render\AbstractRenderer::__construct
     *
     * @covers  Molajito\Event\Dummy::__construct
     * @covers  Molajito\Event::__construct
     *
     * @covers  Molajito\Data\AbstractAdapter::__construct
     * @covers  Molajito\Data\Molajo::__construct
     * @covers  Molajito\Data::__construct
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View::__construct
     *
     * @covers  Molajito\Render\Theme::__construct
     */
    protected function setUp()
    {
        /** Escape Instance */
        $simple = new Simple();
        $escape = new Escape($simple);

        /** Render Instance */
        $render = new Render();

        /** Event */
        $class   = 'Molajito\\Event\\Dummy';
        $adapter = new $class();
        $class   = 'Molajito\\Event';
        $event   = new $class($adapter);

        /** Theme Instance */
        $this->theme_instance = new Theme($escape, $render, $event);
    }

    /**
     * Theme
     *
     * @covers  Molajito\Event::initializeEventOptions
     * @covers  Molajito\Event::scheduleEvent
     * @covers  Molajito\Event\Dummy::initializeEventOptions
     * @covers  Molajito\Event\Dummy::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers  Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @covers  Molajito\Render\Theme::renderOutput
     * @covers  Molajito\Render\Theme::setProperties
     * @covers  Molajito\Render\Theme::includeFile
     *
     * @covers  Molajito\Render\AbstractRenderer::renderOutput
     * @covers  Molajito\Render\AbstractRenderer::setProperties
     * @covers  Molajito\Render\AbstractRenderer::getProperties
     * @covers  Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::includeFile
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testTheme()
    {
        $include_path = __DIR__ . '/Views/';

        ob_start();
        include $include_path . '/Index.phtml';
        $collect = ob_get_clean();

        $runtime_data            = new stdClass();
        $runtime_data->page_name = 'Test';
        $data                    = array();
        $data['runtime_data']    = $runtime_data;

        $results = $this->theme_instance->renderOutput(
            $include_path,
            $data
        );

        $this->assertEquals($collect, $results);

        return $this;
    }

    /**
     * Theme
     *
     * @covers                   Molajito\Event::initializeEventOptions
     * @covers                   Molajito\Event::scheduleEvent
     * @covers                   Molajito\Event\Dummy::initializeEventOptions
     * @covers                   Molajito\Event\Dummy::scheduleEvent
     * @covers                   Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers                   Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers                   Molajito\Event\AbstractAdapter::initializeEventOptions
     *
     * @covers                   Molajito\Escape::__construct
     * @covers                   Molajito\Escape::escapeOutput
     * @covers                   Molajito\Escape\Simple::__construct
     * @covers                   Molajito\Escape\Simple::escapeOutput
     * @covers                   Molajito\Escape\Simple::escapeDataElement
     * @covers                   Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers                   Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @covers                   Molajito\Render\Theme::renderOutput
     * @covers                   Molajito\Render\Theme::setProperties
     * @covers                   Molajito\Render\Theme::includeFile
     *
     * @covers                   Molajito\Render\AbstractRenderer::renderOutput
     * @covers                   Molajito\Render\AbstractRenderer::setProperties
     * @covers                   Molajito\Render\AbstractRenderer::getProperties
     * @covers                   Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers                   Molajito\Render\AbstractRenderer::setEventOptions
     * @covers                   Molajito\Render\AbstractRenderer::setEventOptions
     * @covers                   Molajito\Render\AbstractRenderer::includeFile
     *
     * @covers                   Molajito\Render::renderOutput
     * @covers                   Molajito\Render::setProperties
     * @covers                   Molajito\Render::includeFile
     *
     * @expectedException        \CommonApi\Exception\RuntimeException
     * @expectedExceptionRequest Molajito Theme Renderer - rendering file not found: /Users/amystephen/Sites/Molajo/Molajito/.dev/Tests/NotFound//Index.phtml
     *
     * @return  $this
     * @since                    1.0
     */
    public function testThemeException()
    {
        $rendering_properties                  = array();
        $rendering_properties['query_results'] = 'a';
        $rendering_properties['row']           = 'b';
        $rendering_properties['runtime_data']  = 'c';

        $include_path = __DIR__ . '/NotFound/';

        $this->theme_instance->renderOutput(
            $include_path,
            $rendering_properties
        );

        return $this;
    }
}
