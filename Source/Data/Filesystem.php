<?php
/**
 * Filesystem Data Adapter for Molajito
 *
 * @package    Filesystem
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Data;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\DataInterface;
use stdClass;

/**
 * Filesystem Data Adapter for Molajito
 *
 * @package    Filesystem
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Filesystem extends AbstractAdapter implements DataInterface
{
    /**
     * Pagination Class
     *
     * @var    object
     * @since  1.0
     */
    protected $pagination = null;

    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data = null;

    /**
     * Token
     *
     * @var    object
     * @since  1.0
     */
    protected $token = null;

    /**
     * Model Type
     *
     * @var    string
     * @since  1.0
     */
    protected $model_type = '';

    /**
     * Model Name
     *
     * @var    string
     * @since  1.0
     */
    protected $model_name = '';

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0
     */
    protected $query_results = array();

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = array();

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0
     */
    protected $parameters = null;

    /**
     * Author Profile
     *
     * @var    object
     * @since  1.0
     */
    protected $author = null;

    /**
     * Posts
     *
     * @var    array
     * @since  1.0
     */
    protected $posts = array();

    /**
     * Categories
     *
     * @var    array
     * @since  1.0
     */
    protected $categories = array();

    /**
     * Tags
     *
     * @var    array
     * @since  1.0
     */
    protected $tags = array();

    /**
     * Featured
     *
     * @var    array
     * @since  1.0
     */
    protected $featured = array();

    /**
     * Breadcrumbs
     *
     * @var    array
     * @since  1.0
     */
    protected $breadcrumbs = array();

    /**
     * Holds primary display posts
     *
     * @var    array
     * @since  1.0
     */
    protected $display_items_per_page_count = array();

    /**
     * Count of the total items for pagination
     *  -> not all will necessarily be displayed
     *
     * @var    array
     * @since  1.0
     */
    protected $total_items = 0;

    /**
     * Post Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $post_model_registry = array();

    /**
     * Author Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $author_model_registry = array();

    /**
     * Menu Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $list_model_registry = array(
        'link'  => array('name' => 'link', 'type' => 'url'),
        'title' => array('name' => 'title', 'type' => 'string')
    );

    /**
     * Pagination Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $pagination_model_registry = array(
        'first_page_number'       => array('name' => 'first_page_number', 'type' => 'integer'),
        'first_page_link'         => array('name' => 'first_page_link', 'type' => 'url'),
        'previous_page_number'    => array('name' => 'previous_page_number', 'type' => 'integer'),
        'previous_page_link'      => array('name' => 'previous_page_link', 'type' => 'url'),
        'current_start_parameter_number'     => array('name' => 'current_start_parameter_number', 'type' => 'integer'),
        'current_page_link'       => array('name' => 'current_page_link', 'type' => 'url'),
        'next_page_number'        => array('name' => 'next_page_number', 'type' => 'integer'),
        'next_page_link'          => array('name' => 'next_page_link', 'type' => 'url'),
        'last_page_number'        => array('name' => 'last_page_number', 'type' => 'integer'),
        'last_page_link'          => array('name' => 'last_page_link', 'type' => 'url'),
        'total_items'             => array('name' => 'total_items', 'type' => 'integer'),
        'start_links_page_number' => array('name' => 'start_links_page_number', 'type' => 'integer'),
        'stop_links_page_number'  => array('name' => 'stop_links_page_number', 'type' => 'integer'),
        'page_links_array'        => array('name' => 'page_links_array', 'type' => 'arrays')
    );

    /**
     * First Data Request indicator
     *
     * @var    boolean
     * @since  1.0
     */
    protected $first_request = true;

    /**
     * Get Data for Rendering
     *
     * @param   object $token
     * @param   array  $options
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getData($token, array $options = array())
    {
        $this->initialise($token, $options);

        if (strtolower($token->type) == 'page') {
        } else {
            $this->setModel();
            $this->getModelData();
        }

        return $this->setDataResults();
    }

    /**
     * Initialise Class Properties
     *
     * @param   object $token
     * @param   array  $options
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function initialise($token, array $options = array())
    {
        $this->runtime_data   = null;
        $this->model_type     = '';
        $this->model_name     = '';
        $this->query_results  = array();
        $this->model_registry = array();
        $this->parameters     = null;

        $this->token = $token;

        if (isset($options['runtime_data'])) {
            $this->runtime_data = $options['runtime_data'];
        } else {
            $this->runtime_data = null;
        }

        $this->parameters = new stdClass();

        if ($this->first_request === true) {
            $this->first_request = false;
            $blog_breadcrumbs    = $this->setBreadcrumbs();
            $this->setPostURLs($blog_breadcrumbs);
        }

        return $this;
    }

    /**
     * Set Model Type, Model Name and Field Name values used for data retrieval
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setModel()
    {
        $this->model_type = 'Filesystem';
        $this->model_name = $this->token->name;

        return $this;
    }

    /**
     * Get Data according to Model Type and Model Name
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getModelData()
    {
        $method = 'get' . $this->model_name;

        return $this->$method();
    }

    /**
     * Set Rows for Lists (Menu items, categories, tags, etc.)
     *
     * @param   string $link
     * @param   string $title
     *
     * @return  stdClass
     * @since   1.0
     */
    protected function setListRow($link, $title)
    {
        $row        = new stdClass();
        $row->link  = $link;
        $row->title = $title;

        return $row;
    }

    /**
     * Set Rows for Items (Normal, featured, etc)
     *
     * @param   string $title
     *
     * @return  $this
     * @since   1.0
     */
    protected function setItemRow($title)
    {
        if (isset($this->posts[$title])) {
            return $this->posts[$title];
        }

        return new stdClass();
    }

    /**
     * Set data for return
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setDataResults()
    {
        if (is_array($this->query_results)) {
        } else {
            $this->query_results = array($this->query_results);
        }

        if (is_object($this->parameters)) {
        } else {
            $this->parameters = new stdClass();
        }

        $this->parameters->token      = $this->token;
        $this->parameters->model_type = $this->model_type;
        $this->parameters->model_name = $this->model_name;

        if (isset($this->token->attributes)
            && count($this->token->attributes) > 0
            && is_array($this->token->attributes)
        ) {
            foreach ($this->token->attributes as $key => $value) {
                $this->parameters->$key = $value;
            }
        }

        $data = new stdClass();

        $data->query_results  = $this->query_results;
        $data->model_registry = $this->model_registry;
        $data->parameters     = $this->parameters;

        return $data;
    }

    /**
     * Get Data from Primary Data Collection
     *
     * @param   string $folder
     *
     * @return  array
     * @since   1.0
     */
    protected function getFiles($folder)
    {
        $files = array();

        foreach (glob($folder . '/*.phtml') as $file) {
            if (is_file($file)) {
                $row                    = new stdClass();
                $filename               = basename($file);
                $row->filename          = substr($filename, 0, strlen($filename) - 6);
                $row->folder            = dirname($file);
                $row->path_and_filename = $file;

                $row = $this->getFields(file_get_contents($file), $row);

                $files[$row->filename] = $row;
            }
        }

        return $files;
    }

    /**
     * Get Data from Runtime Data Collection
     *
     * @param   string   $content
     * @param   stdClass $row
     *
     * @return  $this
     * @since   1.0
     */
    protected function getFields($content, $row)
    {
        $row->content  = $this->getContent($content);
        $row->readmore = $this->getReadMore($content);

        $lines = explode(PHP_EOL, substr($content, 0, strrpos($content, '---')));

        foreach ($lines as $line) {
            if (trim($line) == '---' || trim($line) == '') {
            } else {

                $metadata_array = explode(':', $line);

                if (count($metadata_array) > 0) {
                    $key = trim($metadata_array[0]);

                    $value = trim($metadata_array[1]);

                    if (isset($metadata_array[2])) {
                        $value .= ':' . $metadata_array[2];
                    }

                    $row->$key = $value;
                }
            }
        }

        return $row;
    }
}
