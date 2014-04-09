<?php
/**
 * Filesystem Model for Molajito
 *
 * @package    Filesystem Model
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajito\Data;

use CommonApi\Exception\RuntimeException;
use CommonApi\Render\DataInterface;
use CommonApi\Render\PaginationInterface;
use stdClass;

/**
 * Filesystem Model for Molajito
 *
 * @package    Filesystem
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class FilesystemModel extends Filesystem
{

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
        $author_model_registry,
        PaginationInterface $pagination = null
    ) {
        $this->loadPosts($posts_base_folder);
        $this->loadAuthor($author_base_folder);
        $this->post_model_registry   = $post_model_registry;
        $this->author_model_registry = $author_model_registry;
        $this->pagination            = $pagination;
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
     * @return  array
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
        for ($i = 1; $i < 10; $i ++) {

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
     * Pagination View
     *
     * @return  $this
     * @since   1.0
     */
    protected function getPagination()
    {
        $query_parameters = array();
        if ((int)$this->runtime_data->route->parameter_start == 0) {
            $this->runtime_data->route->parameter_start = 1;
        }
        $query_parameters['page'] = 'blog';
        if ($this->runtime_data->route->parameter_category == '') {
        } else {
            $query_parameters['category'] = $this->runtime_data->route->parameter_category;
        }
        if ($this->runtime_data->route->parameter_tag == '') {
        } else {
            $query_parameters['parameter_tag'] = $this->runtime_data->route->parameter_tag;
        }


        $this->pagination->setPagination(
            $this->display_posts,
            $this->runtime_data->route->home,
            $query_parameters,
            $this->display_total_items,
            $this->runtime_data->parameters->posts_per_page,
            $this->runtime_data->parameters->display_links,
            $this->runtime_data->route->parameter_start,
            $this->runtime_data->parameters->sef_url,
            $this->runtime_data->parameters->index_in_url
        );

        $row                       = new stdClass();
        $row->first_page_number    = $this->pagination->getFirstPage();
        $row->first_page_link      = $this->pagination->getPageUrl('first');
        $row->previous_page_number = $this->pagination->getPrevPage();
        $row->previous_page_link   = $this->pagination->getPageUrl('previous');
        $row->current_page_number  = $this->pagination->getCurrentPage();
        $row->current_page_link    = $this->pagination->getPageUrl('current');
        $row->next_page_number     = $this->pagination->getNextPage();
        $row->next_page_link       = $this->pagination->getPageUrl('next');
        $row->last_page_number     = $this->pagination->getLastPage();
        $row->last_page_link       = $this->pagination->getPageUrl('last');
        $row->total_items          = $this->pagination->getTotalItems();
        $row->start_page_number    = $this->pagination->getStartDisplayPage();
        $row->stop_page_number     = $this->pagination->getStopDisplayPage();

        $this->query_results[] = $row;
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

        $i = - 1;
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
                $i ++;
                if ($i < $count) {
                    $this->query_results[] = $post;
                } else {
                    // counting all
                }
            }
        }

        $this->display_posts       = $this->query_results;
        $this->model_registry      = $this->post_model_registry;
        $this->display_total_items = $i;

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
        $author            = $this->author;
        $author->snippet   = $this->getSnippet($author->read_more);
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
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setBreadcrumbs()
    {
        $home      = $this->runtime_data->route->home;
        $home_text = $this->runtime_data->route->home_text;

        $breadcrumbs = array();

        /** Home */
        $row           = new stdClass();
        $row->text     = $home_text;
        $row->link     = $home;
        $breadcrumbs[] = $row;
        $home_row      = $row;

        $this->breadcrumbs[$home] = $breadcrumbs;

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
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setPostURLs($blog_breadcrumbs)
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
            $content = $author->content;

            $author->content = '<p>' . $this->getReadMore($content);
            $author->read_more = '<p>' . $this->getReadMore($content);

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
     * @return  $this
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
     * @return  $this
     * @since   1.0
     */
    protected function getSnippet($content)
    {
        return '<p>'
            . trim(strip_tags(substr(($content), 0, $this->runtime_data->parameters->snippet_length)))
            . '</p>';
    }
}
