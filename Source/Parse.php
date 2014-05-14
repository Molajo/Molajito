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
 * Molajito Render Handler
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
     * Exclude tokens from parsing (Head tokens held until end)
     *
     * @var    array
     * @since  1.0.0
     */
    protected $exclude_tokens = array();

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page = null;

    /**
     * Parse rendered output returning an array of tokens to be rendered
     *
     * @param   string $rendered_page
     * @param   array  $exclude_tokens
     * @param   string $parse_mask
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
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
     * Parse rendered output returning an array of tokens to be rendered
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function parseTokens()
    {
        preg_match_all($this->parse_mask, $this->rendered_page, $matches);

        if (count($matches[1]) > 0) {
            return $this->buildTokensToRender($matches[1]);
        }

        return array();
    }

    /**
     * Build Tokens for Rendering
     *
     * @param   string[] $matches
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function buildTokensToRender(array $matches = array())
    {
        $tokens_to_render = array();

        foreach ($matches as $parsed_token) {
            $tokens_to_render[] = $this->setRenderToken($parsed_token);
        }

        if (count($this->exclude_tokens) > 0) {
            return $this->excludeTokens($tokens_to_render);
        }

        return $tokens_to_render;
    }

    /**
     * Parse rendered output for tokens
     *
     * @param   string $parsed_token
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function setRenderToken($parsed_token)
    {
        $token = $this->initialiseToken($parsed_token);

        $token_elements = $this->setTokenElements($parsed_token);

        return $this->processTokenElements($token_elements, $token);
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
     * Set Token Elements
     *
     * @param   string $parsed_token
     *
     * @return  array
     * @since   1.0
     */
    protected function setTokenElements($parsed_token)
    {
        $pieces = explode(' ', $parsed_token);

        return $this->setTokenElementsPieces($pieces);
    }

    /**
     * Set Token Elements
     *
     * @param   array  $pieces
     *
     * @return  array
     * @since   1.0
     */
    protected function setTokenElementsPieces($pieces)
    {
        $token_elements = array();

        foreach ($pieces as $piece) {
            $token_elements[] = $piece;
        }

        return $token_elements;
    }

    /**
     * Process Token Elements and complete Token Construction
     *
     * @param   array    $token_elements
     * @param   stdClass $token
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function processTokenElements($token_elements, $token)
    {
        $first = 1;

        foreach ($token_elements as $part) {
            $pair  = explode('=', $part);
            $token = $this->processTokenPair($token, $pair, $first);
            $first = 0;
        }

        return $token;
    }

    /**
     * Process Token Pair
     *
     * @param   stdClass $token
     * @param   array    $pair
     * @param integer $first
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function processTokenPair($token, $pair, $first)
    {
        if ($first === 1) {
            $token = $this->processFirstTokenElements($token, $pair);
        } else {
            $token = $this->processSubsequentTokenElements($token, $pair);
        }

        return $token;
    }

    /**
     * Remove tokens specified in the exclude tokens list
     *
     * @param   stdClass $token
     * @param   array  $pair
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function processFirstTokenElements($token, $pair)
    {
        if (count($pair) == 1) {
            $token->type = 'position';
            $token->name = trim(strtolower($pair[0]));
        } else {
            $token->type = trim(strtolower($pair[0]));
            $token->name = trim(strtolower($pair[1]));
        }

        return $token;
    }

    /**
     * Process Subsequent Token Elements
     *
     * @param   stdClass $token
     * @param   array  $pair
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function processSubsequentTokenElements($token, $pair)
    {
        if (count($pair) == 2 && $pair[0] == 'wrap') {
            $token->wrap = $pair[1];

        } else {
            $token->attributes[ $pair[0] ] = $pair[1];
        }

        return $token;
    }

    /**
     * Remove tokens specified in the exclude tokens list
     *
     * @param   array $tokens
     *
     * @return  array
     * @since   1.0
     */
    protected function excludeTokens($tokens)
    {
        $use_tokens = array();
        foreach ($tokens as $object) {
            if (in_array($object->type, $this->exclude_tokens)) {
            } else {
                $use_tokens[] = $object;
            }
        }

        return $use_tokens;
    }
}
