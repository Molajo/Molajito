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
        $matches = $this->parseTokensMatch();

        if (count($matches) === 0) {
            return array();
        }

        return $this->buildTokensToRender($matches);
    }

    /**
     * Parse tokens in rendered page
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function parseTokensMatch()
    {
        preg_match_all($this->parse_mask, $this->rendered_page, $matches);

        if (is_array($matches)) {
            return $matches;
        }

        return array();
    }

    /**
     * Build Tokens for Rendering
     *
     * @param   array $matches
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function buildTokensToRender($matches)
    {
        $tokens_to_render = array();

        foreach ($matches[1] as $parsed_token) {
            $tokens_to_render[] = $this->setRenderToken($parsed_token);
        }

        if (count($this->exclude_tokens) > 0) {
            return $this->removeExcludeTokens($tokens_to_render);
        }

        return $tokens_to_render;
    }

    /**
     * Remove tokens in excluded array
     *
     * @param   array $matches
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function removeExcludeTokens($tokens_to_render)
    {
        $new = array();

        foreach ($tokens_to_render as $object) {
            if (in_array($object->name, $this->exclude_tokens)) {
            } else {
                $new[] = $object;
            }
        }

        return $this->excludeTokens($new);
    }

    /**
     * Parse rendered output for tokens
     *
     * @param   string $parsed_token
     *
     * @return  object
     * @since   1.0
     */
    protected function setRenderToken($parsed_token)
    {
        $token = $this->initialiseToken($parsed_token);

        $token_elements = $this->setTokenElements($parsed_token);
        if (count($token_elements) == 0) {
            return array();
        }

        return $this->processTokenElements($token_elements, $token);
    }

    /**
     * Initialize Token Object
     *
     * @param   string $parsed_token
     *
     * @return  object
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
        $token_elements = array();

        $pieces         = explode(' ', $parsed_token);

        if (count($pieces) > 0) {
            foreach ($pieces as $piece) {
                if (trim($piece) === '') {
                } else {
                    $token_elements[] = $piece;
                }
            }
        }

        return $token_elements;
    }

    /**
     * Process Token Elements and complete Token Construction
     *
     * @param   array  $token_elements
     * @param   object $token
     *
     * @return  object
     * @since   1.0
     */
    protected function processTokenElements($token_elements, $token)
    {
        $first            = 1;

        foreach ($token_elements as $part) {

            $pair = explode('=', $part);

            if ($first === 1) {
                $first = 0;
                $token = $this->processFirstTokenElements($token, $pair);
            } else {
                $token = $this->processSubsequentTokenElements($token, $pair);
            }
        }

        return $token;
    }

    /**
     * Remove tokens specified in the exclude tokens list
     *
     * @param   object $tokens
     * @param   array  $pair
     *
     * @return  object
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
     * @param   object $tokens
     * @param   array  $pair
     *
     * @return  object
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
        if (count($this->exclude_tokens) == 0) {
            return $tokens;
        }

        $temp   = $tokens;
        $tokens = array();
        foreach ($temp as $object) {
            if (in_array($object->type, $this->exclude_tokens)) {
            } else {
                $tokens[] = $object;
            }
        }

        return $tokens;
    }
}
