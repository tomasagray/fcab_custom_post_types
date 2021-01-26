<?php
/**
 * Plugin Name: FCAB Post Types
 * Description: Custom post types for the FCAB WordPress website
 * Version: 0.1
 * Author: Tomás Gray
 **/

add_action('init', 'create_fcab_custom_post_types');
function create_fcab_custom_post_types() {

    register_post_type('fcab_donor',
        [
            'labels'    => [
                'name'          => __('Donors', 'fcab_wp'),
                'singular_name' => __('Donor', 'fcab_wp'),
                'menu_name'     => __('Donors', 'fcab_wp'),
                'add_new_item'  => __('Add new donor', 'fcab_wp'),
            ],
            'public'            => true,
            'has_archive'       => true,
            'show_ui'           => true,
            'show_in_nav_menus' => true,
            'show_in_menu'      => true,
            'show_in_admin_bar' => true,
            'menu_icon'         => plugin_dir_url(__FILE__).'/img/admin_icon.png',
            'can_export'        => true,
            'supports'          => ['title', 'editor', 'custom-fields'],
        ]
    );
}
