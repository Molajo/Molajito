<?php
/**
 * Molajito Engine
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
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
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class Engine implements RenderInterface
{
    /**
     * Token Instance
     *
     * @var    object  CommonApi\Render\TokenInterface
     * @since  1.0.0
     */
    protected $token_instance = null;

    /**
     * Translate Instance
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
     * Event Handler
     *
     * @var    object  CommonApi\Render\EventInterface
     * @since  1.0.0
     */
    protected $event_instance = null;

    /**
     * Exclude tokens from parsing (tokens to generate head are held until body is processed)
     *
     * @var    array
     * @since  1.0.0
     */
    protected $exclude_tokens = array();

    /**
     * Tokens to Render
     *
     * @var    array
     * @since  1.0.0
     */
    protected $token_objects = array();

    /**
     * Stop Parse and Render Loop Count
     *
     * @var    int
     * @since  1.0.0
     */
    protected $stop_loop_count = 100;

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page = null;

    /**
     * Page View passed in with Theme
     *
     * @var    string
     * @since  1.0.0
     */
    protected $page_view = null;

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
     * @param   array $data
     *
     * @return  string
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function renderOutput(array $data = array())
    {
        $this->scheduleEvents('onBeforeRender');

        $this->renderTheme($data);

        $this->renderLoop();

        $this->scheduleEvents('onBeforeParseHead');

        if (count($this->exclude_tokens) > 0) {
            $this->exclude_tokens = array();
            $this->renderLoop();
        }

        $this->rendered_page = $this->translate_instance->translateString($this->rendered_page);

        $this->scheduleEvents('onAfterRender');

        return $this->rendered_page;
    }

    /**
     * Render Theme -- provides rendered_page which is the source of all parsing/rendering
     *
     * @param   array $data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function renderTheme(array $data = array())
    {
        $token_object = new stdClass();

        foreach ($data as $key => $value) {
            $token_object->$key = $value;
        }

        $this->rendered_page = $this->token_instance->processToken($token_object);

        $this->page_view = $data['page'];

        return $this;
    }

    /**
     * Render Loop - runs twice, first time to render Body, second time to render Head
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function renderLoop()
    {
        $continue     = true;
        $loop_counter = 0;

        while ($continue === true) {
            $continue = $this->renderLoopSteps($loop_counter);
            $loop_counter++;
        }

        return $this;
    }

    /**
     * Render Loop Steps - Runs
     *
     * @param   integer $loop_counter
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function renderLoopSteps($loop_counter)
    {
        $this->parseRenderedOutput();

        if (count($this->token_objects) === 0) {
            return false;
        }

        $this->renderTokens();

        $this->testEndOfLoopProcessing($loop_counter);

        $loop_counter++;

        if ($loop_counter > $this->stop_loop_count) {
            echo 'die in molajito engine';
            die;
        }

        return true;
    }

    /**
     * Schedule onBeforeParse Event
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function parseRenderedOutput()
    {
        $this->scheduleEvents('onBeforeParse');

        $this->token_objects = $this->parse_instance->parseRenderedOutput(
            $this->rendered_page,
            $this->exclude_tokens
        );

        if (count($this->token_objects) > 0) {
            $this->setPage();
            $this->scheduleEvents('onAfterParse');
        }

        return $this;
    }

    /**
     * Set Page Automatically if necessary
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPage()
    {
        foreach ($this->token_objects as $token) {

            if ($token->type === 'position'
                && $token->name === 'page'
            ) {
                $token->type = 'page';
                $token->name = $this->page_view;
            }
        }

        return $this;
    }

    /**
     * Schedule Event - onBeforeParse, onAfterParse
     *
     * @param   string $event_name
     *
     * @return  $this
     * @since   1.0.0
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
     * @since   1.0.0
     */
    protected function renderTokens()
    {
        foreach ($this->token_objects as $key => $token_object) {

            $page = $this->rendered_page;

            $this->rendered_page = $this->token_instance->processToken(
                $token_object,
                array('rendered_page' => $page)
            );

            unset($this->token_objects[$key]);
        }

        return $this;
    }

    /**
     * Determine continuance of loop processing
     *
     * @param   integer $loop_counter
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function testEndOfLoopProcessing($loop_counter)
    {
        if ($loop_counter > $this->stop_loop_count) {

            throw new RuntimeException(
                'Molajito renderLoop: Maximum loop count exceeded: ' . $loop_counter
            );
        }

        return true;
    }
}
