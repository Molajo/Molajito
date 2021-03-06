<?php
/**
 * Abstract Adapter {T This T}
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito\Translate;

use CommonApi\Language\TranslateInterface;
use CommonApi\Render\EscapeInterface;
use stdClass;

/**
 * Abstract Adapter {T Array This T}
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
abstract class AbstractAdapter implements TranslateInterface
{
    /**
     * Escape Instance
     *
     * @var    object   CommonApi\Render\EscapeInterface
     * @since  1.0.0
     */
    protected $escape_instance = null;

    /**
     * Escape Row
     *
     * @var    object
     * @since  1.0.0
     */
    protected $row;

    /**
     * Escape Model Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $model_registry
        = array(
            'language_string' => array('name' => 'language_string', 'type' => 'string')
        );

    /**
     * Parse Mask for Array Literals
     *
     * @var    string
     * @since  1.0.0
     */
    protected $translate_mask = '#{T (.*) T}#iU';

    /**
     * Rendered Page
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page;

    /**
     * Class Constructor
     *
     * @param  EscapeInterface $escape_instance
     * @param  null|string     $translate_mask
     * @param  array           $model_registry
     *
     * @since  1.0.0
     */
    public function __construct(
        EscapeInterface $escape_instance,
        $translate_mask = null,
        array $model_registry = array()
    ) {
        $this->escape_instance = $escape_instance;

        if ($translate_mask === null || trim($translate_mask) === '') {
        } else {
            $this->translate_mask = $translate_mask;
        }

        if (count($model_registry) > 0) {
            $this->model_registry = $model_registry;
        }

        $this->row = new stdClass();
    }

    /**
     * Array String
     *
     * @param   $string
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function translateString($string)
    {
        $this->rendered_page = $string;

        $tokens_to_translate = $this->parseTokens();

        if (count($tokens_to_translate[1]) == 0) {
            return $this->rendered_page;
        }

        $this->processTranslateStrings($tokens_to_translate);

        return $this->rendered_page;
    }

    /**
     * Parse tokens to be translated
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function parseTokens()
    {
        $tokens_to_translate = array();

        preg_match_all($this->translate_mask, $this->rendered_page, $tokens_to_translate);

        return $tokens_to_translate;
    }

    /**
     * Loop through all strings and translate
     *
     * @param   array $tokens_to_translate
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function processTranslateStrings($tokens_to_translate)
    {
        for ($i = 0; $i < count($tokens_to_translate[1]); $i++) {

            $this->processTranslateString(
                $tokens_to_translate[0][$i],
                $tokens_to_translate[1][$i]
            );
        }

        return $this;
    }

    /**
     * Loop through all strings and translate
     *
     * @param   string $token
     * @param   string $string
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function processTranslateString($token, $string)
    {
        $translation = $this->translateToken($string);
        $filtered    = $this->filterTranslation($translation);

        $this->rendered_page = $this->replaceToken($token, $filtered);

        return $this;
    }

    /**
     * Array Value
     *
     * @param   string $string
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    abstract protected function translateToken($string);

    /**
     * Array Value
     *
     * @param   string $string
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function filterTranslation($string)
    {
        if (is_object($string)) {
            return '';
        }

        $this->row->language_string = $string;

        $rows = $this->escape_instance->escapeOutput(array($this->row), $this->model_registry);

        return $rows[0]->language_string;
    }

    /**
     * Array Value
     *
     * @param   string $token
     * @param   string $translation
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function replaceToken($token, $translation)
    {
        return str_replace($token, $translation, $this->rendered_page);
    }
}
