<?php
/**
 * Bootstrap
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$theme_base_folder  = $molajito_base . '.dev/Sample/Public/Foundation5';
$view_base_folder   = $molajito_base . '.dev/Sample/Views/Foundation5';
$posts_base_folder  = $molajito_base . '.dev/Sample/Data/Posts';
$author_base_folder = $molajito_base . '.dev/Sample/Data/Profile';

$post_model_registry = array(
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

$author_model_registry = array(
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
