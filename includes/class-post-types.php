<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
namespace Mangora;

class Post_Types {

    public static function init() {
        add_action('init', [__CLASS__, 'register_post_types']);
        add_action('init', [__CLASS__, 'register_taxonomies']);
    }

    public static function register_post_types() {
        // Manga CPT
        $labels = [
            'name'                  => __('Manga', 'mangora'),
            'singular_name'         => __('Manga', 'mangora'),
            'menu_name'             => __('Manga', 'mangora'),
            'add_new'               => __('Add New', 'mangora'),
            'add_new_item'          => __('Add New Manga', 'mangora'),
            'edit_item'             => __('Edit Manga', 'mangora'),
            'new_item'              => __('New Manga', 'mangora'),
            'view_item'             => __('View Manga', 'mangora'),
            'search_items'          => __('Search Manga', 'mangora'),
            'not_found'             => __('No manga found', 'mangora'),
            'not_found_in_trash'    => __('No manga found in trash', 'mangora'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'manga'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'         => false,
            'menu_position'       => 5,
            'menu_icon'          => 'dashicons-book',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
            'show_in_rest'       => true,
        ];

        register_post_type('manga', $args);

        // Episode CPT
        $episode_labels = [
            'name'                  => __('Episodes', 'mangora'),
            'singular_name'         => __('Episode', 'mangora'),
            'menu_name'             => __('Episodes', 'mangora'),
            'add_new'               => __('Add New', 'mangora'),
            'add_new_item'          => __('Add New Episode', 'mangora'),
            'edit_item'             => __('Edit Episode', 'mangora'),
            'new_item'              => __('New Episode', 'mangora'),
            'view_item'             => __('View Episode', 'mangora'),
            'search_items'          => __('Search Episodes', 'mangora'),
            'not_found'             => __('No episodes found', 'mangora'),
        ];

        $episode_args = [
            'labels'             => $episode_labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=manga',
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'supports'           => ['title', 'editor'],
            'show_in_rest'       => true,
        ];

        register_post_type('episode', $episode_args);
    }

    public static function register_taxonomies() {
        // Genre Taxonomy (hierarchical like categories)
        $genre_labels = [
            'name'          => __('Genres', 'mangora'),
            'singular_name' => __('Genre', 'mangora'),
            'search_items'  => __('Search Genres', 'mangora'),
            'all_items'     => __('All Genres', 'mangora'),
            'edit_item'     => __('Edit Genre', 'mangora'),
            'add_new_item'  => __('Add New Genre', 'mangora'),
            'menu_name'     => __('Genres', 'mangora'),
        ];

        register_taxonomy('genre', ['manga'], [
            'labels'        => $genre_labels,
            'hierarchical'  => true,
            'public'        => true,
            'rewrite'       => ['slug' => 'genre'],
            'show_in_rest'  => true,
        ]);

        // Status Taxonomy (non-hierarchical for filtering)
        $status_labels = [
            'name'          => __('Status', 'mangora'),
            'singular_name' => __('Status', 'mangora'),
            'search_items'  => __('Search Status', 'mangora'),
            'all_items'     => __('All Status', 'mangora'),
            'edit_item'     => __('Edit Status', 'mangora'),
            'add_new_item'  => __('Add New Status', 'mangora'),
            'menu_name'     => __('Status', 'mangora'),
        ];

        register_taxonomy('manga_status', ['manga'], [
            'labels'        => $status_labels,
            'hierarchical'  => false,
            'public'        => false,
            'show_ui'       => true,
            'show_in_menu'  => false,
            'show_in_rest'  => true,
        ]);
    }

    public static function seed_status_terms() {
        $statuses = ['Ongoing', 'Completed', 'Hiatus', 'Cancelled'];
        
        foreach ($statuses as $status) {
            if (!term_exists($status, 'manga_status')) {
                wp_insert_term($status, 'manga_status');
            }
        }
    }
}
