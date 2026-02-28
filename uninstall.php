<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all manga posts and their meta
$manga_posts = get_posts([
    'post_type'      => 'manga',
    'posts_per_page' => -1,
    'post_status'    => 'any',
    'fields'         => 'ids',
]);

foreach ($manga_posts as $post_id) {
    wp_delete_post($post_id, true);
}

// Delete all episode posts and their meta
$episode_posts = get_posts([
    'post_type'      => 'episode',
    'posts_per_page' => -1,
    'post_status'    => 'any',
    'fields'         => 'ids',
]);

foreach ($episode_posts as $post_id) {
    wp_delete_post($post_id, true);
}

// Delete all genre terms
$genres = get_terms(['taxonomy' => 'genre', 'hide_empty' => false]);
if (!is_wp_error($genres)) {
    foreach ($genres as $term) {
        wp_delete_term($term->term_id, 'genre');
    }
}

// Delete all manga_status terms
$statuses = get_terms(['taxonomy' => 'manga_status', 'hide_empty' => false]);
if (!is_wp_error($statuses)) {
    foreach ($statuses as $term) {
        wp_delete_term($term->term_id, 'manga_status');
    }
}
