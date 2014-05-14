<?php
/**
 * Molajito Render Token Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Token;
use stdClass;

/**
 * Molajito Render Token Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class RenderTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  $token
     */
    protected $token;

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
     * @covers Molajito\Translate::__construct
     * @covers Molajito\Translate\StringArrayAdapter::__construct
     * @covers Molajito\Translate\AbstractAdapter::__construct
     * @covers Molajito\Token::__construct
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

        $class = 'Molajito\\Token';
        $this->token = new $class (
            $data, $view, $theme, $position, $page, $template, $wrap
        );
    }

    /**
     * @covers Molajito\Token::__construct
     * @covers Molajito\Token::processToken
     * @covers Molajito\Token::initialiseData
     * @covers Molajito\Token::scheduleEvent
     * @covers Molajito\Token::renderTemplateView
     * @covers Molajito\Token::renderOutput
     * @covers Molajito\Token::renderWrapView
     * @covers Molajito\Token::getView
     * @covers Molajito\Token::getData
     * @covers Molajito\Token::setOptions
     * @covers Molajito\Token::replaceTokenWithRenderedOutput
     *
     * @covers Molajito\Render\Theme::renderOutput
     * @covers Molajito\Render\Theme::setProperties
     * @covers Molajito\Render\Theme::includeFile
     *
     * @covers Molajito\Render\AbstractRenderer::renderOutput
     * @covers Molajito\Render\AbstractRenderer::setProperties
     * @covers Molajito\Render\AbstractRenderer::getProperties
     * @covers Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     *
     * @covers Molajito\Render::renderOutput
     * @covers Molajito\Render::setProperties
     * @covers Molajito\Render::includeFile
     *
     * @covers Molajito\Event::initializeEventOptions
     * @covers Molajito\Event::scheduleEvent
     * @covers Molajito\Event\Dummy::initializeEventOptions
     * @covers Molajito\Event\Dummy::scheduleEvent
     * @covers Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers Molajito\Event\AbstractAdapter::scheduleEvent
     * @covers Molajito\Event\AbstractAdapter::initializeEventOptions
     *
     * @return  $this
     * @since   1.0
     */
    public function testTheme()
    {
        $include_path = __DIR__ . '/Views/';

        $token               = new stdClass();
        $token->type         = 'theme';
        $token->name         = 'Theme';
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '';

        $runtime_data                                        = new stdClass();
        $runtime_data->page_name = 'Nameofpage';

        $data = array();
        $data['runtime_data'] = $runtime_data;
        $data['include_path'] = $include_path;

        ob_start();
        include $include_path . '/Index.phtml';
        $expected = ob_get_clean();
        $results = $this->token->processToken($token, $data);

        $this->assertEquals($expected, $results);

        return $this;
    }

    /**
     * @covers Molajito\Token::__construct
     * @covers Molajito\Token::processToken
     * @covers Molajito\Token::initialiseData
     * @covers Molajito\Token::scheduleEvent
     * @covers Molajito\Token::renderTemplateView
     * @covers Molajito\Token::renderOutput
     * @covers Molajito\Token::renderWrapView
     * @covers Molajito\Token::getView
     * @covers Molajito\Token::getData
     * @covers Molajito\Token::setOptions
     * @covers Molajito\Token::replaceTokenWithRenderedOutput
     *
     * @covers Molajito\Escape::__construct
     * @covers Molajito\Escape::escapeOutput
     * @covers Molajito\Escape\Simple::__construct
     * @covers Molajito\Escape\Simple::escapeOutput
     * @covers Molajito\Escape\Simple::escapeDataElement
     * @covers Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @covers Molajito\Render\Position::getPositionTemplateViews
     * @covers Molajito\Render\Position::matchPositionTemplates
     * @covers Molajito\Render\Position::getPositionTemplates
     * @covers Molajito\Render\Position::getPositionParameters
     * @covers Molajito\Render\Position::buildPositionTemplatesArray
     * @covers Molajito\Render\Position::buildPositionArray
     * @covers Molajito\Render\Position::getPositionTemplate
     * @covers Molajito\Render\Position::searchPositionArray
     * @covers Molajito\Render\Position::createIncludeStatements
     * @covers Molajito\Render\Position::escapeTemplateName
     *
     * @covers Molajito\Render\AbstractRenderer::renderOutput
     * @covers Molajito\Render\AbstractRenderer::setProperties
     * @covers Molajito\Render\AbstractRenderer::getProperties
     * @covers Molajito\Render\AbstractRenderer::scheduleEvent
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     * @covers Molajito\Render\AbstractRenderer::setEventOptions
     *
     * @return  $this
     * @since   1.0
     */
    public function testPositionNoMatch()
    {
        $rendered_page = '<div>
    <p>{I Nomatch I}</p>
</div>';

        $expected = '<div>
    <p>{I template=Nomatch I}</p>
</div>';

        $token               = new stdClass();
        $token->type         = 'position';
        $token->name         = 'Nomatch';
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '{I Nomatch I}';

        $resource_extension                                        = new stdClass();
        $resource_extension->page                                  = new stdClass();
        $resource_extension->page->menuitem                        = new stdClass();
        $resource_extension->page->menuitem->parameters            = new stdClass();
        $resource_extension->page->menuitem->parameters->positions = '{{test=dog,food}}{{more=not,used}}';

        $data = array();
        $data['runtime_data'] = $resource_extension;
        $data['rendered_page'] = $rendered_page;

       // $results = $this->token->renderPosition($token, $data);

        //$this->assertEquals($expected, $results);

        return $this;
    }
}
