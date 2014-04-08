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
        return $this->getContact();
    }

    /**
     * Get Breadcrumbs
     *
     * @return  $this
     * @since   1.0
     */
    protected function getBreadcrumbs()
    {
        $current_page = $this->runtime_data->route->blog;

        if (isset($this->breadcrumbs[$current_page])) {
        } else {
            return $this;
        }

        $breadcrumbs = $this->breadcrumbs[$current_page];

        $current = count($breadcrumbs);
        $i       = 1;
        foreach ($breadcrumbs as $key => $value) {
            $row          = new stdClass();
            $row->url     = $value;

            if ($i == $current) {
                $row->current = 1;
            } else {
                $row->current = 0;
            }

            $this->query_results[] = $row;

            $i++;
        }
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
        return array();
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
     * Get Author Profile
     *
     * @return  $this
     * @since   1.0
     */
    protected function getProfile()
    {
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
}
