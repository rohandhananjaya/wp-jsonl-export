<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class WP_JSONL_Export_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    // Add menu page
    public function add_admin_menu() {
        add_management_page(
            __('WP JSONL Export', 'wp-jsonl-export'),
            __('WP JSONL Export', 'wp-jsonl-export'),
            'manage_options',
            'wp-jsonl-export',
            array($this, 'create_admin_page')
        );
    }

    // Enqueue JavaScript and CSS if needed
    public function enqueue_scripts($hook_suffix) {
        if ($hook_suffix === 'tools_page_wp-jsonl-export') {
            wp_enqueue_script('wp-jsonl-export-js', plugin_dir_url(__FILE__) . '../assets/js/wp-jsonl-export.js', array('jquery'), WP_JSONL_EXPORT_VERSION, true);
        }
    }

    // Create the admin page
    public function create_admin_page() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $metadata_fields = $this->get_all_post_type_metadata($post_types); // Get all metadata fields

        ?>
        <div class="wrap">
            <h1><?php esc_html_e('WP JSONL Export', 'wp-jsonl-export'); ?></h1>
            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="export_jsonl">
                
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e('Select Post Type:', 'wp-jsonl-export'); ?></th>
                        <td>
                            <select name="post_type" id="post_type">
                                <option value=""><?php esc_html_e('Select a post type', 'wp-jsonl-export'); ?></option>
                                <?php
                                foreach ($post_types as $post_type) {
                                    echo "<option value='{$post_type->name}'>{$post_type->label}</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Include Post Data (Edit Keys):', 'wp-jsonl-export'); ?></th>
                        <td>
                            <label><input type="checkbox" name="include_title" checked> <?php esc_html_e('Title Key:', 'wp-jsonl-export'); ?> <input type="text" name="custom_title_key" value="title"></label><br>
                            <label><input type="checkbox" name="include_content" checked> <?php esc_html_e('Content Key:', 'wp-jsonl-export'); ?> <input type="text" name="custom_content_key" value="content"></label><br>
                            <label><input type="checkbox" name="include_excerpt"> <?php esc_html_e('Excerpt Key:', 'wp-jsonl-export'); ?> <input type="text" name="custom_excerpt_key" value="excerpt"></label><br>
                            <label><input type="checkbox" name="include_date"> <?php esc_html_e('Date Key:', 'wp-jsonl-export'); ?> <input type="text" name="custom_date_key" value="date"></label><br>
                            <label><input type="checkbox" name="include_author"> <?php esc_html_e('Author Key:', 'wp-jsonl-export'); ?> <input type="text" name="custom_author_key" value="author"></label><br>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Include Metadata:', 'wp-jsonl-export'); ?></th>
                        <td>
                            <label><input type="checkbox" name="include_metadata" id="include_metadata"> <?php esc_html_e('Include Specific Metadata Fields', 'wp-jsonl-export'); ?></label>
                            <div id="meta_fields_container" style="display: none;">
                                <?php foreach ($metadata_fields as $post_type => $meta_keys) : ?>
                                    <div class="meta-fields" data-post-type="<?php echo esc_attr($post_type); ?>" style="display: none;">
                                        <?php foreach ($meta_keys as $meta_key) : ?>
                                            <label>
                                                <input type="checkbox" name="meta_fields[]" value="<?php echo esc_attr($meta_key); ?>"> 
                                                <?php echo esc_html($meta_key); ?> Key: 
                                                <input type="text" name="custom_meta_keys[<?php echo esc_attr($meta_key); ?>]" value="<?php echo esc_attr($meta_key); ?>">
                                            </label><br>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Export to JSONL', 'wp-jsonl-export')); ?>
            </form>
        </div>
        <?php
    }

    // Fetch metadata fields for all public post types
    public function get_all_post_type_metadata($post_types) {
        $metadata_fields = array();

        foreach ($post_types as $post_type) {
            $args = array(
                'post_type' => $post_type->name,
                'posts_per_page' => 1, // Only need 1 post to get its metadata
                'post_status' => 'publish',
            );

            $posts = get_posts($args);
            if ($posts) {
                $metadata_fields[$post_type->name] = array_keys(get_post_meta($posts[0]->ID));
            } else {
                $metadata_fields[$post_type->name] = array();
            }
        }

        return $metadata_fields;
    }
}