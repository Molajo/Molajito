<?php
/**
 * Molajito Render Token Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

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
     * @covers  Molajito\Translate::__construct
     * @covers  Molajito\Translate\StringArrayAdapter::__construct
     * @covers  Molajito\Translate\AbstractAdapter::__construct
     * @covers  Molajito\Token::__construct
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

        $class       = 'Molajito\\Token';
        $this->token = new $class (
            $data, $view, $theme, $position, $page, $template, $wrap
        );
    }

    /**
     * @covers  Molajito\Token::__construct
     * @covers  Molajito\Token::processToken
     * @covers  Molajito\Token::initialiseData
     * @covers  Molajito\Token::scheduleEvent
     * @covers  Molajito\Token::renderTemplateView
     * @covers  Molajito\Token::renderOutput
     * @covers  Molajito\Token::renderWrapView
     * @covers  Molajito\Token::initializeWrapViewObject
     * @covers  Molajito\Token::getWrapData
     * @covers  Molajito\Token::getView
     * @covers  Molajito\Token::getData
     * @covers  Molajito\Token::setClassProperties
     * @covers  Molajito\Token::setOptions
     * @covers  Molajito\Token::replaceTokenWithRenderedOutput
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
     * @covers  Molajito\Render\Theme::renderOutput
     * @covers  Molajito\Render\Theme::setProperties
     * @covers  Molajito\Render\Theme::includeFile
     *
     * @covers  Molajito\Event::__construct
     * @covers  Molajito\Event::initializeEventOptions
     * @covers  Molajito\Event::scheduleEvent
     *
     * @covers  Molajito\Event\Dummy::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::__construct
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers  Molajito\Event\AbstractAdapter::scheduleEvent
     *
     * @covers  Molajito\Data::__construct
     * @covers  Molajito\Data::getData
     * @covers  Molajito\Data::editOptions
     * @covers  Molajito\Data::editToken
     *
     * @covers  Molajito\Data\Molajo::__construct
     * @covers  Molajito\Data\Molajo::getData
     * @covers  Molajito\Data\Molajo::initialise
     * @covers  Molajito\Data\Molajo::setModel
     * @covers  Molajito\Data\Molajo::getPrimaryData
     * @covers  Molajito\Data\Molajo::getPrimaryDataExtensionParameters
     * @covers  Molajito\Data\Molajo::getRuntimeData
     * @covers  Molajito\Data\Molajo::getPluginData
     * @covers  Molajito\Data\Molajo::getPluginDataQueryResults
     * @covers  Molajito\Data\Molajo::getPluginDataQueryResultsField
     * @covers  Molajito\Data\Molajo::setParameters
     * @covers  Molajito\Data\Molajo::getDefaultData
     * @covers  Molajito\Data\Molajo::setDataResults
     * @covers  Molajito\Data\Molajo::setDataResultsQueryResults
     * @covers  Molajito\Data\Molajo::setDataResultsParameters
     * @covers  Molajito\Data\Molajo::setDataResultsDataObject
     *
     * @covers  Molajito\Data\MolajoModel::__construct
     * @covers  Molajito\Data\MolajoModel::setModel
     * @covers  Molajito\Data\MolajoModel::setModelType
     * @covers  Molajito\Data\MolajoModel::setModelTypeToken
     * @covers  Molajito\Data\MolajoModel::setModelTypeParameters
     * @covers  Molajito\Data\MolajoModel::setModelName
     * @covers  Molajito\Data\MolajoModel::setModelNameToken
     * @covers  Molajito\Data\MolajoModel::setModelExtensionParameters
     * @covers  Molajito\Data\MolajoModel::setFieldName
     * @covers  Molajito\Data\MolajoModel::setDefaultModelTypeName
     *
     * @covers  Molajito\Data\AbstractAdapter::__construct
     * @covers  Molajito\Data\AbstractAdapter::getData
     * @covers  Molajito\Data\AbstractAdapter::setParametersFromToken
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View\Filesystem::getView
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     * @covers  Molajito\View\Filesystem::getLocation
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

        $runtime_data            = new stdClass();
        $runtime_data->page_name = 'Nameofpage';

        $data                 = array();
        $data['runtime_data'] = $runtime_data;
        $data['include_path'] = $include_path;

        ob_start();
        include $include_path . '/Index.phtml';
        $expected = ob_get_clean();
        $results  = $this->token->processToken($token, $data);

        $this->assertEquals($expected, $results);

        return $this;
    }

    /**
     * @covers  Molajito\Token::__construct
     * @covers  Molajito\Token::processToken
     * @covers  Molajito\Token::initialiseData
     * @covers  Molajito\Token::scheduleEvent
     * @covers  Molajito\Token::renderTemplateView
     * @covers  Molajito\Token::renderOutput
     * @covers  Molajito\Token::renderWrapView
     * @covers  Molajito\Token::initializeWrapViewObject
     * @covers  Molajito\Token::getWrapData
     * @covers  Molajito\Token::getView
     * @covers  Molajito\Token::getData
     * @covers  Molajito\Token::setClassProperties
     * @covers  Molajito\Token::setOptions
     * @covers  Molajito\Token::replaceTokenWithRenderedOutput
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @covers  Molajito\Render\PageView::renderOutput
     * @covers  Molajito\Render\PageView::setProperties
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
     * @covers  Molajito\Render\Position::getPositionTemplateViews
     * @covers  Molajito\Render\Position::matchPositionTemplates
     * @covers  Molajito\Render\Position::getPositionTemplates
     * @covers  Molajito\Render\Position::getPositionParameters
     * @covers  Molajito\Render\Position::buildPositionTemplatesArray
     * @covers  Molajito\Render\Position::buildPositionArray
     * @covers  Molajito\Render\Position::getPositionTemplate
     * @covers  Molajito\Render\Position::searchPositionArray
     * @covers  Molajito\Render\Position::createIncludeStatements
     * @covers  Molajito\Render\Position::escapeTemplateName
     *
     * @covers  Molajito\Event::__construct
     * @covers  Molajito\Event::initializeEventOptions
     * @covers  Molajito\Event::scheduleEvent
     *
     * @covers  Molajito\Event\Dummy::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::__construct
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers  Molajito\Event\AbstractAdapter::scheduleEvent
     *
     * @covers  Molajito\Data::__construct
     * @covers  Molajito\Data::getData
     * @covers  Molajito\Data::editOptions
     * @covers  Molajito\Data::editToken
     *
     * @covers  Molajito\Data\Molajo::__construct
     * @covers  Molajito\Data\Molajo::getData
     * @covers  Molajito\Data\Molajo::initialise
     * @covers  Molajito\Data\Molajo::setModel
     * @covers  Molajito\Data\Molajo::getPrimaryData
     * @covers  Molajito\Data\Molajo::getPrimaryDataExtensionParameters
     * @covers  Molajito\Data\Molajo::getRuntimeData
     * @covers  Molajito\Data\Molajo::getPluginData
     * @covers  Molajito\Data\Molajo::getPluginDataQueryResults
     * @covers  Molajito\Data\Molajo::getPluginDataQueryResultsField
     * @covers  Molajito\Data\Molajo::setParameters
     * @covers  Molajito\Data\Molajo::getDefaultData
     * @covers  Molajito\Data\Molajo::setDataResults
     * @covers  Molajito\Data\Molajo::setDataResultsQueryResults
     * @covers  Molajito\Data\Molajo::setDataResultsParameters
     * @covers  Molajito\Data\Molajo::setDataResultsDataObject
     *
     * @covers  Molajito\Data\MolajoModel::__construct
     * @covers  Molajito\Data\MolajoModel::setModel
     * @covers  Molajito\Data\MolajoModel::setModelType
     * @covers  Molajito\Data\MolajoModel::setModelTypeToken
     * @covers  Molajito\Data\MolajoModel::setModelTypeParameters
     * @covers  Molajito\Data\MolajoModel::setModelName
     * @covers  Molajito\Data\MolajoModel::setModelNameToken
     * @covers  Molajito\Data\MolajoModel::setModelExtensionParameters
     * @covers  Molajito\Data\MolajoModel::setFieldName
     * @covers  Molajito\Data\MolajoModel::setDefaultModelTypeName
     *
     * @covers  Molajito\Data\AbstractAdapter::__construct
     * @covers  Molajito\Data\AbstractAdapter::getData
     * @covers  Molajito\Data\AbstractAdapter::setParametersFromToken
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View\Filesystem::getView
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     * @covers  Molajito\View\Filesystem::getLocation
     *
     * @return  $this
     * @since   1.0
     */
    public function testPage()
    {
        /** Expected Result */
        ob_start();
        include __DIR__ . '/ViewFilesystem/Views/Pages/Test/RenderedPageExpectedResult.phtml';;
        $expected = ob_get_clean();

        /** Rendered Page */
        ob_start();
        include __DIR__ . '/ViewFilesystem/Views/Pages/Test/RenderedPage.phtml';
        $rendered_page = ob_get_clean();

        $token               = new stdClass();
        $token->type         = 'page';
        $token->name         = 'Test';
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '{I Test I}';

        $runtime_data = new stdClass();

        $data                  = array();
        $data['rendered_page'] = $rendered_page;
        $data['runtime_data']  = $runtime_data;

        $results = $this->token->processToken($token, $data);

        $this->assertEquals($expected, $results);

        return $this;
    }

    /**
     * @covers  Molajito\Token::__construct
     * @covers  Molajito\Token::processToken
     * @covers  Molajito\Token::initialiseData
     * @covers  Molajito\Token::scheduleEvent
     * @covers  Molajito\Token::renderTemplateView
     * @covers  Molajito\Token::renderOutput
     * @covers  Molajito\Token::renderWrapView
     * @covers  Molajito\Token::initializeWrapViewObject
     * @covers  Molajito\Token::getWrapData
     * @covers  Molajito\Token::getView
     * @covers  Molajito\Token::getData
     * @covers  Molajito\Token::setClassProperties
     * @covers  Molajito\Token::setOptions
     * @covers  Molajito\Token::replaceTokenWithRenderedOutput
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
     * @covers  Molajito\Render\Position::getPositionTemplateViews
     * @covers  Molajito\Render\Position::matchPositionTemplates
     * @covers  Molajito\Render\Position::getPositionTemplates
     * @covers  Molajito\Render\Position::getPositionParameters
     * @covers  Molajito\Render\Position::buildPositionTemplatesArray
     * @covers  Molajito\Render\Position::buildPositionArray
     * @covers  Molajito\Render\Position::getPositionTemplate
     * @covers  Molajito\Render\Position::searchPositionArray
     * @covers  Molajito\Render\Position::createIncludeStatements
     * @covers  Molajito\Render\Position::escapeTemplateName
     *
     * @covers  Molajito\Event::__construct
     * @covers  Molajito\Event::initializeEventOptions
     * @covers  Molajito\Event::scheduleEvent
     *
     * @covers  Molajito\Event\Dummy::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::__construct
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers  Molajito\Event\AbstractAdapter::scheduleEvent
     *
     * @covers  Molajito\Data::__construct
     * @covers  Molajito\Data::getData
     * @covers  Molajito\Data::editOptions
     * @covers  Molajito\Data::editToken
     *
     * @covers  Molajito\Data\Molajo::__construct
     * @covers  Molajito\Data\Molajo::getData
     * @covers  Molajito\Data\Molajo::initialise
     * @covers  Molajito\Data\Molajo::setModel
     * @covers  Molajito\Data\Molajo::getPrimaryData
     * @covers  Molajito\Data\Molajo::getPrimaryDataExtensionParameters
     * @covers  Molajito\Data\Molajo::getRuntimeData
     * @covers  Molajito\Data\Molajo::getPluginData
     * @covers  Molajito\Data\Molajo::getPluginDataQueryResults
     * @covers  Molajito\Data\Molajo::getPluginDataQueryResultsField
     * @covers  Molajito\Data\Molajo::setParameters
     * @covers  Molajito\Data\Molajo::getDefaultData
     * @covers  Molajito\Data\Molajo::setDataResults
     * @covers  Molajito\Data\Molajo::setDataResultsQueryResults
     * @covers  Molajito\Data\Molajo::setDataResultsParameters
     * @covers  Molajito\Data\Molajo::setDataResultsDataObject
     *
     * @covers  Molajito\Data\MolajoModel::__construct
     * @covers  Molajito\Data\MolajoModel::setModel
     * @covers  Molajito\Data\MolajoModel::setModelType
     * @covers  Molajito\Data\MolajoModel::setModelTypeToken
     * @covers  Molajito\Data\MolajoModel::setModelTypeParameters
     * @covers  Molajito\Data\MolajoModel::setModelName
     * @covers  Molajito\Data\MolajoModel::setModelNameToken
     * @covers  Molajito\Data\MolajoModel::setModelExtensionParameters
     * @covers  Molajito\Data\MolajoModel::setFieldName
     * @covers  Molajito\Data\MolajoModel::setDefaultModelTypeName
     *
     * @covers  Molajito\Data\AbstractAdapter::__construct
     * @covers  Molajito\Data\AbstractAdapter::getData
     * @covers  Molajito\Data\AbstractAdapter::setParametersFromToken
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View\Filesystem::getView
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     * @covers  Molajito\View\Filesystem::getLocation
     *
     * @return  $this
     * @since   1.0
     */
    public function testPositionNoMatch()
    {
        $include_path = __DIR__ . '/Views/';

        ob_start();
        include $include_path . '/RenderedPageRenderTokenTestPosition.phtml';
        $rendered_page = ob_get_clean();

        ob_start();
        include $include_path . '/RenderedPageRenderTokenTestPositionExpected.phtml';
        $expected = ob_get_clean();

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

        $data                  = array();
        $data['runtime_data']  = $resource_extension;
        $data['rendered_page'] = $rendered_page;

        $results = $this->token->processToken($token, $data);

        $this->assertEquals($expected, $results);

        return $this;
    }

    /**
     * @covers  Molajito\Token::__construct
     * @covers  Molajito\Token::processToken
     * @covers  Molajito\Token::initialiseData
     * @covers  Molajito\Token::scheduleEvent
     * @covers  Molajito\Token::renderTemplateView
     * @covers  Molajito\Token::renderOutput
     * @covers  Molajito\Token::renderWrapView
     * @covers  Molajito\Token::initializeWrapViewObject
     * @covers  Molajito\Token::getWrapData
     * @covers  Molajito\Token::getView
     * @covers  Molajito\Token::getData
     * @covers  Molajito\Token::setClassProperties
     * @covers  Molajito\Token::setOptions
     * @covers  Molajito\Token::replaceTokenWithRenderedOutput
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
     * @covers  Molajito\Render\Position::getPositionTemplateViews
     * @covers  Molajito\Render\Position::matchPositionTemplates
     * @covers  Molajito\Render\Position::getPositionTemplates
     * @covers  Molajito\Render\Position::getPositionParameters
     * @covers  Molajito\Render\Position::buildPositionTemplatesArray
     * @covers  Molajito\Render\Position::buildPositionArray
     * @covers  Molajito\Render\Position::getPositionTemplate
     * @covers  Molajito\Render\Position::searchPositionArray
     * @covers  Molajito\Render\Position::createIncludeStatements
     * @covers  Molajito\Render\Position::escapeTemplateName
     *
     * @covers  Molajito\Event::__construct
     * @covers  Molajito\Event::initializeEventOptions
     * @covers  Molajito\Event::scheduleEvent
     *
     * @covers  Molajito\Event\Dummy::scheduleEvent
     * @covers  Molajito\Event\AbstractAdapter::__construct
     * @covers  Molajito\Event\AbstractAdapter::initializeEventOptions
     * @covers  Molajito\Event\AbstractAdapter::scheduleEvent
     *
     * @covers  Molajito\Data::__construct
     * @covers  Molajito\Data::getData
     * @covers  Molajito\Data::editOptions
     * @covers  Molajito\Data::editToken
     *
     * @covers  Molajito\Data\Molajo::__construct
     * @covers  Molajito\Data\Molajo::getData
     * @covers  Molajito\Data\Molajo::initialise
     * @covers  Molajito\Data\Molajo::setModel
     * @covers  Molajito\Data\Molajo::getPrimaryData
     * @covers  Molajito\Data\Molajo::getPrimaryDataExtensionParameters
     * @covers  Molajito\Data\Molajo::getRuntimeData
     * @covers  Molajito\Data\Molajo::getPluginData
     * @covers  Molajito\Data\Molajo::getPluginDataQueryResults
     * @covers  Molajito\Data\Molajo::getPluginDataQueryResultsField
     * @covers  Molajito\Data\Molajo::setParameters
     * @covers  Molajito\Data\Molajo::getDefaultData
     * @covers  Molajito\Data\Molajo::setDataResults
     * @covers  Molajito\Data\Molajo::setDataResultsQueryResults
     * @covers  Molajito\Data\Molajo::setDataResultsParameters
     * @covers  Molajito\Data\Molajo::setDataResultsDataObject
     *
     * @covers  Molajito\Data\MolajoModel::__construct
     * @covers  Molajito\Data\MolajoModel::setModel
     * @covers  Molajito\Data\MolajoModel::setModelType
     * @covers  Molajito\Data\MolajoModel::setModelTypeToken
     * @covers  Molajito\Data\MolajoModel::setModelTypeParameters
     * @covers  Molajito\Data\MolajoModel::setModelName
     * @covers  Molajito\Data\MolajoModel::setModelNameToken
     * @covers  Molajito\Data\MolajoModel::setModelExtensionParameters
     * @covers  Molajito\Data\MolajoModel::setFieldName
     * @covers  Molajito\Data\MolajoModel::setDefaultModelTypeName
     *
     * @covers  Molajito\Data\AbstractAdapter::__construct
     * @covers  Molajito\Data\AbstractAdapter::getData
     * @covers  Molajito\Data\AbstractAdapter::setParametersFromToken
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View\Filesystem::getView
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     * @covers  Molajito\View\Filesystem::getLocation
     *
     * @return  $this
     * @since   1.0
     */
    public function testTemplate()
    {
        $include_path = __DIR__ . '/ViewFileSystem/Views/Templates/Test';

        ob_start();
        include $include_path . '/RenderedPage.phtml';
        $rendered_page = ob_get_clean();

        ob_start();
        include $include_path . '/RenderedPageExpectedResult.phtml';
        $expected = ob_get_clean();

        $token               = new stdClass();
        $token->type         = 'template';
        $token->name         = 'Test';
        $token->wrap         = '';
        $token->model_type   = 'plugin_data';
        $token->model_name   = 'Test';
        $token->attributes   = array(
            'model_type' => 'primary',
            'model_name' => null
        );
        $token->replace_this = '{I template=Test I}';

        $plugin_data           = new stdClass();
        $plugin_data->testdata = new stdClass();

        $query = array();

        $row               = new stdClass();
        $row->id           = 1;
        $row->title        = 'I am a title 1';
        $row->content_text = '<p>I am a paragraph 1</p>';

        $query[] = $row;

        $model_registry = array(
            'id'           => array('name' => 'id', 'type' => 'integer'),
            'title'        => array('name' => 'title', 'type' => 'string'),
            'content_text' => array('name' => 'content_text', 'type' => 'html')
        );

        $parameters       = new stdClass();
        $parameters->key1 = 1;
        $parameters->key2 = 2;

        $runtime_data                           = new stdClass();
        $runtime_data->resource                 = new stdClass();
        $runtime_data->resource->data           = $query;
        $runtime_data->resource->model_registry = $model_registry;
        $runtime_data->resource->parameters     = $parameters;

        $data                 = array();
        $data['runtime_data'] = $runtime_data;

        $data['plugin_data']   = new stdClass();
        $data['rendered_page'] = $rendered_page;

        $results = $this->token->processToken($token, $data);

        $this->assertEquals($expected, $results);

        return $this;
    }
}
