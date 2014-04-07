<?php
/**
 * Bootstrap
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$theme_base_folder   = MOLAJITO_BASE . '.dev/Sample/Public/Foundation5';
$view_base_folder    = MOLAJITO_BASE . '.dev/Sample/Views/Foundation5';
$posts_base_folder   = MOLAJITO_BASE . '.dev/Sample/Data/Posts';
$author_base_folder  = MOLAJITO_BASE . '.dev/Sample/Data/Profile';
$post_model_registry = array(
    'title'             => array(
        'type' => 'string'
    ),
    'subtitle'          => array(
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
    'featured'          => array(
        'type' => 'integer'
    ),
    'video'             => array(
        'type' => 'url'
    ),
    'content'           => array(
        'type' => 'html'
    ),
    'snippet'           => array(
        'type' => 'string'
    ),
    'readmore'          => array(
        'type' => 'html'
    ),
    'filename'          => array(
        'type' => 'string'
    ),
    'folder'            => array(
        'type' => 'string'
    ),
    'path_and_filename' => array(
        'type' => 'string'
    )
);
$author_model_registry = array(
    'name'             => array(
        'type' => 'string'
    ),
    'published'         => array(
        'type' => 'date'
    ),
    'twitter'        => array(
        'type' => 'string'
    ),
    'github'              => array(
        'type' => 'string'
    ),
    'googleplus'          => array(
        'type' => 'string'
    ),
    'gallery_caption1'    => array(
        'type' => 'string'
    ),
    'gallery_image1'      => array(
        'type' => 'url'
    ),
    'gallery_caption2'    => array(
        'type' => 'string'
    ),
    'gallery_image2'      => array(
        'type' => 'url'
    ),
    'gallery_caption3'    => array(
        'type' => 'string'
    ),
    'gallery_image3'      => array(
        'type' => 'url'
    ),
    'gallery_caption4'    => array(
        'type' => 'string'
    ),
    'gallery_image4'      => array(
        'type' => 'url'
    ),
    'gallery_caption5'    => array(
        'type' => 'string'
    ),
    'gallery_image5'      => array(
        'type' => 'url'
    ),
    'gallery_caption6'    => array(
        'type' => 'string'
    ),
    'gallery_image6'      => array(
        'type' => 'url'
    ),
    'gallery_caption7'    => array(
        'type' => 'string'
    ),
    'gallery_image7'      => array(
        'type' => 'url'
    ),
    'gallery_caption8'    => array(
        'type' => 'string'
    ),
    'gallery_image8'      => array(
        'type' => 'url'
    ),
    'gallery_caption9'    => array(
        'type' => 'string'
    ),
    'gallery_image9'      => array(
        'type' => 'url'
    ),
    'content'           => array(
        'type' => 'html'
    ),
    'snippet'           => array(
        'type' => 'string'
    ),
    'readmore'          => array(
        'type' => 'html'
    )
);
