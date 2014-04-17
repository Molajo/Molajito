<?php
/**
 * Translate {T Translate This T}
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Language\TranslateInterface;
use CommonApi\Render\EscapeInterface;
use Exception;
use stdClass;

/**
 * Translate {T Translate This T}
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Translate implements TranslateInterface
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
    protected $model_registry = array(
        'language_string' => array('name' => 'language_string', 'type' => 'string')
    );

    /**
     * Language Strings
     *
     * @var    array
     * @since  1.0.0
     */
    protected $language_strings = array();

    /**
     * Parse Mask for Translate Literals
     *
     * @var    string
     * @since  1.0.0
     */
    protected $parse_mask = '#{T (.*) T}#iU';

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
     * @param  array           $language_strings
     * @param  null|string     $parse_mask
     * @param  array           $model_registry
     *
     * @since  1.0.0
     */
    public function __construct(
        EscapeInterface $escape_instance,
        array $language_strings = array(),
        $parse_mask = null,
        array $model_registry = array()
    ) {
        $this->escape_instance  = $escape_instance;
        $this->language_strings = $language_strings;
        $this->parse_mask       = $parse_mask;

        if ($parse_mask === null || trim($parse_mask) == '') {
        } else {
            $this->parse_mask = $parse_mask;
        }

        if (count($model_registry) > 0) {
            $this->model_registry = $model_registry;
        }

        $this->row = new stdClass();
    }

    /**
     * Translate String
     *
     * @param   $string
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function translate($string)
    {
        $this->rendered_page = $string;

        $tokens_to_translate = $this->parseTokens();

        if (count($tokens_to_translate[1]) == 0) {
            return $this->rendered_page;
        }

        for ($i = 0; $i < count($tokens_to_translate[1]); $i ++) {
            $token  = $tokens_to_translate[0][$i];
            $string = $tokens_to_translate[1][$i];

            if (trim($string) == '') {
                $filtered = '';
            } else {
                $translation = $this->translateToken($string);
                $filtered    = $this->filterTranslation($translation);
            }

            $this->replaceToken($token, $filtered);
        }

        return $this->rendered_page;
    }

    /**
     * Parse tokens to be translated
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function parseTokens()
    {
        $tokens_to_translate = array();

        preg_match_all($this->parse_mask, $this->rendered_page, $tokens_to_translate);

        return $tokens_to_translate;
    }

    /**
     * Translate Value
     *
     * @param   string $string
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function translateToken($string)
    {
        $key = strtolower($string);

        if (isset($this->language_strings[$key])) {
            return $this->language_strings[$key];
        }

        return $string;
    }

    /**
     * Translate Value
     *
     * @param   string $string
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function filterTranslation($string)
    {
        $this->row->language_string = $string;

        try {
            return $this->escape_instance->escape(array($this->row), $this->model_registry);

        } catch (Exception $e) {
            throw new RuntimeException
            ('Molajito Translate::filterTranslation Failed: ' . $e->getMessage());
        }
    }

    /**
     * Translate Value
     *
     * @param   string $token
     * @param   string $translation
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function replaceToken($token, $translation)
    {
        $this->rendered_page = str_replace($token, $translation, $this->rendered_page);

        return $this;
    }
}
