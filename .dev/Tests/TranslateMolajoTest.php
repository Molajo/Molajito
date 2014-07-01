<?php
/**
 * Translate Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Test;

use CommonApi\Language\TranslateInterface;
use Molajito\Translate;
use Molajito\Translate\MolajoLanguageAdapter;
use Molajito\Escape;
use Molajito\Escape\Simple;

/**
 * Translate Molajo Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class TranslateMolajoTest extends \PHPUnit_Framework_TestCase
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
        $parse_mask     = null;
        $model_registry = array();

        $adapter = new MolajoLanguageAdapter(
            $escape, $parse_mask, $model_registry, new MockLanguageController
        );

        $this->translate = new Translate($adapter);
    }

    /**
     * Test Template View
     *
     * @covers  Molajito\Escape\Simple::__construct
     * @covers  Molajito\Escape::__construct
     * @covers  Molajito\Translate::__construct
     * @covers  Molajito\Translate\MolajoLanguageAdapter::__construct
     * @covers  Molajito\Translate\AbstractAdapter::__construct
     *
     * @covers  Molajito\Translate::translateString
     * @covers  Molajito\Translate\MolajoLanguageAdapter::translateString
     * @covers  Molajito\Translate\MolajoLanguageAdapter::translateToken
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
}

class MockLanguageController implements TranslateInterface
{
    /**
     * @var $translate
     */
    protected $language_strings
        = array(
            'thing' => 'it is a thing.',
            'THING' => 'Really. It is really a thing.'
        );

    /**
     * Translate String
     *
     * @param   $string
     *
     * @return  string
     * @since   1.0
     */
    public function translateString($string)
    {
        if (isset($this->language_strings[$string])) {
            return $this->language_strings[$string];
        }

        return $string;
    }
}
