<?php
/**
 * Pagination Parse
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Render\ParseInterface;
use stdClass;

/**
 * Pagination Render Handler
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Parse implements ParseInterface
{
    /**
     * Parse Mask
     *
     * @var    string
     * @since  1.0
     */
    protected $parse_mask = '#<include(.*)\/>#iU';

    /**
     * Exclude tokens from parsing (Head tokens held until end)
     *
     * @var    array
     * @since  1.0
     */
    protected $exclude_tokens = array();

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_page = null;

    /**
     * Constructor
     *
     * @param   string $rendered_page
     * @param   array  $exclude_tokens
     * @param   string $parse_mask
     *
     * @since   1.0
     */
    public function __construct(
        $rendered_page,
        array $exclude_tokens = array(),
        $parse_mask = '#<include(.*)\/>#iU'
    )
    {
        $this->rendered_page  = $rendered_page;
        $this->exclude_tokens = $exclude_tokens;

        if ($parse_mask === null || trim($parse_mask) === '') {
        } else {
            $this->parse_mask = $parse_mask;
        }
    }

    /**
     * Parse Rendered Output returning Tokens to be rendered
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function parse()
    {
        $matches          = array();
        $tokens_to_render = array();

        preg_match_all($this->parse_mask, $this->rendered_page, $matches);

        if (count($matches) == 0) {
            return $tokens_to_render;
        }

        foreach ($matches[1] as $parsed_token) {
            $tokens_to_render[] = $this->setRenderToken($parsed_token);
        }

        if (count($this->exclude_tokens) > 0) {
            $temp             = $tokens_to_render;
            $tokens_to_render = array();
            foreach ($temp as $object) {
                if (in_array($object->name, $this->exclude_tokens)) {
                } else {
                    $tokens_to_render[] = $object;
                }
            }
        }

        $tokens_to_render = $this->excludeTokens($tokens_to_render);

        return $tokens_to_render;
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
        $token               = new stdClass();
        $token->type         = '';
        $token->name         = '';
        $token->wrap         = '';
        $token->attributes   = array();
        $token->replace_this = '<include' . $parsed_token . '/>';

        $token_elements = array();
        $pieces         = explode(' ', $parsed_token);

        if (count($pieces) > 0) {
            foreach ($pieces as $piece) {
                if (trim($piece) == '') {
                } else {
                    $token_elements[] = $piece;
                }
            }
        }

        if (count($token_elements) == 0) {
            return array();
        }

        $count_attributes = 0;
        $first            = 1;

        foreach ($token_elements as $part) {

            $pair = explode('=', $part);

            if ($first === 1) {
                $first = 0;

                if (count($pair) == 1) {
                    $token->type = 'template';
                    $token->name = trim(strtolower($part));
                } else {
                    $token->type = trim(strtolower($pair[0]));
                    $token->name = trim(strtolower($pair[1]));
                }

            } elseif (count($pair) == 2 && $pair[0] == 'wrap') {
                $token->wrap = $pair[1];

            } else {
                $count_attributes ++;
                $token->attributes[$pair[0]] = $pair[1];
            }
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
