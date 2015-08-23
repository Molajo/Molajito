<?php
/**
 * View Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\View;
use Molajito\View\Filesystem;
use stdClass;

/**
 * View Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ViewFilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $view
     */
    protected $view_instance;

    /**
     * @var $view
     */
    protected $theme_base_folder;

    /**
     * @var $view
     */
    protected $view_base_folder;

    /**
     * Create Theme Instance
     */
    protected function setUp()
    {
        /** View */
        $this->theme_base_folder = $include_path = __DIR__ . '/ViewFilesystem/Themes';
        $this->view_base_folder  = $include_path = __DIR__ . '/ViewFilesystem/Views';

        $adapter             = new Filesystem($this->theme_base_folder, $this->view_base_folder);
        $this->view_instance = new View($adapter);
    }

    /**
     * Test Theme
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View\Filesystem::getView
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     * @covers  Molajito\View\Filesystem::getLocation
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testTheme()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'theme';

        $extensions = $this->view_instance->getView($token);

        $this->assertEquals(
            $extensions->extension->include_path,
            $this->theme_base_folder . '/Test'
        );

        return $this;
    }

    /**
     * Get Page View Extension
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View\Filesystem::getView
     * @covers  Molajito\View\Filesystem::getLocation
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     * @covers  Molajito\View\Filesystem::getLocation
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testGetExtensionPage()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'page';

        $extensions = $this->view_instance->getView($token);

        $this->assertEquals(
            $extensions->extension->include_path,
            $this->view_base_folder . '/Pages/Test'
        );

        return $this;
    }

    /**
     * Get Template View Extension
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View\Filesystem::getView
     * @covers  Molajito\View\Filesystem::getLocation
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     * @covers  Molajito\View\Filesystem::getLocation
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testGetExtensionTemplate()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'template';

        $extensions = $this->view_instance->getView($token);

        $this->assertEquals(
            $extensions->extension->include_path,
            $this->view_base_folder . '/Templates/Test'
        );

        return $this;
    }

    /**
     * Get Wrap View Extension
     *
     * @covers  Molajito\View\Filesystem::__construct
     * @covers  Molajito\View\Filesystem::getView
     * @covers  Molajito\View\Filesystem::getLocation
     * @covers  Molajito\View::__construct
     * @covers  Molajito\View::getView
     * @covers  Molajito\View\Filesystem::getLocation
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testGetExtensionWrap()
    {
        $token       = new stdClass();
        $token->name = 'Test';
        $token->type = 'wrap';

        $extensions = $this->view_instance->getView($token);

        $this->assertEquals(
            $extensions->extension->include_path,
            $this->view_base_folder . '/Wraps/Test'
        );

        return $this;
    }
}
