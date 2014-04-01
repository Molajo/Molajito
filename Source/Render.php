<?php
/**
 * Molajito Render Handler
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\RenderInterface;

/**
 * Molajito Render Handler
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Render implements RenderInterface
{
    /**
     * Resource
     *
     * @var    object
     * @since  1.0
     */
    //protected $resource = null;

    /**
     * Fieldhandler
     *
     * @var    object  CommonApi\Model\FieldhandlerInterface
     * @since  1.0
     */
    //protected $fieldhandler = null;

    /**
     * Date Controller
     *
     * @var    object  CommonApi\Controller\DateInterface
     * @since  1.0
     */
    //protected $date_controller = null;

    /**
     * Url Controller
     *
     * @var    object  CommonApi\Controller\UrlInterface
     * @since  1.0
     */
    //protected $url_controller = null;

    /**
     * Language Instance
     *
     * @var    object CommonApi\Language\LanguageInterface
     * @since  1.0
     */
    protected $language_controller;

    /**
     * Authorisation Controller
     *
     * @var    object  CommonApi\Authorisation\AuthorisationInterface
     * @since  1.0
     */
    //protected $authorisation_controller;

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data;

    /**
     * Plugin Data
     *
     * @var    object
     * @since  1.0
     */
    protected $plugin_data;

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0
     */
    protected $parameters = null;

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    //protected $model_registry = null;

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0
     */
    protected $query_results = array();

    /**
     * Query Results
     *
     * @var    object
     * @since  1.0
     */
    protected $row = null;

    /**
     * Include Path
     *
     * @var    string
     * @since  1.0
     */
    protected $include_path = null;

    /**
     * Constructor
     *
     * @param   array $options
     *
     * @since  1.0
     */
    public function __construct(
        array $options = array()
    ) {
        foreach ($options as $key => $value) {
            if ($key == 'language_controller'
// || $key == 'fieldhandler'
                || $key == 'runtime_data'
                || $key == 'plugin_data'
                || $key == 'parameters'
// || $key == 'model_registry'
                || $key == 'query_results'
                || $key == 'row'
                || $key == 'include_path'
            )
            $this->$key = $value;
        }
    }

    /**
     * Render Output
     *
     * @return  string
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function render()
    {
        if (file_exists($this->include_path)) {
        } else {
            throw new RuntimeException
            ('Molajito Render - path not found: ' . $this->include_path);
        }

        ob_start();
        include $this->include_path;
        $collect = ob_get_clean();

        return $collect;
    }
}
