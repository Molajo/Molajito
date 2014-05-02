<?php
/**
 * Blog Example Data Adapter for Molajito
 *
 * @package    Blog
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Data;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\PaginationInterface;
use CommonApi\Render\DataInterface;
use stdClass;

/**
 * Blog Example Data Adapter for Molajito
 *
 * @package    Blog
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Blog extends AbstractAdapter implements DataInterface
{
    /**
     * Runtime Data
     *
     * @var    object
     * @since  1.0.0
     */
    protected $runtime_data = NULL;

    /**
     * Token
     *
     * @var    object
     * @since  1.0.0
     */
    protected $token = NULL;

    /**
     * Model Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_type = '';

    /**
     * Model Name
     *
     * @var    string
     * @since  1.0.0
     */
    protected $model_name = '';

    /**
     * Query Results
     *
     * @var    array
     * @since  1.0.0
     */
    protected $query_results = array();

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0.0
     */
    protected $model_registry = array();

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0.0
     */
    protected $parameters = NULL;

    /**
     * Author Profile
     *
     * @var    object
     * @since  1.0.0
     */
    protected $author = NULL;

    /**
     * Posts
     *
     * @var    array
     * @since  1.0.0
     */
    protected $posts = array();

    /**
     * Categories
     *
     * @var    array
     * @since  1.0.0
     */
    protected $categories = array();

    /**
     * Tags
     *
     * @var    array
     * @since  1.0.0
     */
    protected $tags = array();

    /**
     * Featured
     *
     * @var    array
     * @since  1.0.0
     */
    protected $featured = array();

    /**
     * Breadcrumbs
     *
     * @var    array
     * @since  1.0.0
     */
    protected $breadcrumbs = array();

    /**
     * Holds primary display posts
     *
     * @var    array
     * @since  1.0.0
     */
    protected $display_items_per_page_count = array();

    /**
     * Count of the total items for pagination
     *  -> not all will necessarily be displayed
     *
     * @var    array
     * @since  1.0.0
     */
    protected $total_items = 0;

    /**
     * Post Model Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $post_model_registry = array(
        'title'             => array('name' => 'title', 'type' => 'string'),
        'subtitle'          => array('name' => 'subtitle', 'type' => 'string'),
        'author'            => array('name' => 'author', 'type' => 'string'),
        'published'         => array('name' => 'published', 'type' => 'date'),
        'categories'        => array('name' => 'categories', 'type' => 'string'),
        'tags'              => array('name' => 'tags', 'type' => 'string'),
        'featured'          => array('name' => 'featured', 'type' => 'integer'),
        'video'             => array('name' => 'video', 'type' => 'string'),
        'content'           => array('name' => 'content', 'type' => 'html'),
        'snippet'           => array('name' => 'snippet', 'type' => 'html'),
        'readmore'          => array('name' => 'readmore', 'type' => 'html'),
        'filename'          => array('name' => 'filename', 'type' => 'string'),
        'folder'            => array('name' => 'folder', 'type' => 'string'),
        'path_and_filename' => array('name' => 'path_and_filename', 'type' => 'string')
    );

    /**
     * Author Model Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $author_model_registry = array(
        'name'              => array('name' => 'name', 'type' => 'string'),
        'published'         => array('name' => 'published', 'type' => 'date'),
        'twitter'           => array('name' => 'twitter', 'type' => 'string'),
        'github'            => array('name' => 'github', 'type' => 'string'),
        'googleplus'        => array('name' => 'googleplus', 'type' => 'string'),
        'gallery_caption1'  => array('name' => 'gallery_caption1', 'type' => 'string'),
        'gallery_image1'    => array('name' => 'gallery_image1', 'type' => 'url'),
        'gallery_caption2'  => array('name' => 'gallery_caption2', 'type' => 'string'),
        'gallery_image2'    => array('name' => 'gallery_image2', 'type' => 'url'),
        'gallery_caption3'  => array('name' => 'gallery_caption3', 'type' => 'string'),
        'gallery_image3'    => array('name' => 'gallery_image3', 'type' => 'url'),
        'gallery_caption4'  => array('name' => 'gallery_caption4', 'type' => 'string'),
        'gallery_image4'    => array('name' => 'gallery_image4', 'type' => 'url'),
        'gallery_caption5'  => array('name' => 'gallery_caption5', 'type' => 'string'),
        'gallery_image5'    => array('name' => 'gallery_image5', 'type' => 'url'),
        'gallery_caption6'  => array('name' => 'gallery_caption6', 'type' => 'string'),
        'gallery_image6'    => array('name' => 'gallery_image6', 'type' => 'url'),
        'gallery_caption7'  => array('name' => 'gallery_caption7', 'type' => 'string'),
        'gallery_image7'    => array('name' => 'gallery_image7', 'type' => 'url'),
        'gallery_caption8'  => array('name' => 'gallery_caption8', 'type' => 'string'),
        'gallery_image8'    => array('name' => 'gallery_image8', 'type' => 'url'),
        'gallery_caption9'  => array('name' => 'gallery_caption9', 'type' => 'string'),
        'gallery_image9'    => array('name' => 'gallery_image9', 'type' => 'url'),
        'content'           => array('name' => 'content', 'type' => 'html'),
        'snippet'           => array('name' => 'snippet', 'type' => 'html'),
        'readmore'          => array('name' => 'readmore', 'type' => 'html'),
        'filename'          => array('name' => 'filename', 'type' => 'string'),
        'folder'            => array('name' => 'folder', 'type' => 'string'),
        'path_and_filename' => array('name' => 'path_and_filename', 'type' => 'string')
    );
    /**
     * Menu Model Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $list_model_registry = array(
        'link'  => array('name' => 'link', 'type' => 'url'),
        'title' => array('name' => 'title', 'type' => 'string')
    );

    /**
     * Pagination Model Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $pagination_model_registry = array(
        'first_page_number'              => array('name' => 'first_page_number', 'type' => 'integer'),
        'first_page_link'                => array('name' => 'first_page_link', 'type' => 'url'),
        'previous_page_number'           => array('name' => 'previous_page_number', 'type' => 'integer'),
        'previous_page_link'             => array('name' => 'previous_page_link', 'type' => 'url'),
        'current_start_parameter_number' => array('name' => 'current_start_parameter_number', 'type' => 'integer'),
        'current_page_link'              => array('name' => 'current_page_link', 'type' => 'url'),
        'next_page_number'               => array('name' => 'next_page_number', 'type' => 'integer'),
        'next_page_link'                 => array('name' => 'next_page_link', 'type' => 'url'),
        'last_page_number'               => array('name' => 'last_page_number', 'type' => 'integer'),
        'last_page_link'                 => array('name' => 'last_page_link', 'type' => 'url'),
        'total_items'                    => array('name' => 'total_items', 'type' => 'integer'),
        'start_links_page_number'        => array('name' => 'start_links_page_number', 'type' => 'integer'),
        'stop_links_page_number'         => array('name' => 'stop_links_page_number', 'type' => 'integer'),
        'page_links_array'               => array('name' => 'page_links_array', 'type' => 'arrays')
    );

    /**
     * First Data Request indicator
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $first_request = TRUE;

    /**
     * Class Constructor
     *
     * @param  array               $options
     * @param  PaginationInterface $pagination
     *
     * @since  1.0.0
     */
    public function __construct(
        PaginationInterface $pagination = NULL,
        array $options = array()
    ) {
        parent::__construct($pagination);

        if (isset($options['data_folder'])) {
            $this->loadPosts($options['data_folder']);
            $this->loadAuthor($options['data_folder'] . '/Author');
        }
    }

    /**
     * Get Data for Rendering
     *
     * @param   object $token
     * @param   array  $options
     *
     * @return  stdClass
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
        $this->model_type     = '';
        $this->model_name     = '';
        $this->query_results  = array();
        $this->model_registry = array();
        $this->token          = $token;
        $this->runtime_data   = $options['runtime_data'];
        $this->parameters     = new stdClass();

        if ($this->first_request === TRUE) {
            $this->first_request = FALSE;
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
        $this->model_type = 'Blog';
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
        $row            = new stdClass();
        $row->link      = $link;
        $row->title     = $title;
        $row->home_link = $this->runtime_data->route->home;

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
     * @return  stdClass
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

        $this->parameters = $this->setParametersFromToken($this->token, $this->parameters);

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
     * @return  stdClass
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

    /**
     * Get Action
     *
     * @return  $this
     * @since   1.0
     */
    protected function getAction()
    {
        $row = new stdClass();

        $row->title       = $this->runtime_data->parameters->action_heading;
        $row->content     = $this->runtime_data->parameters->action_message;
        $row->button_text = $this->runtime_data->parameters->action_button;

        $this->query_results[] = $row;

        return $this;
    }

    /**
     * Get Author
     *
     * @return  $this
     * @since   1.0
     */
    protected function getAuthor()
    {
        return $this->getProfile();
    }

    /**
     * Get Breadcrumbs
     *
     * @return  $this
     * @since   1.0
     */
    protected function getBreadcrumbs()
    {
        $current_page = $this->runtime_data->breadcrumb_current_url;

        if (isset($this->breadcrumbs[$current_page])) {
        } else {
            return $this;
        }

        $this->query_results = $this->breadcrumbs[$current_page];

        return $this;
    }

    /**
     * Get Blog
     *
     * @return  $this
     * @since   1.0
     */
    protected function getBlog()
    {
        return $this->getPosts();
    }

    /**
     * Get Categories
     *
     * @return  $this
     * @since   1.0
     */
    protected function getCategories()
    {
        return $this->getCategoryTagListRow('categories');
    }

    /**
     * Get Comments
     *
     * @return  $this
     * @since   1.0
     */
    protected function getComments()
    {
        if (trim($this->runtime_data->parameters->disqus_name) == '') {
            return $this;
        }

        $row                   = new stdClass();
        $row->disqus_name      = $this->runtime_data->parameters->disqus_name;
        $this->query_results[] = $row;

        return $this;
    }

    /**
     * Get Contact
     *
     * @return  $this
     * @since   1.0
     */
    protected function getContact()
    {
        $row = new stdClass();

        $row->title       = $this->runtime_data->parameters->contact_heading;
        $row->content     = $this->runtime_data->parameters->contact_message;
        $row->map         = $this->runtime_data->parameters->map;
        $row->map_address = $this->runtime_data->parameters->map_address;

        $this->query_results[] = $row;

        return $this;
    }

    /**
     * Get Featured
     *
     * @return  $this
     * @since   1.0
     */
    protected function getFeatured()
    {
        if (count($this->featured) > 0) {
        } else {
            return $this;
        }

        foreach ($this->featured as $item) {
            $this->query_results[] = $this->setItemRow($item);
        }

        $this->model_registry = $this->list_model_registry;

        return $this;
    }

    /**
     * Footer View
     *
     * @return  $this
     * @since   1.0
     */
    protected function getFooter()
    {
        return $this->getMenu();
    }

    /**
     * Get Gallery
     *
     * @return  $this
     * @since   1.0
     */
    protected function getGallery()
    {
        for ($i = 1; $i < 10; $i++) {

            $image   = 'gallery_image' . $i;
            $caption = 'gallery_caption' . $i;

            if ($this->author->$image == '') {
            } else {
                $row = new stdClass();

                $row->gallery_image   = $this->author->$image;
                $row->gallery_caption = $this->author->$caption;

                $this->query_results[] = $row;
            }
        }

        return $this;
    }

    /**
     * Horizontal View
     *
     * @return  $this
     * @since   1.0
     */
    protected function getHorizontal()
    {
        return $this->getPosts();
    }

    /**
     * Maps View
     *
     * @return  $this
     * @since   1.0
     */
    protected function getMaps()
    {
        return $this->getContact();
    }

    /**
     * Get Menu
     *
     * @return  $this
     * @since   1.0
     */
    protected function getMenu()
    {
        $this->query_results[] = $this->setListRow($this->runtime_data->route->home, 'Home');
        $this->query_results[] = $this->setListRow($this->runtime_data->route->blog, 'Blog');
        $this->query_results[] = $this->setListRow($this->runtime_data->route->about, 'About');
        $this->query_results[] = $this->setListRow($this->runtime_data->route->contact, 'Contact');

        $this->model_registry = $this->list_model_registry;

        return $this;
    }

    /**
     * Navbar View
     *
     * @return  $this
     * @since   1.0
     */
    protected function getNavbar()
    {
        return $this->getMenu();
    }

    /**
     * Get Orbit
     *
     * @return  $this
     * @since   1.0
     */
    protected function getOrbit()
    {
        for ($i = 1; $i < 10; $i++) {

            $image = 'orbit_image' . $i;

            if ($this->runtime_data->parameters->$image == '') {
            } else {
                $row = new stdClass();

                $row->image = $this->runtime_data->parameters->$image;

                $this->query_results[] = $row;
            }
        }

        return $this;
    }

    /**
     * Pagination View
     *
     * @return  $this
     * @since   1.0
     */
    protected function getPagination()
    {
        $other_query_parameters = array();
        if ((int)$this->runtime_data->route->parameter_start == 0) {
            $this->runtime_data->route->parameter_start = 1;
        }
        $other_query_parameters['page'] = 'blog';
        if ($this->runtime_data->route->parameter_category == '') {
        } else {
            $other_query_parameters['category'] = $this->runtime_data->route->parameter_category;
        }
        if ($this->runtime_data->route->parameter_tag == '') {
        } else {
            $other_query_parameters['parameter_tag'] = $this->runtime_data->route->parameter_tag;
        }

        $row = $this->pagination->getPaginationData(
            $this->runtime_data->parameters->display_items_per_page_count,
            $this->runtime_data->parameters->display_page_link_count,
            $this->runtime_data->parameters->create_sef_url_indicator,
            $this->runtime_data->parameters->display_index_in_url_indicator,
            $this->total_items,
            $this->runtime_data->route->home,
            $current_page = $this->runtime_data->route->parameter_start,
            $other_query_parameters
        );

        $this->query_results[] = $row;
        $this->model_registry  = $this->pagination_model_registry;

        return $this;
    }

    /**
     * Paging View
     *
     * @return  $this
     * @since   1.0
     */
    protected function getPaging()
    {
        return $this->getPosts();
    }

    /**
     * Post View
     *
     * @return  $this
     * @since   1.0
     */
    protected function getPost()
    {
        return $this->getPosts();
    }

    /**
     * Get Blog Posts
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getPosts()
    {
        if (count($this->posts) == 0) {
            return $this;
        }

        $start                         = $this->setPaginationStart();
        $display_items_per_page_count  = $this->setPaginationPostsPerPage();
        $display_page_link_count       = $this->setPaginationDisplayLinkCount();
        $skip_count                    = ($start * $display_items_per_page_count) - $display_items_per_page_count;
        $display_page_link_count_count = $display_page_link_count * $display_items_per_page_count;

        /** Parameter Array */
        $parameter_array = $this->setPostSelectionCriteria();

        $tag_parameter      = $parameter_array['tag_parameter'];
        $category_parameter = $parameter_array['category_parameter'];
        $name_parameter     = $parameter_array['name_parameter'];

        $total_posts  = 0;
        $return_count = 0;

        foreach ($this->posts as $post) {

            $use_it = $this->setUseItFlag($post, $tag_parameter, $category_parameter, $name_parameter);

            if ($use_it === TRUE) {

                $total_posts++;

                if ($skip_count < $total_posts) {

                    if ($skip_count + $display_page_link_count_count < $total_posts) {
                        // items following display

                    } else {
                        // display links
                        if ($display_items_per_page_count > $return_count) {
                            $return_count++;
                            $this->query_results[] = $post;
                        } else {
                            // counting display links -- not all display
                        }
                    }

                } else {
                    // items previous to display
                }
            }
        }

        $this->model_registry = $this->post_model_registry;
        $this->total_items    = $total_posts;

        return $this;
    }

    /**
     * Set Pagination Start
     *
     * @return  integer
     * @since   1.0
     */
    protected function setPaginationStart()
    {
        if ((int)$this->runtime_data->route->parameter_start == 0) {
            $this->runtime_data->route->parameter_start = 1;
        }

        return (int)$this->runtime_data->route->parameter_start;
    }

    /**
     * Set Pagination Posts per page
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPaginationPostsPerPage()
    {
        if ((int)$this->runtime_data->parameters->display_items_per_page_count > 0) {
        } else {
            $this->runtime_data->parameters->display_items_per_page_count = 3;
        }

        return $this->runtime_data->parameters->display_items_per_page_count;
    }

    /**
     * Set Pagination Display Link Count
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPaginationDisplayLinkCount()
    {
        if ((int)$this->runtime_data->parameters->display_page_link_count > 0) {
        } else {
            $this->runtime_data->parameters->display_page_link_count = 3;
        }

        return $this->runtime_data->parameters->display_page_link_count;
    }

    /**
     * Using Parameters values, determine selection criteria for posts
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setPostSelectionCriteria()
    {
        /** Parameter Array */
        $parameter_array = $this->runtime_data->route->parameter_array;

        $tag_parameter      = '';
        $category_parameter = '';
        $name_parameter     = '';

        if (count($parameter_array) > 0) {

            foreach ($parameter_array as $parameter) {

                $temp = explode('=', $parameter);

                if (count($temp) == 2) {

                    if ($temp[0] == 'category') {
                        $category_parameter = $temp[1];

                    } elseif ($temp[0] == 'tag') {
                        $tag_parameter = $temp[1];

                    } elseif ($temp[0] == 'name') {
                        $name_parameter = $temp[1];
                    }
                }
            }
        }

        return array(
            'tag_parameter'      => $tag_parameter,
            'category_parameter' => $category_parameter,
            'name_parameter'     => $name_parameter
        );
    }

    /**
     * Given the parameter values, determine if the post qualifies
     *
     * @param   string $post
     * @param   string $tag_parameter
     * @param   string $category_parameter
     * @param   string $name_parameter
     *
     * @return  boolean
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setUseItFlag($post, $tag_parameter, $category_parameter, $name_parameter)
    {
        $use_it = FALSE;

        if ($tag_parameter == '' && $category_parameter == '' && $name_parameter == '') {
            $use_it = TRUE;

        } elseif ($category_parameter == '' && $tag_parameter == '') {

            if (trim($post->filename) == trim($name_parameter)) {
                $use_it = TRUE;
            }

        } elseif ($category_parameter == '') {

            if (isset($post->tags)) {
                $post_tags = explode(',', $post->tags);

                if ($tag_parameter == '' || in_array($tag_parameter, $post_tags)) {
                    $use_it = TRUE;
                }
            }

        } else {

            if (isset($post->categories)) {
                $post_categories = explode(',', $post->categories);

                if ($category_parameter == '' || in_array($category_parameter, $post_categories)) {
                    $use_it = TRUE;
                }
            }
        }

        return $use_it;
    }

    /**
     * Get Author Profile
     *
     * @return  $this
     * @since   1.0
     */
    protected function getProfile()
    {
        $author                = $this->author;
        $author->snippet       = $this->getSnippet($author->read_more);
        $this->query_results[] = $this->author;

        return $this;
    }

    /**
     * Get Tags
     *
     * @return  $this
     * @since   1.0
     */
    protected function getTags()
    {
        return $this->getCategoryTagListRow('tags');
    }

    /**
     * Get Categories or Tags List Rows
     *
     * @param   string  $type
     *
     * @return  $this
     * @since   1.0
     */
    protected function getCategoryTagListRow($type = 'tags')
    {
        if ($type === 'categories') {
            $slug = 'category';
        } else {
            $type = 'tags';
            $slug = 'tag';
        }

        foreach ($this->$type as $value => $list) {

            $this->query_results[] = $this->setListRow(
                $this->runtime_data->route->blog . '&' . $slug . '=' . $value,
                ucfirst(strtolower($value))
            );
        }

        $this->model_registry = $this->list_model_registry;

        return $this;
    }

    /**
     * Get Video
     *
     * @return  $this
     * @since   1.0
     */
    protected function getVideo()
    {
        $row = new stdClass();

        if (isset($this->token->attributes['link'])) {
            $row->link = $this->token->attributes['link'];
        }

        $this->query_results[] = $row;

        return $this;
    }

    /**
     * Set Breadcrumbs
     *
     * @return  stdClass
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setBreadcrumbs()
    {
        $breadcrumbs = array();

        /** Home */
        $row           = new stdClass();
        $row->text     = $this->runtime_data->route->home_text;
        $row->link     = $this->runtime_data->route->home;
        $breadcrumbs[] = $row;
        $home_row      = $row;

        $this->breadcrumbs[$this->runtime_data->route->home] = $breadcrumbs;

        /** Blog */
        $blog_breadcrumbs   = array();
        $blog_breadcrumbs[] = $home_row;

        $row                = new stdClass();
        $row->text          = $this->runtime_data->route->blog_text;
        $row->link          = $this->runtime_data->route->blog;
        $blog_breadcrumbs[] = $row;

        $this->breadcrumbs[$this->runtime_data->route->blog] = $blog_breadcrumbs;

        /** Contact */
        $breadcrumbs   = array();
        $breadcrumbs[] = $home_row;

        $row           = new stdClass();
        $row->text     = $this->runtime_data->route->contact_text;
        $row->link     = $this->runtime_data->route->contact;
        $breadcrumbs[] = $row;

        $this->breadcrumbs[$this->runtime_data->route->contact] = $breadcrumbs;

        /** About */
        $breadcrumbs   = array();
        $breadcrumbs[] = $home_row;

        $row           = new stdClass();
        $row->text     = $this->runtime_data->route->about_text;
        $row->link     = $this->runtime_data->route->about;
        $breadcrumbs[] = $row;

        $this->breadcrumbs[$this->runtime_data->route->about] = $breadcrumbs;

        return $blog_breadcrumbs;
    }

    /**
     * Set Previous, Current and Next URL Links
     *
     * @param   array $blog_breadcrumbs
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setPostURLs($blog_breadcrumbs)
    {
        if (count($this->posts) == 0) {
            return $this;
        }

        $next_url = '';

        foreach ($this->posts as $post) {

            $post->author_url   = $this->runtime_data->route->contact;
            $post->next_url     = $next_url;
            $post->current_url  = $this->runtime_data->route->blog . '&name=' . $post->filename;
            $post->previous_url = '';
            $post->snippet      = $this->getSnippet($post->content);

            $breadcrumbs   = $blog_breadcrumbs;
            $row           = new stdClass();
            $row->text     = $post->title;
            $row->link     = $post->current_url;
            $breadcrumbs[] = $row;

            $this->breadcrumbs[$post->current_url] = $breadcrumbs;

            $next_url = $this->runtime_data->route->blog . '&name=' . $post->filename;
        }

        $hold = array();
        foreach ($this->posts as $item) {
            if (trim($item->next_url) == '') {
            } else {
                $hold[$item->next_url] = $item->current_url;
            }
        }

        foreach ($this->posts as $post) {

            if (isset($hold[$post->current_url])) {
                $post->previous_url = $hold[$post->current_url];
            }
        }

        return $this;
    }

    /**
     * Load Posts
     *
     * @param   string $data_folder
     *
     * @return  $this
     * @since   1.0
     */
    protected function loadPosts($data_folder)
    {
        $posts = $this->getFiles($data_folder);
        arsort($posts);
        $this->posts = $posts;

        if (count($posts) > 0) {
        } else {
            return $this;
        }

        $list_categories = array();
        $list_tags       = array();
        $list_featured   = array();

        foreach ($posts as $post) {

            if (isset($post->categories)) {
                $temp = explode(',', $post->categories);
                if (count($temp) > 0) {
                    foreach ($temp as $category) {
                        $category = strtolower(trim($category));
                        if (trim($category) == '') {
                        } else {
                            if (isset($list_categories[$category])) {
                                $temp_list = $list_categories[$category];
                            } else {
                                $temp_list = array();
                            }
                            $temp_list[]                = $post->filename;
                            $list_categories[$category] = $temp_list;
                        }
                    }
                }
            }

            if (isset($post->tags)) {
                $temp = explode(',', $post->tags);
                if (count($temp) > 0) {
                    foreach ($temp as $tag) {
                        $tag = strtolower(trim($tag));
                        if (trim($tag) == '') {
                        } else {
                            if (isset($list_tags[$tag])) {
                                $temp_list = $list_tags[$tag];
                            } else {
                                $temp_list = array();
                            }
                            $temp_list[]     = $post->filename;
                            $list_tags[$tag] = $temp_list;
                        }
                    }
                }
            }

            if (isset($post->featured)) {
                if ((int)$post->featured == 1) {
                    $list_featured[] = $post->filename;
                }
            }
        }

        ksort($list_categories);
        $this->categories = $list_categories;

        ksort($list_tags);
        $this->tags = $list_tags;

        $this->featured = $list_featured;

        return $this;
    }

    /**
     * Load Authors
     *
     * @param   string $author_base_folder
     *
     * @return  $this
     * @since   1.0
     */
    protected function loadAuthor($author_base_folder)
    {
        $temp = $this->getFiles($author_base_folder);

        foreach ($temp as $author) {
            $content = $author->content;

            $author->content   = $this->getReadMore($content);
            $author->read_more = $this->getReadMore($content);

            $this->author = $author;
        }

        return $this;
    }

    /**
     * Get Content from file input
     *
     * @param   string $content
     *
     * @return  string
     * @since   1.0
     */
    protected function getContent($content)
    {
        $content = trim(substr($content, strrpos($content, '---') + 3, 9999));

        return str_replace('{{readmore}}', '', $content);
    }

    /**
     * Get Data from Runtime Data Collection
     *
     * @param   string $content
     *
     * @return  string
     * @since   1.0
     */
    protected function getReadMore($content)
    {
        $content = trim(substr($content, strrpos($content, '---') + 3, 9999));

        if (strrpos($content, '{{readmore}}')) {
            return trim(substr($content, 0, strpos($content, '{{readmore}}')));
        }

        return $content;
    }

    /**
     * Get Data from Runtime Data Collection
     *
     * @param   string $content
     *
     * @return  string
     * @since   1.0
     */
    protected function getSnippet($content)
    {
        return '<p>'
        . trim(strip_tags(substr(($content), 0, $this->runtime_data->parameters->snippet_length)))
        . '</p>';
    }
}
