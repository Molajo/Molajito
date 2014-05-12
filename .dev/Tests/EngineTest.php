<?php
/**
 * Molajito Engine Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

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
    protected $event_option_keys
        = array(
            'runtime_data',
            'parameters',
            'query',
            'model_registry',
            'row',
            'rendered_view',
            'rendered_page'
        );

    /**
     * @var  $exclude_tokens
     */
    protected $exclude_tokens = array('exclude1');

    /**
     * @var  $stop_loop_count
     */
    protected $stop_loop_count = 100;

    /**
     * @covers Molajito\Escape\Simple::__construct
     * @covers Molajito\Escape::__construct
     * @covers Molajito\Event\AbstractAdapter::__construct
     * @covers Molajito\Event\Dummy::__construct
     * @covers Molajito\Event::__construct
     * @covers Molajito\Data\AbstractAdapter::__construct
     * @covers Molajito\Data\Molajo::__construct
     * @covers Molajito\Data::__construct
     * @covers Molajito\View\Filesystem::__construct
     * @covers Molajito\View::__construct
     * @covers Molajito\Render\AbstractRenderer::__construct
     * @covers Molajito\Render\Theme::__construct
     * @covers Molajito\Render\Position::__construct
     * @covers Molajito\Render\PageView::__construct
     * @covers Molajito\Render\TemplateView::__construct
     * @covers Molajito\Render\WrapView::__construct
     * @covers Molajito\Render\Token::__construct
     * @covers Molajito\Translate\StringArrayAdapter::__construct
     * @covers Molajito\Translate::__construct
     * @covers Molajito\Engine::__construct
     *
     * Setup
     */
    protected function setUp()
    {
        /** Escape */
        $class = 'Molajito\\Escape\\Simple';
        $simple = new $class();
        $class = 'Molajito\\Escape';
        $escape = new $class($simple);

        /** Render */
        $class = 'Molajito\\Render';
        $render = new $class();

        /** Event */
        $class = 'Molajito\\Event\\Dummy';
        $adapter = new $class();
        $class = 'Molajito\\Event';
        $event = new $class($adapter);

        /** Data */
        $class = 'Molajito\\Data\\Molajo';
        $adapter = new $class($pagination = null);
        $class = 'Molajito\\Data';
        $data = new $class($adapter);

        /** View */
        $theme_base_folder = $include_path = __DIR__ . '/Views/';
        $view_base_folder = $include_path = __DIR__ . '/Views/';

        $class = 'Molajito\\View\\Filesystem';
        $adapter = new $class($theme_base_folder, $view_base_folder);
        $class = 'Molajito\\View';
        $view = new $class($adapter);

        /** Render Classes */
        $class = 'Molajito\\Render\\Theme';
        $theme = new $class($escape, $render, $event);
        $class = 'Molajito\\Render\\Position';
        $position = new $class($escape, $render, $event);
        $class = 'Molajito\\Render\\PageView';
        $page = new $class($escape, $render, $event);
        $class = 'Molajito\\Render\\TemplateView';
        $template = new $class($escape, $render, $event);
        $class = 'Molajito\\Render\\WrapView';
        $wrap = new $class($escape, $render, $event);

        $class = 'Molajito\\Render\\Token';
        $token = new $class ($escape, $render, $event, $data, $view, $theme, $position, $page, $template, $wrap);

        /** Translate - with Escape */
        $class    = 'Molajito\\Translate\\StringArrayAdapter';
        $adapter = new $class ($escape, $parse_mask = null, $model_registry = array(), $language_strings = array());
        $class    = 'Molajito\\Translate';
        $translate = new $class($adapter);

        /** Parse */
        $class = 'Molajito\\Parse';
        $parse    = new $class($rendered_page = null, $exclude_tokens = array(), $parse_mask = null);

        $this->engine = new Engine($token, $translate, $parse, $this->exclude_tokens, $this->stop_loop_count);
    }

    /**
     * @covers Molajito\Engine::renderOutput
     * @covers Molajito\Engine::renderLoop
     * @covers Molajito\Engine::parseTokens
     * @covers Molajito\Engine::parseTokens
     * @covers Molajito\Engine::parseTokens
     * @covers Molajito\Render\Token::renderTheme
     * @covers Molajito\Render\Token::initialiseData
     * @covers Molajito\Render\Token::renderPosition
     * @covers Molajito\Render\Token::renderToken
     * @covers Molajito\Render\Theme::renderOutput
     * @covers Molajito\Render\Theme::setProperties
     * @covers Molajito\Render\Theme::includeFile
     * @covers Molajito\Render\AbstractRenderer::renderOutput
     * @covers Molajito\Render\AbstractRenderer::setProperties
     * @covers Molajito\Render\AbstractRenderer::getProperties
     * @covers Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     * @covers Molajito\Event::initializeEventOptions
     * @covers Molajito\Event::scheduleEvent
     * @covers Molajito\Event\Dummy::initializeEventOptions
     * @covers Molajito\Event\Dummy::scheduleEvent
     * @covers Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers Molajito\Parse::parseRenderedOutput
     * @covers Molajito\Parse::parseTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::excludeTokens
     * @covers Molajito\Translate::translateString
     * @covers Molajito\Translate\StringArrayAdapter::translateString
     * @covers Molajito\Translate\AbstractAdapter::translateString
     * @covers Molajito\Translate\AbstractAdapter::parseTokens
     * @covers Molajito\Translate\AbstractAdapter::translateToken
     * @covers Molajito\Translate\AbstractAdapter::filterTranslation
     * @covers Molajito\Translate\AbstractAdapter::replaceToken
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

        $results = $this->engine->renderOutput($include_path, $data);

        $this->assertEquals($collect, $results);

        return $this;
    }
}
