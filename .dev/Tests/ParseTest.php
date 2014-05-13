<?php
/**
 * Parse Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Parse;

/**
 * Parse Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ParseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $parse
     */
    protected $parse;

    /**
     * Initialises Adapter
     */
    protected function setUp()
    {
        $this->parse = new Parse();
    }

    /**
     * No input
     *
     * @covers Molajito\Parse::parseRenderedOutput
     * @covers Molajito\Parse::excludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::initialiseToken
     * @covers Molajito\Parse::setTokenElements
     * @covers Molajito\Parse::processTokenElements
     * @covers Molajito\Parse::processFirstTokenElements
     * @covers Molajito\Parse::processSubsequentTokenElements
     * @covers Molajito\Parse::parseTokens
     * @covers Molajito\Parse::parseTokensMatch
     * @covers Molajito\Parse::buildTokensToRender
     * @covers Molajito\Parse::removeExcludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::excludeTokens
     *
     * @return  $this
     * @since   1.0
     */
    public function testParseNoInput()
    {
        $rendered_page = '';

        $results = $this->parse->parseRenderedOutput($rendered_page, array(), null);

        $this->assertEquals(array(), $results);

        return $this;
    }

    /**
     * Test Parse
     *
     * @covers Molajito\Parse::parseRenderedOutput
     * @covers Molajito\Parse::excludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::initialiseToken
     * @covers Molajito\Parse::setTokenElements
     * @covers Molajito\Parse::processTokenElements
     * @covers Molajito\Parse::processFirstTokenElements
     * @covers Molajito\Parse::processSubsequentTokenElements
     * @covers Molajito\Parse::parseTokens
     * @covers Molajito\Parse::parseTokensMatch
     * @covers Molajito\Parse::buildTokensToRender
     * @covers Molajito\Parse::removeExcludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::excludeTokens
     *
     * @return  $this
     * @since   1.0
     */
    public function testParse()
    {
        $include_path = __DIR__ . '/Parse/Include.phtml';

        ob_start();
        include $include_path;
        $rendered_page = ob_get_clean();

        $results = $this->parse->parseRenderedOutput($rendered_page, array(), null);

        $this->assertEquals('page', $results[0]->type);
        $this->assertEquals('xyz', $results[0]->name);
        $this->assertEquals('', $results[0]->wrap);
        $this->assertEquals(array(), $results[0]->attributes);

        return $this;
    }

    /**
     * Test Parse, excluding the token in the previous parse, pass in parse mask
     *
     * @covers Molajito\Parse::parseRenderedOutput
     * @covers Molajito\Parse::excludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::initialiseToken
     * @covers Molajito\Parse::setTokenElements
     * @covers Molajito\Parse::processTokenElements
     * @covers Molajito\Parse::processFirstTokenElements
     * @covers Molajito\Parse::processSubsequentTokenElements
     * @covers Molajito\Parse::parseTokens
     * @covers Molajito\Parse::parseTokensMatch
     * @covers Molajito\Parse::buildTokensToRender
     * @covers Molajito\Parse::removeExcludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::excludeTokens
     *
     * @return  $this
     * @since   1.0
     */
    public function testParseExcludeToken()
    {
        $include_path = __DIR__ . '/Parse/Include.phtml';

        ob_start();
        include $include_path;
        $rendered_page = ob_get_clean();

        $results = $this->parse->parseRenderedOutput($rendered_page, array('page'), '#{I (.*) I}#iU');

        $this->assertEquals(array(), $results);

        return $this;
    }

    /**
     * Position Parse
     *
     * @covers Molajito\Parse::parseRenderedOutput
     * @covers Molajito\Parse::excludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::initialiseToken
     * @covers Molajito\Parse::setTokenElements
     * @covers Molajito\Parse::processTokenElements
     * @covers Molajito\Parse::processFirstTokenElements
     * @covers Molajito\Parse::processSubsequentTokenElements
     * @covers Molajito\Parse::parseTokens
     * @covers Molajito\Parse::parseTokensMatch
     * @covers Molajito\Parse::buildTokensToRender
     * @covers Molajito\Parse::removeExcludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::excludeTokens
     *
     * @return  $this
     * @since   1.0
     */
    public function testParsePosition()
    {
        $include_path = __DIR__ . '/Parse/Position.phtml';

        ob_start();
        include $include_path;
        $rendered_page = ob_get_clean();

        $results = $this->parse->parseRenderedOutput($rendered_page, array(), null);

        $this->assertEquals('position', $results[0]->type);
        $this->assertEquals('xyz', $results[0]->name);
        $this->assertEquals('', $results[0]->wrap);
        $this->assertEquals(array(), $results[0]->attributes);

        return $this;
    }

    /**
     * Wrap Parse
     *
     * @covers Molajito\Parse::parseRenderedOutput
     * @covers Molajito\Parse::excludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::initialiseToken
     * @covers Molajito\Parse::setTokenElements
     * @covers Molajito\Parse::processTokenElements
     * @covers Molajito\Parse::processFirstTokenElements
     * @covers Molajito\Parse::processSubsequentTokenElements
     * @covers Molajito\Parse::parseTokens
     * @covers Molajito\Parse::parseTokensMatch
     * @covers Molajito\Parse::buildTokensToRender
     * @covers Molajito\Parse::removeExcludeTokens
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::setRenderToken
     * @covers Molajito\Parse::excludeTokens
     *
     * @return  $this
     * @since   1.0
     */
    public function testParseWrap()
    {
        $include_path = __DIR__ . '/Parse/Wrap.phtml';

        ob_start();
        include $include_path;
        $rendered_page = ob_get_clean();

        $results = $this->parse->parseRenderedOutput($rendered_page, array(), null);

        $this->assertEquals('template', $results[0]->type);
        $this->assertEquals('xyz', $results[0]->name);
        $this->assertEquals('Article', $results[0]->wrap);
        $this->assertEquals(array(), $results[0]->attributes);

        return $this;
    }
}
