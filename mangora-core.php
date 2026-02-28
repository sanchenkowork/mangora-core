<?php
/**
 * Plugin Name: Mangora Core
 * Description: A lightweight WordPress plugin for manga streaming with episode management.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: mangora
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MANGORA_VERSION', '1.0.0');
define('MANGORA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MANGORA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'Mangora\\';
    $base_dir = MANGORA_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . str_replace('\\', '/', str_replace('_', '-', strtolower($relative_class))) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize
add_action('plugins_loaded', function () {
    Mangora\Post_Types::init();
    Mangora\Meta_Boxes::init();
    Mangora\Ajax::init();
    Mangora\Template::init();
});

// Activation hook
register_activation_hook(__FILE__, function () {
    Mangora\Post_Types::init();
    Mangora\Post_Types::seed_status_terms();
    flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});
