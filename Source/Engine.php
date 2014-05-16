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
use CommonApi\Render\EventInterface;
use CommonApi\Render\ParseInterface;
use CommonApi\Render\RenderInterface;
use CommonApi\Render\TokenInterface;
use stdClass;

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
     * Token Instance
     *
     * @var    object  CommonApi\Render\TokenInterface
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
     * Event Handler
     *
     * @var    object  CommonApi\Render\EventInterface
     * @since  1.0.0
     */
    protected $event_instance = null;

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
    protected $token_objects = array();

    /**
     * Constructor
     *
     * @param  TokenInterface     $token_instance
     * @param  TranslateInterface $translate_instance
     * @param  EventInterface     $event_instance
     * @param  ParseInterface     $parse_instance
     * @param  array              $exclude_tokens
     * @param  int                $stop_loop_count
     *
     * @since  1.0.0
     */
    public function __construct(
        TokenInterface $token_instance,
        TranslateInterface $translate_instance,
        EventInterface $event_instance,
        ParseInterface $parse_instance,
        array $exclude_tokens = array(),
        $stop_loop_count = 100
    ) {
        $this->token_instance     = $token_instance;
        $this->translate_instance = $translate_instance;
        $this->event_instance     = $event_instance;
        $this->parse_instance     = $parse_instance;
        $this->exclude_tokens     = $exclude_tokens;
        $this->stop_loop_count    = $stop_loop_count;
    }

    /**
     * Render output for specified Theme and all of the tokens which are located thereafter ...
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderOutput($include_path, array $data = array())
    {
        /** Step 1. Render Theme */
        $this->renderTheme($include_path, $data);

        /** Step 2. Parse and Render Body */
        $this->renderLoop();

        /** Step 3. Render Head */
        $this->renderLoop();

        /** Step 4. Translate */
        $this->rendered_page = $this->translate_instance->translateString($this->rendered_page);

        /** Step 5. Schedule onAfterRender Event */
        $this->scheduleEvents('onAfterRender');

        return $this->rendered_page;
    }

    /**
     * Render Theme -- provides rendered_page which is the source of all parsing/rendering
     *
     * @param   string $include_path
     * @param   array  $data
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderTheme($include_path, array $data = array())
    {
        $token_object               = new stdClass();
        $token_object->type         = 'theme';
        $data['include_path']       = $include_path;

        $this->rendered_page = $this->token_instance->processToken($token_object, $data);

        return $this;
    }

    /**
     * Render Loop - runs twice, first time to render Body, second time to render Head
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderLoop()
    {
        $loop_counter = 0;

        while (true === true) {

            $this->parseRenderedOutput();

            $this->renderTokens();

            if ($this->testEndOfLoopProcessing($loop_counter) === true) {
                $loop_counter++;
                continue;
            } else {
                break;
            }
        }

        $this->exclude_tokens = array();

        return $this;
    }

    /**
     * Schedule onBeforeParse Event
     *
     * @return  $this
     * @since   1.0
     */
    protected function parseRenderedOutput()
    {
        $this->scheduleEvents('onBeforeParse');

        $this->token_objects = $this->parse_instance->parseRenderedOutput(
            $this->rendered_page,
            $this->exclude_tokens
        );

        $this->scheduleEvents('onAfterParse');

        return $this;
    }

    /**
     * Schedule Event
     *
     * @param   string $event_name
     *
     * @return  $this
     * @since   1.0
     */
    protected function scheduleEvents($event_name)
    {
        $options = $this->event_instance->initializeEventOptions();

        $options['rendered_page']  = $this->rendered_page;
        $options['exclude_tokens'] = $this->exclude_tokens;
        $options['token_objects']  = $this->token_objects;

        $event_results = $this->event_instance->scheduleEvent($event_name, $options);

        $this->rendered_page      = $event_results['rendered_page'];
        $this->exclude_tokens     = $event_results['exclude_tokens'];
        $options['token_objects'] = $this->token_objects;

        return $this;
    }

    /**
     * Render Output for Tokens
     *
     * @return  $this
     * @since   1.0
     */
    protected function renderTokens()
    {
        foreach ($this->token_objects as $token_object) {

            $this->rendered_page = $this->token_instance->processToken(
                $token_object,
                array('rendered_page' => $this->rendered_page)
            );

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
        if (count($this->token_objects) > 0) {
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
