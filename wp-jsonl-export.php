<?php
/**
 * Plugin Name: WP JSONL Export
 * Description: Export any post type to a JSONL file, with the option to include metadata and change key names.
 * Version: 1.4
 * Author: Rohan Dhananjaya
 * Author URI: https://github.com/rohandhananjaya
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_JSONL_EXPORT_VERSION', '1.4');
define('WP_JSONL_EXPORT_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include necessary files
require_once WP_JSONL_EXPORT_PLUGIN_DIR . 'includes/class-wp-jsonl-export-admin.php';
require_once WP_JSONL_EXPORT_PLUGIN_DIR . 'includes/class-wp-jsonl-export-export.php';

// Initialize the plugin
function wp_jsonl_export_init() {
    new WP_JSONL_Export_Admin();
    new WP_JSONL_Export_Export();
}
add_action('plugins_loaded', 'wp_jsonl_export_init');
