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
        'link'  => array(
            'type' => 'url'
        ),
        'title' => array(
            'type' => 'string'
        )
    );

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
     * @param  array  $post_model_registry
     * @param  array  $author_model_registry
     *
     * @since  1.0
     */
    public function __construct(
        $posts_base_folder,
        $author_base_folder,
        $post_model_registry,
        $author_model_registry
    ) {
        $this->loadPosts($posts_base_folder);
        $this->loadAuthor($author_base_folder);
        $this->post_model_registry   = $post_model_registry;
        $this->author_model_registry = $author_model_registry;
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
            $this->setBreadcrumbs();
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
     * Set Breadcrumbs
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setBreadcrumbs()
    {
        $home = $this->runtime_data->route->home;

        $breadcrumbs              = array();
        $breadcrumbs[]            = $home;
        $this->breadcrumbs[$home] = $breadcrumbs;

        $breadcrumbs   = array();
        $breadcrumbs[] = $home;
        $breadcrumbs[] = $this->runtime_data->route->contact;

        $this->breadcrumbs[$this->runtime_data->route->contact] = $breadcrumbs;

        $breadcrumbs   = array();
        $breadcrumbs[] = $home;
        $breadcrumbs[] = $this->runtime_data->route->blog;

        $this->breadcrumbs[$this->runtime_data->route->blog] = $breadcrumbs;

        $breadcrumbs   = array();
        $breadcrumbs[] = $home;
        $breadcrumbs[] = $this->runtime_data->route->about;

        $this->breadcrumbs[$this->runtime_data->route->about] = $breadcrumbs;

        $breadcrumbs   = array();
        $breadcrumbs[] = $home;
        $breadcrumbs[] = $this->runtime_data->route->contact;

        $this->breadcrumbs[$this->runtime_data->route->contact] = $breadcrumbs;

        return $this;
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
        $home = $this->runtime_data->route->home;
        $blog = $this->runtime_data->route->blog;

        if (count($this->posts) == 0) {
            return $this;
        }

        $next_url = '';

        foreach ($this->posts as $post) {

            $post->author_url   = $this->runtime_data->route->contact;
            $post->next_url     = $next_url;
            $post->current_url  = $this->runtime_data->route->post . '&name=' . $post->filename;
            $post->previous_url = '';
            $post->snippet      = $this->getSnippet($post->content);

            $breadcrumbs                           = array();
            $breadcrumbs[]                         = $home;
            $breadcrumbs[]                         = $blog;
            $breadcrumbs[]                         = $post->current_url;
            $this->breadcrumbs[$post->current_url] = $breadcrumbs;

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
        $posts = $this->getFiles($posts_base_folder);
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
            $this->author = $author;
        }

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
     * @param   string $content
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
