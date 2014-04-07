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
 * @since      1.0
 */
class Filesystem extends AbstractAdapter implements DataInterface
{
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
     * Field Name
     *
     * @var    string
     * @since  1.0
     */
    protected $field_name = '';

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
     * Post Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $post_model_registry = array(
        'filename'          => array(
            'typye' => 'string'
        ),
        'folder'            => array(
            'type' => 'string'
        ),
        'path_and_filename' => array(
            'type' => 'string'
        ),
        'content'           => array(
            'type' => 'html'
        ),
        'title'             => array(
            'type' => 'string'
        ),
        'author'            => array(
            'type' => 'string'
        ),
        'published'         => array(
            'type' => 'date'
        ),
        'categories'        => array(
            'type' => 'string'
        ),
        'tags'              => array(
            'type' => 'string'
        ),
        'video'             => array(
            'type' => 'url'
        )
    );

    /**
     * Menu Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $list_model_registry = array(
        'link'  => array(
            'type' => 'url'
        ),
        'title' => array(
            'type' => 'string'
        )
    );

    /**
     * Authors
     *
     * @var    array
     * @since  1.0
     */
    protected $authors = array();

    /**
     * First Data Request indicator
     *
     * @var    boolean
     * @since  1.0
     */
    protected $first_request = true;

    /**
     * Class Constructor
     *
     * @param  string $theme_base_folder
     * @param  string $view_base_folder
     *
     * @since  1.0
     */
    public function __construct(
        $posts_base_folder,
        $authors_base_folder
    ) {
        $this->loadPosts($posts_base_folder);
        $this->loadAuthors($authors_base_folder);
    }

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

        $this->setModel();

        if (strtolower($token->type) == 'page') {
        } else {
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
        $this->field_name     = '';
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
            $this->setPostURLs();
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
        if ($this->token->type == 'page'
        ) {

        } elseif ($this->token->name == 'Blog'
            || $this->token->name == 'Horizontal'
            || $this->token->name == 'Post'
            || $this->token->name == 'Paging'
        ) {
            $this->model_type = 'Blog';
            $this->model_name = 'Posts';

        } elseif ($this->token->name == 'Profile') {
            $this->model_type = 'Author';
            $this->model_name = 'Profile';

        } elseif ($this->token->name == 'Gallery') {
            $this->model_type = 'Author';
            $this->model_name = 'Gallery';

        } elseif ($this->token->name == 'Footer'
            || $this->token->name == 'Navbar'
        ) {
            $this->model_type = 'Menu';
            $this->model_name = 'Navbar';

        } elseif ($this->token->name == 'Comments') {
            $this->model_type = 'Post';
            $this->model_name = 'Comments';

        } elseif ($this->token->name == 'Categories'
            || $this->token->name == 'Tags'
            || $this->token->name == 'Featured'
        ) {
            $this->model_type = 'Sidebar';
            $this->model_name = $this->token->name;

        } elseif ($this->token->name == 'Breadcrumbs') {

// todo

        } elseif ($this->token->name == 'Tags') {
            $this->model_type = 'Sidebar';
            $this->model_name = 'Tags';

        } elseif ($this->token->name == 'Video') {

            $this->model_type = 'Embed';
            $this->model_name = 'Video';

        } elseif ($this->token->name == 'Orbit') {
            $this->model_type = 'Home';
            $this->model_name = 'Orbit';

        } elseif ($this->token->name == 'Pagination') {

//todo

        } elseif ($this->token->name == 'Action') {
            $this->model_type = 'Runtimedata';
            $this->model_name = 'Action';

        } elseif ($this->token->name == 'Contact'
            || $this->token->name = 'Maps'
        ) {
            $this->model_type = 'Runtimedata';
            $this->model_name = 'Contact';

        } else {
            echo '<pre>';
            var_dump($this->token);
            echo '</pre>';
            die;
        }


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
        if ($this->model_type == 'Blog') {
            return $this->getBlogPosts();

        } elseif ($this->model_name == 'Comments') {
            return $this->getComments();

        } elseif ($this->model_type == 'Author') {

            if ($this->model_name == 'Gallery') {
                return $this->getAuthorGallery();
            }

            return $this->getProfile();

        } elseif ($this->model_type == 'Menu') {
            return $this->getMenu();

        } elseif ($this->model_type == 'Sidebar') {

            if ($this->model_name == 'Categories') {
                return $this->getCategories();

            } elseif ($this->model_name == 'Featured') {
                return $this->getFeatured();

            } elseif ($this->model_name == 'Tags') {
                return $this->getTags();
            }

        } elseif ($this->model_type == 'Embed') {

            if ($this->model_name == 'Video') {
                return $this->getVideo();
            }

        } elseif ($this->model_type == 'Home') {

            if ($this->model_name == 'Orbit') {
                return $this->getOrbit();
            }

        } elseif ($this->model_type == 'Runtimedata') {

            if ($this->model_name == 'Action') {
                return $this->getAction();
            }
            if ($this->model_name == 'Contact') {
                return $this->getContact();
            }
        }

        return $this;
    }

    /**
     * Get Author Profile
     *
     * @return  $this
     * @since   1.0
     */
    protected function getProfile()
    {
        foreach ($this->authors as $author) {
        }

        $this->query_results[] = $author;

        return $this;
    }

