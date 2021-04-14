<?php


namespace fcab\model;


use const fcab\DOMAIN;

class FCABProject
{
    public const POST_TYPE = 'fcab_cpt_project';
    public const TAGS = 'fcab_project_tag';
    public const OTHER_TAG = 'Other';

    public static function create_post_type(): void
    {
        register_post_type(self::POST_TYPE,
            [
                'labels' => [
                    'name' => __('Projects', DOMAIN),
                    'singular_name' => __('Project', DOMAIN),
                    'menu_name' => __('Projects', DOMAIN),
                    'add_new_item' => __('Add new Project', DOMAIN),
                    'edit_item' => __('Edit Project', DOMAIN),
                    'new_item' => __('New Project', DOMAIN),
                    'view_item' => __('View Project', DOMAIN),
                    'view_items' => __('View Project', DOMAIN),
                    'featured_image' => __('Project Image', DOMAIN),
                    'set_featured_image' => __('Set Project image', DOMAIN),
                    'remove_featured_image' => __('Remove Project image', DOMAIN),
                ],
                'public' => true,
                'has_archive' => true,
                'rewrite' => ['slug' => 'projects'],
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_menu' => true,
                'show_in_admin_bar' => true,
                'show_in_rest' => true,
                'menu_icon' => plugin_dir_url(__FILE__) . '../../img/projects_admin_icon.png',
                'can_export' => true,
                'supports' => ['title', 'thumbnail', 'editor'],
            ]
        );
    }

    public static function create_tag_taxonomies(): void
    {
        // Add new taxonomy, NOT hierarchical (like tags)
        $labels = array(
            'name' => _x('Tags', 'taxonomy general name'),
            'singular_name' => _x('Tag', 'taxonomy singular name'),
            'search_items' => __('Search Tags'),
            'popular_items' => __('Popular Tags'),
            'all_items' => __('All Tags'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __('Edit Tag'),
            'update_item' => __('Update Tag'),
            'add_new_item' => __('Add New Tag'),
            'new_item_name' => __('New Tag Name'),
            'separate_items_with_commas' => __('Separate tags with commas'),
            'add_or_remove_items' => __('Add or remove tags'),
            'choose_from_most_used' => __('Choose from the most used tags'),
            'menu_name' => __('Tags'),
        );

        register_taxonomy(self::TAGS, self::POST_TYPE, array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array('slug' => self::TAGS),
            'show_admin_column' => true,
            'sort' => true,
        ));
        register_taxonomy_for_object_type(self::TAGS, self::POST_TYPE);
        // Add 'other' category
        wp_insert_term(self::OTHER_TAG, self::TAGS,
            array('description' => 'Projects which do not fit into other categories.'));
    }
}

// Hooks
add_action('init', [FCABProject::class, 'create_post_type']);
add_action('init', [FCABProject::class, 'create_tag_taxonomies'], 0);

// Disable 'block editor'
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
    if (in_array($post_type, array('post', FCABProject::POST_TYPE), true)) {
        return false;
    }
    return $use_block_editor;
}, 10, 2);
