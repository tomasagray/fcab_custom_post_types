<?php


namespace fcab\model;


use const fcab\DOMAIN;

class FCABVolunteer
{
    public const POST_TYPE = 'fcab_cpt_volunteer';

    public static function create_post_type(): void
    {
        register_post_type(self::POST_TYPE,
            [
                'labels' => [
                    'name' => __('Volunteers', DOMAIN),
                    'singular_name' => __('Volunteer', DOMAIN),
                    'menu_name' => __('Volunteers', DOMAIN),
                    'add_new_item' => __('Add new Volunteer', DOMAIN),
                    'edit_item' => __('Edit Volunteer', DOMAIN),
                    'new_item' => __('New Volunteer', DOMAIN),
                    'view_item' => __('View Volunteer', DOMAIN),
                    'view_items' => __('View Volunteers', DOMAIN),
                    'featured_image' => __('Volunteer Image', DOMAIN),
                    'set_featured_image' => __('Set Volunteer image', DOMAIN),
                    'remove_featured_image' => __('Remove Volunteer image', DOMAIN),
                ],
                'public' => true,
                'has_archive' => true,
                'rewrite' => ['slug' => 'volunteer'],
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_menu' => true,
                'show_in_admin_bar' => true,
                'show_in_rest' => true,
                'menu_icon' => plugin_dir_url(__FILE__) . '../../img/volunteers_admin_icon.png',
                'can_export' => true,
                'supports' => ['title', 'thumbnail', 'editor'],
            ]
        );
    }
}

add_action('init', [FCABVolunteer::class, 'create_post_type']);
// Disable 'block editor'
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
    if (in_array($post_type, array('post', FCABVolunteer::POST_TYPE), true)) {
        return false;
    }
    return $use_block_editor;
}, 10, 2);

