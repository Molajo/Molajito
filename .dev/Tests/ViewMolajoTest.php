<?php
/**
 * View Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Exception;
use Molajito\View;
use Molajito\View\Molajo;
use stdClass;

/**
 * View Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ViewMolajoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $view
     */
    protected $view_instance;

    /**
     * Create Theme Instance
     */
    protected function setUp()
    {
        /** View */
        $adapter             = new Molajo(new ResourceMock());
        $this->view_instance = new View($adapter);
    }

    /**
     * Test Theme
     *
     * @covers  Molajito\View\Molajo::__construct
     * @covers  Molajito\View\Molajo::getView
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testTheme()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'Theme';

        $extensions = $this->view_instance->getView($token);

        $test = 'Theme://Molajo//Themes//Test';
        $this->assertEquals($extensions->extension, $test);

        return $this;
    }

    /**
     * Get Page View Extension
     *
     * @covers  Molajito\View\Molajo::__construct
     * @covers  Molajito\View\Molajo::getView
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testGetExtensionPage()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'Page';

        $extensions = $this->view_instance->getView($token);

        $test = 'Page://Molajo//Views//Pages//Test';
        $this->assertEquals($extensions->extension, $test);

        return $this;
    }

    /**
     * Get Template View Extension
     *
     * @covers  Molajito\View\Molajo::__construct
     * @covers  Molajito\View\Molajo::getView
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testGetExtensionTemplate()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'Template';

        $extensions = $this->view_instance->getView($token);

        $test = 'Template://Molajo//Views//Templates//Test';
        $this->assertEquals($extensions->extension, $test);

        return $this;
    }

    /**
     * Get Wrap View Extension
     *
     * @covers  Molajito\View\Molajo::__construct
     * @covers  Molajito\View\Molajo::getView
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testGetExtensionWrap()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'Wrap';

        $extensions = $this->view_instance->getView($token);

        $test = 'Wrap://Molajo//Views//Wraps//Test';
        $this->assertEquals($extensions->extension, $test);

        return $this;
    }
}

/**
 * Mock Resource Class
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ResourceMock
{
    /**
     * Mock
     *
     * @param   string $event_name
     * @param   array  $options
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function get($request)
    {
        return $request;
    }
}

/**
 * Mock Resource Class
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class ResourceMock2
{
    /**
     * Mock
     *
     * @param   string $event_name
     * @param   array  $options
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function get($request)
    {
        throw new Exception('ResourceMock2 Exception');
    }
}


