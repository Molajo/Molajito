<?php
/**
 * Translate Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Escape;
use Molajito\Escape\Simple;
use Molajito\Translate;

/**
 * Translate Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class TranslateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $translate_instance
     */
    protected $translate_instance;

    /**
     * Construct Simple Escape Class and Proxy
     */
    protected function setUp()
    {
        $simple          = new Simple();
        $escape_instance = new Escape($simple);

        $language_strings = array(
            'thing' => 'it is a thing.',
            'THING' => 'Really. It is really a thing.'
        );

        $this->translate = new Translate($escape_instance, $language_strings);
    }

    /**
     * Test Translate
     *
     * @return  $this
     * @since   1.0
     */
    public function testTranslate()
    {

        $page
            = 'Hello
        and goodbye.
        {T This should return without brackets. T}
        {T thing T}';

        $should_be
            = 'Hello
        and goodbye.
        This should return without brackets.
        it is a thing.';

        $results = $this->translate->translate($page);

        $this->assertEquals($should_be, $results);

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
