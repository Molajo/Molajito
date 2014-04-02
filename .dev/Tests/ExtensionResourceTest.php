<?php
/**
 * Pagination Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Extension;
use stdClass;

/**
 * Pagination Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Object
     */
    protected $resource;

    /**
     * @var Object
     */
    protected $extension_resource;

    /**
     * Initialises Adapter
     */
    protected function setUp()
    {
        $this->resource = new ResourceMock();

        $this->extension_resource = new Extension(
            $this->resource,
            $theme = 1,
            $page_view = 2,
            $template_view = 3,
            $wrap_view = 4
        );
    }

    /**
     * Initialise Event Options
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetResourceExtension()
    {
        $extensions = $this->extension_resource->getResourceExtension();

        $test = 'Theme:///Molajo//Themes//1';
        $this->assertEquals($extensions->theme, $test);

        $test = 'Page:///Molajo//Views//Pages//2';
        $this->assertEquals($extensions->page, $test);

        $test = 'Template:///Molajo//Views//Templates//3';
        $this->assertEquals($extensions->template, $test);

        $test = 'Wrap:///Molajo//Views//Wraps//4';
        $this->assertEquals($extensions->wrap, $test);

        return $this;
    }

    /**
     * Get Page View Extension
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetExtensionPage()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'Page';

        $extensions = $this->extension_resource->getExtension($token);

        $test = 'Page:///Molajo//Views//Pages//Test';
        $this->assertEquals($extensions->extension, $test);

        return $this;
    }

    /**
     * Get Template View Extension
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetExtensionTemplate()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'Template';

        $extensions = $this->extension_resource->getExtension($token);

        $test = 'Template:///Molajo//Views//Templates//Test';
        $this->assertEquals($extensions->extension, $test);

        return $this;
    }

    /**
     * Get Wrap View Extension
     *
     * @return  $this
     * @since   1.0
     */
    public function testGetExtensionWrap()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'Wrap';

        $extensions = $this->extension_resource->getExtension($token);

        $test = 'Wrap:///Molajo//Views//Wraps//Test';
        $this->assertEquals($extensions->extension, $test);

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


/**
 * Mock Resource Class
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0
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
     * @since   1.0
     */
    public function get($request)
    {
        return $request;
    }
}
