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
        if ($this->token->name == 'Blog'
            || $this->token->name == 'Horizontal'
        ) {
            $this->model_type = 'Blog';
            $this->model_name = 'Posts';

        } elseif ($this->token->name == 'Footer'
            || $this->token->name == 'Navbar'
        ) {

            $this->model_type = 'Menu';
            $this->model_name = 'Navbar';

        } elseif ($this->token->name == 'Categories'
            || $this->token->name == 'Tags'
            || $this->token->name == 'Featured'
        ) {
            $this->model_type = 'Sidebar';
            $this->model_name = $this->token->name;

        } elseif ($this->token->name == 'Breadcrumbs') {

// ???

        } elseif ($this->token->name == 'Tags') {

            $this->model_type = 'Sidebar';
            $this->model_name = 'Tags';
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
        echo '<pre>';
        var_dump($this->token);
        echo '</pre>';

        if ($this->model_type == 'Blog') {
            return $this->getBlogPosts();

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

        $tag_parameter = '';
        $tag_category  = '';

        if (count($parameter_array) > 0) {

            foreach ($parameter_array as $parameter) {

                $temp = explode('=', $parameter);

                if (count($temp) == 2) {

                    if ($temp[0] == 'category') {
                        $tag_category = $temp[1];

                    } elseif ($temp[0] == 'tag') {
                        $tag_parameter = $temp[1];
                    }
                }
            }
        }

        $i = 0;
        foreach ($this->posts as $post) {
            $use_it = false;

            if ($tag_parameter == '' && $tag_category == '') {
                $use_it = true;

            } elseif ($tag_category == '') {

                if (isset($post->tags)) {
                    $post_tags = explode(',', $post->tags);

                    if ($tag_parameter == '' || in_array($tag_parameter, $post_tags)) {
                        $use_it = true;
                    }

                }

            } else {
                if (isset($post->categories)) {
                    $post_categories = explode(',', $post->categories);

                    if ($tag_category == '' || in_array($tag_category, $post_categories)) {
                        $use_it = true;
                    }
                }
            }

            if ($use_it == true) {
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
                    $key       = trim($metadata_array[0]);
                    $value     = trim($metadata_array[1]);
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
}
