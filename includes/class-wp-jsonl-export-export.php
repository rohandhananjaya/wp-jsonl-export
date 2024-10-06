<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class WP_JSONL_Export_Export {

    public function __construct() {
        add_action('admin_post_export_jsonl', array($this, 'export_jsonl'));
    }

    // Export function
    public function export_jsonl() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized user', 'wp-jsonl-export'));
        }

        $post_type = sanitize_text_field($_POST['post_type']);
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $posts = get_posts($args);

        // Gather custom field names
        $custom_keys = array(
            'title'   => sanitize_text_field($_POST['custom_title_key']),
            'content' => sanitize_text_field($_POST['custom_content_key']),
            'excerpt' => sanitize_text_field($_POST['custom_excerpt_key']),
            'date'    => sanitize_text_field($_POST['custom_date_key']),
            'author'  => sanitize_text_field($_POST['custom_author_key']),
        );

        // Check which fields are included
        $include_title   = isset($_POST['include_title']);
        $include_content = isset($_POST['include_content']);
        $include_excerpt = isset($_POST['include_excerpt']);
        $include_date    = isset($_POST['include_date']);
        $include_author  = isset($_POST['include_author']);

        $include_metadata = isset($_POST['include_metadata']);
        $selected_meta_fields = isset($_POST['meta_fields']) ? $_POST['meta_fields'] : array();
        $custom_meta_keys = isset($_POST['custom_meta_keys']) ? $_POST['custom_meta_keys'] : array();

        // Generate the JSONL file
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="export-' . $post_type . '-' . date('Y-m-d') . '.jsonl"');

        foreach ($posts as $post) {
            $post_data = array();

            // Include selected fields using custom key names
            if ($include_title && !empty($custom_keys['title'])) {
                $post_data[$custom_keys['title']] = $post->post_title;
            }
            if ($include_content && !empty($custom_keys['content'])) {
                $post_data[$custom_keys['content']] = $post->post_content;
            }
            if ($include_excerpt && !empty($custom_keys['excerpt'])) {
                $post_data[$custom_keys['excerpt']] = $post->post_excerpt;
            }
            if ($include_date && !empty($custom_keys['date'])) {
                $post_data[$custom_keys['date']] = $post->post_date;
            }
            if ($include_author && !empty($custom_keys['author'])) {
                $post_data[$custom_keys['author']] = get_the_author_meta('display_name', $post->post_author);
            }

            // Include selected metadata fields using custom key names
            if ($include_metadata) {
                $metadata = array();
                foreach ($selected_meta_fields as $meta_key) {
                    $meta_value = get_post_meta($post->ID, $meta_key, true);
                    $custom_meta_key = isset($custom_meta_keys[$meta_key]) ? $custom_meta_keys[$meta_key] : $meta_key;
                    $metadata[$custom_meta_key] = $meta_value;
                }
                $post_data['metadata'] = $metadata;
            }

            echo json_encode($post_data) . "\n";
        }

        exit;
    }
}
