<?php
/**
 * Molajito Engine
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Language\TranslateInterface;
use CommonApi\Render\ParseInterface;
use CommonApi\Render\RenderInterface;

/**
 * Molajito Engine
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Engine implements RenderInterface
{
    /**
     * Parse Instance
     *
     * @var    object  CommonApi\Render\ParseInterface
     * @since  1.0.0
     */
    protected $parse_instance = null;

    /**
     * Exclude tokens from parsing (tokens to generate head are held until body is processed)
     *
     * @var    array
     * @since  1.0.0
     */
    protected $exclude_tokens = array();

    /**
     * Stop Parse and Render Loop Count
     *
     * @var    int
     * @since  1.0.0
     */
    protected $stop_loop_count = 100;

    /**
     * Token Instance
     *
     * @var    object  CommonApi\Render\RenderInterface
     * @since  1.0.0
     */
    protected $token_instance = null;

    /**
     * Wrap View Instance
     *
     * @var    object  CommonApi\Language\TranslateInterface
     * @since  1.0.0
     */
    protected $translate_instance = null;

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page = null;

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_view = null;

    /**
     * Tokens to Render
     *
     * @var    array
     * @since  1.0.0
     */
    protected $tokens = array();

    /**
     * Constructor
     *
     * @param  ParseInterface     $parse_instance
     * @param  array              $exclude_tokens
     * @param  int                $stop_loop_count
     * @param  RenderInterface    $token_instance
     * @param  TranslateInterface $translate_instance
     *
     * @since  1.0.0
     */
    public function __construct(
        ParseInterface $parse_instance,
        array $exclude_tokens = array(),
        $stop_loop_count = 100,
        RenderInterface $token_instance,
        TranslateInterface $translate_instance
    ) {
        $this->parse_instance     = $parse_instance;
        $this->exclude_tokens     = $exclude_tokens;
        $this->stop_loop_count    = $stop_loop_count;
        $this->token_instance     = $token_instance;
        $this->translate_instance = $translate_instance;
    }

    /**
     * Render output for specified view and data
     *
     * @param   string $include_file
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function render($include_file, array $data = array())
    {
        /** Step 1. Render Theme */
        $this->rendered_page = $this->token_instance->renderTheme($include_file, $data);

        /** Step 2. Parse and Render Body */
        $this->renderLoop($this->exclude_tokens);

        /** Step 3. Render Head */
        $this->renderLoop(array());

        /** Step 4. Translate */
        $this->rendered_page = $this->translate_instance->translate($this->rendered_page);

        /** Step 5. Schedule onAfterRender Event */
        $options                  = array();
        $options['rendered_page'] = $this->rendered_page;

        $this->token_instance->scheduleEvent('onAfterRender', $options);

        return $this->rendered_page;
    }

    /**
     * Render Loop - runs twice, first time to render Body, second time to render Head
     *
     * @param   array $exclude_tokens
     *
     * @return  $this
     * @throws  \CommonApi\Exception\RuntimeException
     * @since   1.0
     */
    protected function renderLoop(array $exclude_tokens = array())
    {
        $loop_counter = 0;

        while (true === true) {

            $loop_counter++;

            /** Step 1. Schedule onBeforeParse Event */
            $options                  = array();
            $options['rendered_page'] = $this->rendered_page;

            $this->token_instance->scheduleEvent('onBeforeParse', $options);

            /** Step 2. Parse Output for Tokens */
            $this->tokens = $this->parseTokens($exclude_tokens);

            /** Step 3. Schedule onAfterParse Event */
            $options                  = array();
            $options['rendered_page'] = $this->rendered_page;

            $this->token_instance->scheduleEvent('onAfterParse', $options);

            if (is_array($this->tokens) && count($this->tokens) > 0) {
            } else {
                break;
            }

            /** Step 4. Render Output for Tokens */
            $tokens = $this->tokens;

            foreach ($tokens as $token) {

                if (strtolower($token->type) === 'position') {
                    $this->rendered_page = $this->token_instance->renderPosition($token);
                } else {
                    $this->rendered_page = $this->token_instance->renderToken($token, $this->rendered_page);
                }
            }

            /** Step 5: Check Max Loop Count and stop or continue */
            if ($loop_counter > $this->stop_loop_count) {

                throw new RuntimeException
                (
                    'Molajito renderLoop: Maximum loop count exceeded: ' . $loop_counter
                );
            }

            continue;
        }

        return $this;
    }

    /**
     * Invoke Parse Class to retrieve tokens to use in rendering
     *
     * @param   array $exclude_tokens
     *
     * @return  array
     * @since   1.0
     */
    protected function parseTokens(array $exclude_tokens = array())
    {
        return $this->parse_instance->parse($this->rendered_page, $exclude_tokens);
    }
}
