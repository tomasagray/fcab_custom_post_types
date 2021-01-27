<?php

namespace fcab\model;

use const fcab\DOMAIN;
use const fcab\DONATION_FIELD_NAME;

/**
 * Class FCABDonor
 * Represents a financial contributor to FCAB
 */
class FCABDonor
{
    public const POST_TYPE = 'fcab_cpt_donor';

    public static function create_post_type(): void
    {
        register_post_type('fcab_cpt_donor',
            [
                'labels' => [
                    'name' => __('Donors', DOMAIN),
                    'singular_name' => __('Donor', DOMAIN),
                    'menu_name' => __('Donors', DOMAIN),
                    'add_new_item' => __('Add new Donor', DOMAIN),
                    'edit_item' => __('Edit Donor', DOMAIN),
                    'featured_image' => __('Donor Image', DOMAIN),
                    'set_featured_image' => __('Set donor image', DOMAIN),
                    'remove_featured_image' => __('Remove donor image', DOMAIN),
                ],
                'public' => true,
                'has_archive' => true,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_menu' => true,
                'show_in_admin_bar' => true,
                'show_in_rest'  => true,
                'menu_icon' => plugin_dir_url(__FILE__) . '../../img/donors_admin_icon.png',
                'can_export' => true,
                'supports' => ['title', 'thumbnail', 'editor'],
            ]
        );
    }

    public static function create_taxonomies(): void
    {
        register_taxonomy('fcab_cpt_donation_amount', self::POST_TYPE, [
            'label' => __('Total Donations', DOMAIN),
            'rewrite' => false, // ['slug', 'donations'],
            'hierarchical' => false,
            'public'    =>  true,
        ]);
    }

    public static function create_post_columns($columns): array
    {
        $columns['title'] = __('Name', DOMAIN);
        $new_columns = array_merge($columns, [DONATION_FIELD_NAME => __('Total Donations', DOMAIN)]);
        $end_col = $new_columns['date'];
        unset($new_columns['date']);
        $new_columns['date'] = $end_col;
        return $new_columns;
    }

    public static function make_columns_sortable($columns): array
    {
        $columns[DONATION_FIELD_NAME] = DONATION_FIELD_NAME;
        return $columns;
    }


    public static function create_post_column($column, $post_id): void
    {
        if ($column === DONATION_FIELD_NAME) {
            $donations = get_post_meta($post_id, DONATION_FIELD_NAME, true);
            echo '<span>'.$donations.'</span>';
        }
    }
}

// Hooks
add_action('init', [FCABDonor::class, 'create_post_type']);
add_filter('manage_fcab_cpt_donor_posts_columns', [FCABDonor::class, 'create_post_columns']);
add_filter('manage_edit-fcab_cpt_donor_sortable_columns', [FCABDonor::class, 'make_columns_sortable']);
add_action('manage_fcab_cpt_donor_posts_custom_column', [FCABDonor::class, 'create_post_column'], 10, 2);

