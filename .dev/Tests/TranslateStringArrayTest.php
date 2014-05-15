<?php
/**
 * Translate Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use Molajito\Translate;
use Molajito\Translate\StringArrayAdapter;
use Molajito\Escape;
use Molajito\Escape\Simple;

/**
 * Translate Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class TranslateStringArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $translate
     */
    protected $translate;

    /**
     * Create Theme Instance
     */
    protected function setUp()
    {
        /** Escape */
        $simple = new Simple();
        $escape = new Escape($simple);

        /** Translate - with Escape */
        $parse_mask = null;
        $model_registry = array();
        $language_strings = array(
            'thing' => 'it is a thing.',
            'THING' => 'Really. It is really a thing.'
        );
        $adapter = new StringArrayAdapter ($escape, $parse_mask, $model_registry, $language_strings);
        $this->translate = new Translate($adapter);
    }

    /**
     * Test Template View
     *
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Translate::__construct
     * @covers  Molajito\Translate\StringArrayAdapter::__construct
     * @covers  Molajito\Translate\AbstractAdapter::__construct
     *
     * @covers  Molajito\Translate::translateString
     * @covers  Molajito\Translate\StringArrayAdapter::translateString
     * @covers  Molajito\Translate\StringArrayAdapter::translateToken
     * @covers  Molajito\Translate\AbstractAdapter::translateString
     * @covers  Molajito\Translate\AbstractAdapter::processTranslateStrings
     * @covers  Molajito\Translate\AbstractAdapter::processTranslateString
     * @covers  Molajito\Translate\AbstractAdapter::translateToken
     * @covers  Molajito\Translate\AbstractAdapter::parseTokens
     * @covers  Molajito\Translate\AbstractAdapter::translateToken
     * @covers  Molajito\Translate\AbstractAdapter::filterTranslation
     * @covers  Molajito\Translate\AbstractAdapter::replaceToken
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testTranslatePage()
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

        $results = $this->translate->translateString($page);

        $this->assertEquals($should_be, $results);

        return $this;
    }


    /**
     * Construct $translate_mask and $model_registry
     *
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Translate::__construct
     * @covers  Molajito\Translate\StringArrayAdapter::__construct
     * @covers  Molajito\Translate\AbstractAdapter::__construct
     *
     * @covers  Molajito\Translate::translateString
     * @covers  Molajito\Translate\StringArrayAdapter::translateString
     * @covers  Molajito\Translate\StringArrayAdapter::translateToken
     * @covers  Molajito\Translate\AbstractAdapter::translateString
     * @covers  Molajito\Translate\AbstractAdapter::processTranslateStrings
     * @covers  Molajito\Translate\AbstractAdapter::processTranslateString
     * @covers  Molajito\Translate\AbstractAdapter::translateToken
     * @covers  Molajito\Translate\AbstractAdapter::parseTokens
     * @covers  Molajito\Translate\AbstractAdapter::translateToken
     * @covers  Molajito\Translate\AbstractAdapter::filterTranslation
     * @covers  Molajito\Translate\AbstractAdapter::replaceToken
     *
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Escape::escapeOutput
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape\Simple::escapeOutput
     * @covers  Molajito\Escape\Simple::escapeDataElement
     * @covers  Molajito\Escape\AbstractAdapter::escapeOutput
     * @covers  Molajito\Escape\AbstractAdapter::escapeDataElement
     *
     * @return  $this
     * @since   1.0
     */
    public function testConstructor()
    {
        /** Escape */
        $simple = new Simple();
        $escape = new Escape($simple);

        /** Translate - with Escape */
        $parse_mask = '#{T (.*) T}#iU';
        $model_registry
            = array(
            'language_string' => array('name' => 'language_string', 'type' => 'string')
        );
        $language_strings = array(
            'thing' => 'it is a thing.',
            'THING' => 'Really. It is really a thing.'
        );
        $adapter = new StringArrayAdapter ($escape, $parse_mask, $model_registry, $language_strings);
        $translate = new Translate($adapter);

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

        $results = $translate->translateString($page);

        $this->assertEquals($should_be, $results);

        return $this;
    }
}
