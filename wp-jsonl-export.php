<?php
/**
 * Plugin Name: WP JSONL Export
 * Description: Export any post type to a JSONL file, with the option to include metadata and change key names.
 * Version: 1.3
 * Author: Rohan Dhananjaya
 * Author URI: https://github.com/rohandhananjaya
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WP_JSONL_Export_Plugin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_export_jsonl', array($this, 'export_jsonl'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    // Add menu page
    public function add_admin_menu() {
        add_management_page(
            'WP JSONL Export', 
            'WP JSONL Export', 
            'manage_options', 
            'wp-jsonl-export', 
            array($this, 'create_admin_page')
        );
    }

    // Enqueue JavaScript
    public function enqueue_scripts() {
        wp_enqueue_script('wp-jsonl-export', plugin_dir_url(__FILE__) . 'wp-jsonl-export.js', array('jquery'), '1.0', true);
    }

    // Create the admin page
    public function create_admin_page() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $metadata_fields = $this->get_all_post_type_metadata($post_types); // Get all metadata fields

        ?>
        <div class="wrap">
            <h1>WP JSONL Export</h1>
            <form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="export_jsonl">
                
                <table class="form-table">
                    <tr>
                        <th>Select Post Type:</th>
                        <td>
                            <select name="post_type" id="post_type">
                                <option value="">Select a post type</option>
                                <?php
                                foreach ($post_types as $post_type) {
                                    echo "<option value='{$post_type->name}'>{$post_type->label}</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Include Post Data (Edit Keys):</th>
                        <td>
                            <label>Title Key: <input type="text" name="custom_title_key" value="title"></label><br>
                            <label>Content Key: <input type="text" name="custom_content_key" value="content"></label><br>
                            <label>Excerpt Key: <input type="text" name="custom_excerpt_key" value="excerpt"></label><br>
                            <label>Date Key: <input type="text" name="custom_date_key" value="date"></label><br>
                            <label>Author Key: <input type="text" name="custom_author_key" value="author"></label><br>
                        </td>
                    </tr>
                    <tr>
                        <th>Include Metadata:</th>
                        <td>
                            <label><input type="checkbox" name="include_metadata" id="include_metadata"> Include Specific Metadata Fields</label>
                            <div id="meta_fields_container" style="display: none;">
                                <?php foreach ($metadata_fields as $post_type => $meta_keys) : ?>
                                    <div class="meta-fields" data-post-type="<?php echo $post_type; ?>" style="display: none;">
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

                <?php submit_button('Export to JSONL'); ?>
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

    // Export function
    public function export_jsonl() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
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

        $include_metadata = isset($_POST['include_metadata']);
        $selected_meta_fields = isset($_POST['meta_fields']) ? $_POST['meta_fields'] : array();
        $custom_meta_keys = isset($_POST['custom_meta_keys']) ? $_POST['custom_meta_keys'] : array();

        // Generate the JSONL file
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="export-' . $post_type . '-' . date('Y-m-d') . '.jsonl"');

        foreach ($posts as $post) {
            $post_data = array();

            // Include selected fields using custom key names
            if (!empty($custom_keys['title'])) {
                $post_data[$custom_keys['title']] = $post->post_title;
            }
            if (!empty($custom_keys['content'])) {
                $post_data[$custom_keys['content']] = $post->post_content;
            }
            if (!empty($custom_keys['excerpt'])) {
                $post_data[$custom_keys['excerpt']] = $post->post_excerpt;
            }
            if (!empty($custom_keys['date'])) {
                $post_data[$custom_keys['date']] = $post->post_date;
            }
            if (!empty($custom_keys['author'])) {
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

// Initialize the plugin
new WP_JSONL_Export_Plugin();