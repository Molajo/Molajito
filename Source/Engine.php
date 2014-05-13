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
     * Parameters
     *
     * @var    array
     * @since  1.0.0
     */
    protected $parameters = null;

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0.0
     */
    protected $model_registry = null;

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_results = array();

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
        RenderInterface $token_instance,
        TranslateInterface $translate_instance,
        ParseInterface $parse_instance,
        array $exclude_tokens = array(),
        $stop_loop_count = 100
    ) {
        $this->token_instance     = $token_instance;
        $this->translate_instance = $translate_instance;
        $this->parse_instance     = $parse_instance;
        $this->exclude_tokens     = $exclude_tokens;
        $this->stop_loop_count    = $stop_loop_count;
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
    public function renderOutput($include_file, array $data = array())
    {
        /** Step 1. Render Theme */
        $this->rendered_page = $this->token_instance->renderTheme($include_file, $data);

        /** Step 2. Parse and Render Body */
        $this->renderLoop($this->exclude_tokens);

        /** Step 3. Render Head */
        $this->renderLoop(array());

        /** Step 4. Translate */
        $this->rendered_page = $this->translate_instance->translateString($this->rendered_page);

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

            $this->renderLoopProcessToken($exclude_tokens);

            if ($this->testEndOfLoopProcessing($loop_counter) === true) {
                $loop_counter++;
                continue;
            } else {
                break;
            }
        }

        return $this;
    }

    /**
     * Schedule onBeforeParse Event
     *
     * @param   array  $exclude_tokens
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderLoopProcessToken($exclude_tokens)
    {
        $this->scheduleParseEvents('onBeforeParse');

        $this->parseTokens($exclude_tokens);

        $this->scheduleParseEvents('onAfterParse');

        return $this;
    }

    /**
     * Schedule onBeforeParse Event
     *
     * @return  $this
     * @since   1.0
     */
    protected function scheduleParseEvents($event)
    {
        $options                  = array();
        $options['rendered_page'] = $this->rendered_page;

        $this->token_instance->scheduleEvent($event, $options);

        return $this;
    }

    /**
     * Invoke Parse Class to retrieve tokens to use in rendering
     *
     * @param   array $exclude_tokens
     *
     * @return  $this
     * @since   1.0
     */
    protected function parseTokens(array $exclude_tokens = array())
    {
        $this->tokens = $this->parse_instance->parseRenderedOutput($this->rendered_page, $exclude_tokens);

        return $this;
    }

    /**
     * Render Output for Tokens
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderTokenOutput()
    {
        $tokens = $this->tokens;

        foreach ($tokens as $token) {

            if (strtolower($token->type) === 'position') {
                $this->rendered_page = $this->token_instance->renderPosition($token);
            } else {
                $this->rendered_page = $this->token_instance->renderToken($token, $this->rendered_page);
            }
        }

        return $this;
    }

    /**
     * Determine continuance of loop processing
     *
     * @param   integer $loop_counter
     *
     * @return  boolean
     * @since   1.0
     */
    protected function testEndOfLoopProcessing($loop_counter)
    {
        if (count($this->tokens) > 0) {
        } else {
            return false;
        }

        if ($loop_counter > $this->stop_loop_count) {

            throw new RuntimeException(
                'Molajito renderLoop: Maximum loop count exceeded: ' . $loop_counter
            );
        }

        return true;
    }
}
