<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
namespace Mangora;

class Ajax {

    public static function init() {
        add_action('wp_ajax_mangora_filter_manga', [__CLASS__, 'filter_manga']);
        add_action('wp_ajax_nopriv_mangora_filter_manga', [__CLASS__, 'filter_manga']);
        add_action('wp_ajax_mangora_get_episodes', [__CLASS__, 'get_episodes']);
        add_action('wp_ajax_nopriv_mangora_get_episodes', [__CLASS__, 'get_episodes']);
        add_action('wp_ajax_mangora_get_episode_player', [__CLASS__, 'get_episode_player']);
        add_action('wp_ajax_nopriv_mangora_get_episode_player', [__CLASS__, 'get_episode_player']);
    }

    public static function filter_manga() {
        check_ajax_referer('mangora_filter', 'nonce');

        $genre = isset($_POST['genre']) ? array_map('intval', $_POST['genre']) : [];
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $year = isset($_POST['year']) ? intval($_POST['year']) : 0;
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

        $args = [
            'post_type'      => 'manga',
            'posts_per_page' => 12,
            'paged'          => $paged,
            'post_status'    => 'publish',
        ];

        $tax_query = [];
        $meta_query = [];

        if (!empty($genre)) {
            $tax_query[] = [
                'taxonomy' => 'genre',
                'field'    => 'term_id',
                'terms'    => $genre,
            ];
        }

        if (!empty($status)) {
            $meta_query[] = [
                'key'   => '_manga_status',
                'value' => $status,
            ];
        }

        if ($year > 0) {
            $meta_query[] = [
                'key'   => '_manga_year',
                'value' => $year,
            ];
        }

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        if (!empty($meta_query)) {
            if (count($meta_query) > 1) {
                $meta_query['relation'] = 'AND';
            }
            $args['meta_query'] = $meta_query;
        }

        $query = new \WP_Query($args);
        $results = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $id = get_the_ID();
                $results[] = [
                    'id'        => $id,
                    'title'     => get_the_title(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url($id, 'medium'),
                    'excerpt'   => get_the_excerpt(),
                    'rating'    => get_post_meta($id, '_manga_rating', true),
                    'year'      => get_post_meta($id, '_manga_year', true),
                    'status'    => get_post_meta($id, '_manga_status', true),
                ];
            }
            wp_reset_postdata();
        }

        wp_send_json_success([
            'results'      => $results,
            'total_pages'  => $query->max_num_pages,
            'current_page' => $paged,
        ]);
    }

    public static function get_episodes() {
        check_ajax_referer('mangora_episodes', 'nonce');

        $manga_id = isset($_POST['manga_id']) ? intval($_POST['manga_id']) : 0;

        if (!$manga_id) {
            wp_send_json_error('Invalid manga ID');
        }

        $args = [
            'post_type'      => 'episode',
            'posts_per_page' => 1000,
            'post_status'    => 'publish',
            'meta_key'       => '_episode_number',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
            'meta_query'     => [
                [
                    'key'   => '_episode_manga_id',
                    'value' => $manga_id,
                ],
            ],
        ];

        $episodes = get_posts($args);
        $results = [];

        foreach ($episodes as $episode) {
            $results[] = [
                'id'       => $episode->ID,
                'title'    => sanitize_text_field( $episode->post_title ),
                'number'   => get_post_meta($episode->ID, '_episode_number', true),
                'duration' => get_post_meta($episode->ID, '_episode_duration', true),
            ];
        }

        wp_send_json_success($results);
    }

    public static function get_episode_player() {
        check_ajax_referer('mangora_episodes', 'nonce');

        $episode_id = isset($_POST['episode_id']) ? intval($_POST['episode_id']) : 0;

        if (!$episode_id) {
            wp_send_json_error('Invalid episode ID');
        }

        $video_url = get_post_meta($episode_id, '_episode_video_url', true);
        $episode = get_post($episode_id);

        if (!$video_url || !$episode || 'publish' !== $episode->post_status) {
            wp_send_json_error('Episode not found');
        }

        // Check if URL is embeddable (YouTube, Vimeo, direct video)
        $embed_url = self::get_embed_url($video_url);
        
        // Validate and escape the URL
        $embed_url = esc_url($embed_url);
        if (empty($embed_url)) {
            wp_send_json_error('Invalid video URL');
        }

        wp_send_json_success([
            'title'     => sanitize_text_field($episode->post_title),
            'video_url' => $embed_url,
            'content'   => wp_kses_post(apply_filters('the_content', $episode->post_content)),
        ]);
    }

    private static function get_embed_url($url) {
        // Validate URL first
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }
        
        // YouTube
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . sanitize_text_field($matches[1]);
        }
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . sanitize_text_field($matches[1]);
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . absint($matches[1]);
        }

        // Return as-is for direct video files
        return $url;
    }
}
