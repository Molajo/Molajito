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
use stdClass;

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
     * Setup
     *
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Event\Dummy::__construct
     * @covers  Molajito\Event::__construct
     * @covers  Molajito\Data\AbstractAdapter::__construct
     * @covers  Molajito\Data\Molajo::__construct
     * @covers  Molajito\Data::__construct
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View::__construct
     * @covers  Molajito\Render\AbstractRenderer::__construct
     * @covers  Molajito\Render\Theme::__construct
     * @covers  Molajito\Render\Position::__construct
     * @covers  Molajito\Render\PageView::__construct
     * @covers  Molajito\Render\TemplateView::__construct
     * @covers  Molajito\Render\WrapView::__construct
     * @covers  Molajito\Token::__construct
     * @covers  Molajito\Translate::__construct
     * @covers  Molajito\Translate\StringArrayAdapter::__construct
     * @covers  Molajito\Translate\AbstractAdapter::__construct
     * @covers  Molajito\Engine::__construct
     */
    protected function setUp()
    {
        /** Escape */
        $class  = 'Molajito\\Escape\\Simple';
        $simple = new $class();
        $class  = 'Molajito\\Escape';
        $escape = new $class($simple);

        /** Render */
        $class  = 'Molajito\\Render';
        $render = new $class();

        /** Event */
        $class   = 'Molajito\\Event\\Dummy';
        $adapter = new $class();
        $class   = 'Molajito\\Event';
        $event   = new $class($adapter);

        /** Data */
        $class   = 'Molajito\\Data\\Molajo';
        $adapter = new $class($pagination = null);
        $class   = 'Molajito\\Data';
        $data    = new $class($adapter);

        /** View */
        $theme_base_folder = $include_path = __DIR__ . '/ViewFilesystem/Themes';
        $view_base_folder  = $include_path = __DIR__ . '/ViewFilesystem/Views';

        $class   = 'Molajito\\View\\Filesystem';
        $adapter = new $class($theme_base_folder, $view_base_folder);
        $class   = 'Molajito\\View';
        $view    = new $class($adapter);

        /** Render Classes */
        $class    = 'Molajito\\Render\\Theme';
        $theme    = new $class($escape, $render, $event);
        $class    = 'Molajito\\Render\\Position';
        $position = new $class($escape, $render, $event);
        $class    = 'Molajito\\Render\\PageView';
        $page     = new $class($escape, $render, $event);
        $class    = 'Molajito\\Render\\TemplateView';
        $template = new $class($escape, $render, $event);
        $class    = 'Molajito\\Render\\WrapView';
        $wrap     = new $class($escape, $render, $event);

        $class = 'Molajito\\Token';
        $token = new $class ($data, $view, $theme, $position, $page, $template, $wrap);

        /** Translate - with Escape */
        $class     = 'Molajito\\Translate\\StringArrayAdapter';
        $adapter   = new $class ($escape, $parse_mask = null, $model_registry = array(), $language_strings = array());
        $class     = 'Molajito\\Translate';
        $translate = new $class($adapter);

        /** Parse */
        $class = 'Molajito\\Parse';
        $parse = new $class($rendered_page = null, $exclude_tokens = array(), $parse_mask = null);

        $this->engine = new Engine($token, $translate, $event, $parse, $this->exclude_tokens, $this->stop_loop_count);
    }

    /**
     * @covers  Molajito\Engine::__construct
     * @covers  Molajito\Engine::renderOutput
     * @covers  Molajito\Engine::renderTheme
     * @covers  Molajito\Engine::renderLoop
     * @covers  Molajito\Engine::parseRenderedOutput
     * @covers  Molajito\Engine::scheduleEvents
     * @covers  Molajito\Engine::renderTokens
     * @covers  Molajito\Engine::testEndOfLoopProcessing
     *
     * @covers  Molajito\Parse::parseRenderedOutput
     * @covers  Molajito\Parse::parseTokens
     * @covers  Molajito\Parse::buildTokenObjects
     * @covers  Molajito\Parse::setTokenObject
     * @covers  Molajito\Parse::initialiseToken
     * @covers  Molajito\Parse::extractTokenObjectElements
     * @covers  Molajito\Parse::extractTokenObjectElementsPieces
     * @covers  Molajito\Parse::processExtractedTokenElements
     * @covers  Molajito\Parse::processExtractedTokenElementPair
     * @covers  Molajito\Parse::processFirstTokenElementPair
     * @covers  Molajito\Parse::processSubsequentTokenElementPairs
     * @covers  Molajito\Parse::excludeTokens
     *
     * @covers  Molajito\Token::__construct
     * @covers  Molajito\Token::processToken
     * @covers  Molajito\Token::initialiseData
     * @covers  Molajito\Token::scheduleEvent
     * @covers  Molajito\Token::renderPosition
     * @covers  Molajito\Token::renderTemplateView
     * @covers  Molajito\Token::renderOutput
     * @covers  Molajito\Token::renderWrapView
     * @covers  Molajito\Token::initializeWrapViewObject
     * @covers  Molajito\Token::getWrapData
     * @covers  Molajito\Token::getView
     * @covers  Molajito\Token::getData
     * @covers  Molajito\Token::setClassProperties
     * @covers  Molajito\Token::setClassProperty
     * @covers  Molajito\Token::setOptions
     * @covers  Molajito\Token::replaceTokenWithRenderedOutput
     *
     * @covers  Molajito\Render\Theme::renderOutput
     * @covers  Molajito\Render\Theme::includeFile
     *
     * @covers  Molajito\Render\AbstractRenderer::renderOutput
     * @covers  Molajito\Render\AbstractRenderer::setProperties
     * @covers  Molajito\Render\AbstractRenderer::getProperties
     * @covers  Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::performRendering
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0
     */
    public function testThemeNoIncludes()
    {
        $include_path = __DIR__ . '/Views/';

        $runtime_data            = new stdClass();
        $runtime_data->page_name = 'Nameofpage';

        $data                 = array();
        $data['runtime_data'] = $runtime_data;

        ob_start();
        include $include_path . '/Index.phtml';
        $expected = ob_get_clean();

        $results = $this->engine->renderOutput($include_path, $data);

        $this->assertEquals($expected, $results);

        return $this;
    }

    /**
     * @covers  Molajito\Engine::__construct
     * @covers  Molajito\Engine::renderOutput
     * @covers  Molajito\Engine::renderTheme
     * @covers  Molajito\Engine::renderLoop
     * @covers  Molajito\Engine::parseRenderedOutput
     * @covers  Molajito\Engine::scheduleEvents
     * @covers  Molajito\Engine::renderTokens
     * @covers  Molajito\Engine::testEndOfLoopProcessing
     *
     * @covers  Molajito\Parse::parseRenderedOutput
     * @covers  Molajito\Parse::parseTokens
     * @covers  Molajito\Parse::buildTokenObjects
     * @covers  Molajito\Parse::setTokenObject
     * @covers  Molajito\Parse::initialiseToken
     * @covers  Molajito\Parse::extractTokenObjectElements
     * @covers  Molajito\Parse::extractTokenObjectElementsPieces
     * @covers  Molajito\Parse::processExtractedTokenElements
     * @covers  Molajito\Parse::processExtractedTokenElementPair
     * @covers  Molajito\Parse::processFirstTokenElementPair
     * @covers  Molajito\Parse::processSubsequentTokenElementPairs
     * @covers  Molajito\Parse::excludeTokens
     *
     * @covers  Molajito\Token::__construct
     * @covers  Molajito\Token::processToken
     * @covers  Molajito\Token::initialiseData
     * @covers  Molajito\Token::scheduleEvent
     * @covers  Molajito\Token::renderPosition
     * @covers  Molajito\Token::renderTemplateView
     * @covers  Molajito\Token::renderOutput
     * @covers  Molajito\Token::renderWrapView
     * @covers  Molajito\Token::initializeWrapViewObject
     * @covers  Molajito\Token::getWrapData
     * @covers  Molajito\Token::getView
     * @covers  Molajito\Token::getData
     * @covers  Molajito\Token::setClassProperties
     * @covers  Molajito\Token::setClassProperty
     * @covers  Molajito\Token::setOptions
     * @covers  Molajito\Token::replaceTokenWithRenderedOutput
     *
     * @covers  Molajito\Render\Theme::renderOutput
     * @covers  Molajito\Render\Theme::includeFile
     *
     * @covers  Molajito\Render\AbstractRenderer::renderOutput
     * @covers  Molajito\Render\AbstractRenderer::setProperties
     * @covers  Molajito\Render\AbstractRenderer::getProperties
     * @covers  Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers  Molajito\Render\AbstractRenderer::setEventOptions
     * @covers  Molajito\Render\AbstractRenderer::performRendering
     *
     * @covers  Molajito\Render::renderOutput
     * @covers  Molajito\Render::setProperties
     * @covers  Molajito\Render::includeFile
     *
     * @return  $this
     * @since   1.0
     */
    public function testTheme()
    {
        $include_path = __DIR__ . '/ViewFilesystem/Themes/Test';

        $runtime_data            = new stdClass();
        $runtime_data->page_name = 'Blog';

        $data                 = array();
        $data['runtime_data'] = $runtime_data;

        ob_start();
        include $include_path . '/Index.phtml';
        $expected = ob_get_clean();

        $results = $this->engine->renderOutput($include_path, $data);

        $this->assertEquals($results, $results);

        return $this;
    }
}
