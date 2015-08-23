<?php
/**
 * Molajito Token Processor
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Render\TokenInterface;

/**
 * Molajito Token Processor
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class Token implements TokenInterface
{
    /**
     * First Time
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $first_time = true;

    /**
     * Render Array
     *
     * key   - ex. theme, page, position, template, wrap
     * value - class instance of CommonApi\Render\RenderInterface type
     *
     * @var    array
     * @since  1.0.0
     */
    protected $render_array = array();

    /**
     * Rendered View
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_view = null;

    /**
     * Rendered Page
     *
     * @var    string
     * @since  1.0.0
     */
    protected $rendered_page = null;

    /**
     * Token
     *
     * @var    object
     * @since  1.0.0
     */
    protected $token = null;

    /**
     * Constructor
     *
     * @param  array $render_array
     *
     * @since  1.0.0
     */
    public function __construct(
        array $render_array = array()
    ) {
        $this->render_array = $render_array;
    }

    /**
     * Render Output for Specified Token
     *
     * @param   object $token
     *
     * @return  string
     * @since   1.0.0
     */
    public function processToken($token)
    {
        $this->initialiseData($token);

        $this->renderToken();

        $this->replaceTokenWithRenderedOutput();

        return $this->rendered_page;
    }

    /**
     * Initialise Class Data
     *
     * @param   object $token
     *
     * @return  object
     * @since   1.0.0
     */
    protected function initialiseData($token)
    {
        if (isset($token->rendered_page)) {
            $this->rendered_page = $token->rendered_page;
            unset($token->rendered_page);
        }

        $this->token = $token;

        return $this;
    }

    /**
     * Render Output for Token
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function renderToken()
    {
        if (isset($this->render_array[$this->token->type])) {
            $this->rendered_view
                = $this->render_array[$this->token->type]->renderOutput(array('token' => $this->token));
        }

        return $this;
    }

    /**
     * Replace Token with Rendered Output
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function replaceTokenWithRenderedOutput()
    {
        if ($this->first_time === true) {
            $this->first_time = false;
            $page             = $this->rendered_view;
        } else {
            $page = str_replace($this->token->replace_this, trim($this->rendered_view), $this->rendered_page);
        }

        $this->rendered_view = '';

        $this->rendered_page = $page;

        return $this;
    }
}
