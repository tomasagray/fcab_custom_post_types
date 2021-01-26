<?php
/**
 * Plugin Name: FCAB Post Types
 * Description: Custom post types for the FCAB WordPress website
 * Version: 0.1
 * Author: Tomás Gray
 **/

/**
 * Create custom post type: Donors
 */
add_action('init', 'fcab_create_donor_post_type');
function fcab_create_donor_post_type()
{
    register_post_type('fcab_cpt_donor',
        [
            'labels' => [
                'name' => __('Donors', 'fcab_wp'),
                'singular_name' => __('Donor', 'fcab_wp'),
                'menu_name' => __('Donors', 'fcab_wp'),
                'add_new_item' => __('Add new Donor', 'fcab_wp'),
                'edit_item' => __('Edit Donor', 'fcab_wp'),
                'featured_image' => __('Donor Image', 'fcab_wp'),
                'set_featured_image' => __('Set donor image', 'fcab_wp'),
                'remove_featured_image' => __('Remove donor image', 'fcab_wp'),
            ],
            'public' => true,
            'has_archive' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_icon' => plugin_dir_url(__FILE__) . '/img/donors_admin_icon.png',
            'can_export' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
        ]
    );
}

abstract class FCABDonorMetaBox
{
    public static function add(): void
    {
        add_meta_box(
            'fcab_cpt_meta_boxes',
            __('Donor Information', 'fcab_wp'),
            [self::class, 'html'],
            'fcab_cpt_donor',
            'side',
            'low'
        );
    }

    public static function save(int $post_id): void
    {
        $field_name = 'fcab_cpt_donor_total_donations';
        if (array_key_exists($field_name, $_POST)) {
            update_post_meta($post_id, '_fcab_cpt_donor_donations', $_POST[$field_name]);
        }
    }

    public static function html($post): void
    {
        // State verification
        wp_nonce_field(plugin_basename(__FILE__), 'fcab_cpt_meta_nonce');
        $total_donations = get_post_meta($post->ID, 'fcab_cpt_donor_total_donations', true);
        ?>
        <!-- FCAB Donor custom data fields -->
        <div>
            <label for="fcab_cpt_total_donations">Total Donations</label>
            <input type="number" min="1" step="1" name="fcab_cpt_donor_total_donations"
                 id="fcab_cpt_total_donations"
                 value="<?php echo $total_donations ?>"/>
        </div>
        <?php
    }
}

add_action('add_meta_boxes', ['FCABDonorMetaBox', 'add']);
add_action('save_post', ['FCABDonorMetaBox', 'save']);
