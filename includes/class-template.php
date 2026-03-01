<?php
namespace Mangora;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Template {

    public static function init() {
        add_filter('template_include', [__CLASS__, 'template_loader']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'frontend_scripts']);
    }

    public static function template_loader($template) {
        if (is_post_type_archive('manga')) {
            $custom_template = MANGORA_PLUGIN_DIR . 'templates/archive-manga.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        if (is_singular('manga')) {
            $custom_template = MANGORA_PLUGIN_DIR . 'templates/single-manga.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        return $template;
    }

    public static function frontend_scripts() {
        if (is_post_type_archive('manga') || is_singular('manga')) {
            wp_enqueue_style(
                'mangora-frontend',
                MANGORA_PLUGIN_URL . 'assets/css/frontend.css',
                [],
                MANGORA_VERSION
            );

            wp_enqueue_script(
                'mangora-frontend',
                MANGORA_PLUGIN_URL . 'assets/js/frontend.js',
                ['jquery'],
                MANGORA_VERSION,
                true
            );

            wp_localize_script('mangora-frontend', 'mangoraData', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'filterNonce' => wp_create_nonce('mangora_filter'),
                'episodesNonce' => wp_create_nonce('mangora_episodes'),
            ]);
        }
    }
}