    /**
     * Get Author Profile
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
     * Get Author Gallery
     *
     * @return  $this
     * @since   1.0
     */
    protected function getAuthorGallery()
    {
        foreach ($this->authors as $author) {
        }

        for ($i = 1; $i < 10; $i ++) {

            $image   = 'gallery_image' . $i;
            $caption = 'gallery_caption' . $i;

            if ($author->$image == '') {
            } else {
                $row = new stdClass();

                $row->gallery_image   = $author->$image;
                $row->gallery_caption = $author->$caption;

                $this->query_results[] = $row;
            }
        }

        return $this;
    }

    /**
     * Get Orbit
     *
     * @return  $this
     * @since   1.0
     */
    protected function getOrbit()
    {
        for ($i = 1; $i < 10; $i ++) {

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
     * Get Blog Posts
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getBlogPosts()
    {
        if (count($this->posts) == 0) {
            return $this;
        }

        $count = (int)$this->runtime_data->parameters->posts_per_page;
        if ($count > 0) {
        } else {
            $count = 3;
        }

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
                        $count          = 1;
                    }
                }
            }
        }

        $query = array();

        $i = 0;
        foreach ($this->posts as $post) {
            $use_it = false;

            if ($tag_parameter == '' && $category_parameter == '' && $name_parameter == '') {
                $use_it = true;

            } elseif ($category_parameter == '' && $tag_parameter == '') {

                if (trim($post->filename) == trim($name_parameter)) {
                    $use_it = true;
                }

            } elseif ($category_parameter == '') {

                if (isset($post->tags)) {
                    $post_tags = explode(',', $post->tags);

                    if ($tag_parameter == '' || in_array($tag_parameter, $post_tags)) {
                        $use_it = true;
                    }

                }

            } else {
                if (isset($post->categories)) {
                    $post_categories = explode(',', $post->categories);

                    if ($category_parameter == '' || in_array($category_parameter, $post_categories)) {
                        $use_it = true;
                    }
                }
            }

            if ($use_it === true) {
                $post->snippet         = $this->getSnippet($post->content);
                $this->query_results[] = $post;
                $i ++;
                if ($i < $count) {
                } else {
                    break;
                }
            }
        }

        $this->model_registry = $this->post_model_registry;

        return $this;
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
     * Get Categories
     *
     * @return  $this
     * @since   1.0
     */
    protected function getCategories()
    {
        foreach ($this->categories as $category => $list) {
            $this->query_results[] = $this->setListRow(
                $this->runtime_data->route->blog . '&category=' . $category,
                ucfirst(strtolower($category))
            );
        }

        $this->model_registry = $this->list_model_registry;

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
        foreach ($this->tags as $tag => $list) {
            $this->query_results[] = $this->setListRow(
                $this->runtime_data->route->blog . '&tag=' . $tag,
                ucfirst(strtolower($tag))
            );
        }

        $this->model_registry = $this->list_model_registry;

        return $this;
    }

    /**
     * Get Categories
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
     * Get Action information (links to Contact on Home Page)
     *
     * @return  $this
     * @since   1.0
     */
    protected function getAction()
    {
        $row = new stdClass();

        $row->action_heading = $this->runtime_data->parameters->action_heading;
        $row->action_message = $this->runtime_data->parameters->action_message;
        $row->action_button  = $this->runtime_data->parameters->action_button;

        $this->query_results[] = $row;

        return $this;
    }

    /**
     * Get Contact information
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
     * Set Rows for Lists (Menu items, categories, tags, etc.)
     *
     * @return  $this
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
        $this->parameters->field_name = $this->field_name;

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
     * Set Previous, Current and Next URL Links
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setPostURLs()
    {
        if (count($this->posts) == 0) {
            return $this;
        }

        $next_url = '';

        $i = 0;
        foreach ($this->posts as $post) {

            $post->next_url     = $next_url;
            $post->current_url  = $this->runtime_data->route->post . '&name=' . $post->filename;
            $post->previous_url = '';

            $next_url = $this->runtime_data->route->post . '&name=' . $post->filename;
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
     * @param   string $posts_base_folder
     *
     * @return  $this
     * @since   1.0
     */
    protected function loadPosts($posts_base_folder)
    {
        $posts = $this->getFolderList($posts_base_folder);
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
     * @param   string $posts_base_folder
     *
     * @return  $this
     * @since   1.0
     */
    protected function loadAuthors($authors_base_folder)
    {
        $files = $this->getFolderList($authors_base_folder);
        arsort($files);
        $this->authors = $files;

        return $this;
    }

    /**
     * Get Data from Primary Data Collection
     *
     * @param   string $folder
     *
     * @return  array
     * @since   1.0
     */
    protected function getFolderList($folder)
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
     * @param   string $file
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
     * @param   string $file
     *
     * @return  $this
     * @since   1.0
     */
    protected function getReadMore($content)
    {
        $content = trim(substr($content, strrpos($content, '---') + 3, 9999));

        return trim(substr($content, 0, strrpos($content, '{{readmore}}')));
    }

    /**
     * Get Data from Runtime Data Collection
     *
     * @param   string $file
     *
     * @return  $this
     * @since   1.0
     */
    protected function getSnippet($content)
    {
        return '<p>'
        . strip_tags(trim(substr($content, 0, $this->runtime_data->parameters->snippet_length)))
        . '...</p>';
    }
}
