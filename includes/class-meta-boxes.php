<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
namespace Mangora;

class Meta_Boxes {

    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post', [__CLASS__, 'save_manga_meta'], 10, 2);
        add_action('save_post', [__CLASS__, 'save_episode_meta'], 10, 2);
        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_scripts']);
    }

    public static function add_meta_boxes() {
        // Manga meta box
        add_meta_box(
            'mangora_manga_details',
            __('Manga Details', 'mangora'),
            [__CLASS__, 'render_manga_meta_box'],
            'manga',
            'normal',
            'high'
        );

        // Episode meta box
        add_meta_box(
            'mangora_episode_details',
            __('Episode Details', 'mangora'),
            [__CLASS__, 'render_episode_meta_box'],
            'episode',
            'normal',
            'high'
        );
    }

    public static function render_manga_meta_box($post) {
        wp_nonce_field('mangora_manga_meta', 'mangora_manga_nonce');

        $year = get_post_meta($post->ID, '_manga_year', true);
        $rating = get_post_meta($post->ID, '_manga_rating', true);
        $status = get_post_meta($post->ID, '_manga_status', true);
        $statuses = get_terms(['taxonomy' => 'manga_status', 'hide_empty' => false]);
        ?>
        <table class="form-table mangora-meta-table">
            <tr>
                <th><label for="manga_year"><?php esc_html_e('Release Year', 'mangora'); ?></label></th>
                <td>
                    <input type="number" id="manga_year" name="manga_year" 
                           value="<?php echo esc_attr($year); ?>" min="1900" max="2099" class="small-text">
                </td>
            </tr>
            <tr>
                <th><label for="manga_rating"><?php esc_html_e('Rating', 'mangora'); ?></label></th>
                <td>
                    <input type="number" id="manga_rating" name="manga_rating" 
                           value="<?php echo esc_attr($rating); ?>" min="0" max="10" step="0.1" class="small-text">
                    <span class="description"><?php esc_html_e('0-10 scale', 'mangora'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="manga_status"><?php esc_html_e('Status', 'mangora'); ?></label></th>
                <td>
                    <select id="manga_status" name="manga_status">
                        <option value=""><?php esc_html_e('Select Status', 'mangora'); ?></option>
                        <?php 
                        if (!is_wp_error($statuses) && !empty($statuses)) :
                            foreach ($statuses as $term) : 
                        ?>
                            <option value="<?php echo esc_attr($term->slug); ?>" 
                                <?php selected($status, $term->slug); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function render_episode_meta_box($post) {
        wp_nonce_field('mangora_episode_meta', 'mangora_episode_nonce');

        $manga_id = get_post_meta($post->ID, '_episode_manga_id', true);
        $episode_number = get_post_meta($post->ID, '_episode_number', true);
        $video_url = get_post_meta($post->ID, '_episode_video_url', true);
        $duration = get_post_meta($post->ID, '_episode_duration', true);

        // Get manga for dropdown (limited for performance)
        $manga = get_posts([
            'post_type' => 'manga',
            'posts_per_page' => 500,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => 'publish',
        ]);
        ?>
        <table class="form-table mangora-meta-table">
            <tr>
                <th><label for="episode_manga_id"><?php esc_html_e('Manga', 'mangora'); ?></label></th>
                <td>
                    <select id="episode_manga_id" name="episode_manga_id" required>
                        <option value=""><?php esc_html_e('Select Manga', 'mangora'); ?></option>
                        <?php foreach ($manga as $m) : ?>
                            <option value="<?php echo esc_attr($m->ID); ?>" 
                                <?php selected($manga_id, $m->ID); ?>>
                                <?php echo esc_html($m->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="episode_number"><?php esc_html_e('Episode Number', 'mangora'); ?></label></th>
                <td>
                    <input type="number" id="episode_number" name="episode_number" 
                           value="<?php echo esc_attr($episode_number); ?>" step="0.1" class="small-text" required>
                    <span class="description"><?php esc_html_e('E.g., 1, 1.5, OVA use 0', 'mangora'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="episode_video_url"><?php esc_html_e('Video URL', 'mangora'); ?></label></th>
                <td>
                    <input type="url" id="episode_video_url" name="episode_video_url" 
                           value="<?php echo esc_attr($video_url); ?>" class="large-text" 
                           placeholder="https://...">
                    <p class="description">
                        <?php esc_html_e('Direct video URL or embeddable player URL', 'mangora'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="episode_duration"><?php esc_html_e('Duration', 'mangora'); ?></label></th>
                <td>
                    <input type="text" id="episode_duration" name="episode_duration" 
                           value="<?php echo esc_attr($duration); ?>" class="small-text" 
                           placeholder="24:00">
                </td>
            </tr>
        </table>
        <?php
    }

    public static function save_manga_meta($post_id, $post) {
        if (!isset($_POST['mangora_manga_nonce']) || 
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mangora_manga_nonce'])), 'mangora_manga_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_type !== 'manga' || !current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save year
        if (isset($_POST['manga_year'])) {
            $year = absint(wp_unslash($_POST['manga_year']));
            if ($year >= 1900 && $year <= 2099) {
                update_post_meta($post_id, '_manga_year', $year);
            }
        }

        // Save rating
        if (isset($_POST['manga_rating'])) {
            $rating = floatval(wp_unslash($_POST['manga_rating']));
            if ($rating >= 0 && $rating <= 10) {
                update_post_meta($post_id, '_manga_rating', $rating);
            }
        }

        // Save status
        if (isset($_POST['manga_status'])) {
            $status = sanitize_text_field(wp_unslash($_POST['manga_status']));
            update_post_meta($post_id, '_manga_status', $status);
        }
    }

    public static function save_episode_meta($post_id, $post) {
        if (!isset($_POST['mangora_episode_nonce']) || 
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mangora_episode_nonce'])), 'mangora_episode_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_type !== 'episode' || !current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save manga ID with validation
        if (isset($_POST['episode_manga_id'])) {
            $manga_id = absint(wp_unslash($_POST['episode_manga_id']));
            if ($manga_id > 0 && get_post_type($manga_id) === 'manga') {
                update_post_meta($post_id, '_episode_manga_id', $manga_id);
            }
        }

        // Save episode number
        if (isset($_POST['episode_number'])) {
            $episode_number = floatval(wp_unslash($_POST['episode_number']));
            if ($episode_number >= 0) {
                update_post_meta($post_id, '_episode_number', $episode_number);
            }
        }

        // Save video URL
        if (isset($_POST['episode_video_url'])) {
            $video_url = esc_url_raw(wp_unslash($_POST['episode_video_url']));
            update_post_meta($post_id, '_episode_video_url', $video_url);
        }

        // Save duration
        if (isset($_POST['episode_duration'])) {
            $duration = sanitize_text_field(wp_unslash($_POST['episode_duration']));
            update_post_meta($post_id, '_episode_duration', $duration);
        }
    }

    public static function admin_scripts($hook) {
        $screen = get_current_screen();
        if ($screen && in_array($screen->post_type, ['manga', 'episode'])) {
            wp_enqueue_style(
                'mangora-admin',
                MANGORA_PLUGIN_URL . 'assets/css/admin.css',
                [],
                MANGORA_VERSION
            );
        }
    }
}
