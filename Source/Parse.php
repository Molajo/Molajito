<?php
/**
 * Molajito Parse
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Render\ParseInterface;
use stdClass;

/**
 * Molajito Parse - finds tokens and builds token objects
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Parse implements ParseInterface
{
    /**
     * Parse Mask
     *
     * @var    string
     * @since  1.0.0
     */
    protected $parse_mask = '#{I (.*) I}#iU';

    /**
     * Exclude tokens from first set of parsing (ex. Head tokens held until the second round)
     *
     * @var    array
     * @since  1.0.0
     */
    protected $exclude_tokens = array();

    /**
     * Rendered Output for Page - could have additional tokens
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page = null;

    /**
     * Parse rendered output - return an array of tokens to be rendered
     *
     * @param   string $rendered_page
     * @param   array  $exclude_tokens
     * @param   string $parse_mask
     *
     * @return  array
     * @since   1.0
     */
    public function parseRenderedOutput(
        $rendered_page,
        array $exclude_tokens = array(),
        $parse_mask = null
    ) {
        $this->rendered_page  = $rendered_page;

        $this->exclude_tokens = $exclude_tokens;

        if ($parse_mask === null || trim($parse_mask) === '') {
        } else {
            $this->parse_mask = $parse_mask;
        }

        return $this->parseTokens();
    }

    /**
     * Parse rendered output -- return an array of tokens to be rendered
     *
     * @return  array
     * @since   1.0
     */
    public function parseTokens()
    {
        preg_match_all($this->parse_mask, $this->rendered_page, $matches);

        if (count($matches[1]) > 0) {
            return $this->buildTokenObjects($matches[1]);
        }

        return array();
    }

    /**
     * Build Token Objects for Rendering
     *
     * @param   array $matches
     *
     * @return  array
     * @since   1.0
     */
    public function buildTokenObjects(array $matches = array())
    {
        $tokens_to_render = array();

        foreach ($matches as $parsed_token) {
            $tokens_to_render[] = $this->setTokenObject($parsed_token);
        }

        if (count($this->exclude_tokens) > 0) {
            return $this->excludeTokens($tokens_to_render);
        }

        return $tokens_to_render;
    }

    /**
     * Create a single Token Object for a single parsed token
     *
     * @param   string $parsed_token
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function setTokenObject($parsed_token)
    {
        $token_object = $this->initialiseToken($parsed_token);

        $token_elements = $this->extractTokenObjectElements($parsed_token);

        return $this->processExtractedTokenElements($token_object, $token_elements);
    }

    /**
     * Initialize Token Object
     *
     * @param   string $parsed_token
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function initialiseToken($parsed_token)
    {
        $token               = new stdClass();
        $token->type         = '';
        $token->name         = '';
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '{I ' . $parsed_token . ' I}';

        return $token;
    }

    /**
     * Set Token Object Elements
     *
     * @param   string $parsed_token
     *
     * @return  array
     * @since   1.0
     */
    protected function extractTokenObjectElements($parsed_token)
    {
        $pieces = explode(' ', $parsed_token);

        return $this->extractTokenObjectElementsPieces($pieces);
    }

    /**
     * Set Token Object Ele
     *
     * @param   array $pieces
     *
     * @return  array
     * @since   1.0
     */
    protected function extractTokenObjectElementsPieces(array $pieces = array())
    {
        $token_elements = array();

        foreach ($pieces as $piece) {
            $token_elements[] = $piece;
        }

        return $token_elements;
    }

    /**
     * Process Extracted Token Elements for Token and complete Token Construction
     *
     * @param   stdClass $token_object
     * @param   array    $token_elements
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function processExtractedTokenElements($token_object, array $token_elements = array())
    {
        $first = true;

        foreach ($token_elements as $part) {
            $pair         = explode('=', $part);
            $token_object = $this->processExtractedTokenElementPair($token_object, $pair, $first);
            $first        = false;
        }

        return $token_object;
    }

    /**
     * Process a single token element pair (key and value) for the token
     *
     * @param   stdClass $token_object
     * @param   array    $pair
     * @param   boolean  $first
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function processExtractedTokenElementPair($token_object, array $pair = array(), $first = false)
    {
        if ($first === true) {
            $token_object = $this->processFirstTokenElementPair($token_object, $pair);
        } else {
            $token_object = $this->processSubsequentTokenElementPairs($token_object, $pair);
        }

        return $token_object;
    }

    /**
     * The first token element is view-type=value: ex. template=name or name-of-position
     *
     * @param   stdClass $token_object
     * @param   array    $pair
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function processFirstTokenElementPair($token_object, array $pair = array())
    {
        if (count($pair) == 1) {
            $token_object->type = 'position';
            $token_object->name = trim(strtolower($pair[0]));
        } else {
            $token_object->type = trim(strtolower($pair[0]));
            $token_object->name = trim(strtolower($pair[1]));
        }

        return $token_object;
    }

    /**
     * Process Subsequent Token Element Pairs
     *
     * @param   stdClass $token_object
     * @param   array    $pair
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function processSubsequentTokenElementPairs($token_object, array $pair = array())
    {
        if (count($pair) == 2 && $pair[0] == 'wrap') {
            $token_object->wrap = $pair[1];

        } else {
            $token_object->attributes[ $pair[0] ] = $pair[1];
        }

        return $token_object;
    }

    /**
     * Remove tokens specified in the exclude tokens list
     *
     * @param   array $token_objects
     *
     * @return  array
     * @since   1.0
     */
    protected function excludeTokens(array $token_objects = array())
    {
        $use_token_objects = array();

        foreach ($token_objects as $token_object) {
            if (in_array($token_object->type, $this->exclude_tokens)) {
            } else {
                $use_token_objects[] = $token_object;
            }
        }

        return $use_token_objects;
    }
}
