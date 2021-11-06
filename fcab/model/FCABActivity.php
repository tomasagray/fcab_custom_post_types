<?php

namespace fcab\model;

use const fcab\DOMAIN;


class FCABActivity {
	public const POST_TYPE = 'fcab_cpt_activity';

	public static function create_post_type(): void {
		register_post_type( self::POST_TYPE,
			[
				'labels'            => [
					'name'                  => __( 'Activities', DOMAIN ),
					'singular_name'         => __( 'Activity', DOMAIN ),
					'menu_name'             => __( 'Activities', DOMAIN ),
					'add_new_item'          => __( 'Add new Activity', DOMAIN ),
					'edit_item'             => __( 'Edit Activity', DOMAIN ),
					'new_item'              => __( 'New Activity', DOMAIN ),
					'view_item'             => __( 'View Activity', DOMAIN ),
					'view_items'            => __( 'View Activity', DOMAIN ),
					'featured_image'        => __( 'Activity Image', DOMAIN ),
					'set_featured_image'    => __( 'Set Activity image', DOMAIN ),
					'remove_featured_image' => __( 'Remove Activity image', DOMAIN ),
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [ 'slug' => 'activity' ],
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_in_menu'      => true,
				'show_in_admin_bar' => true,
				'show_in_rest'      => true,
				'menu_icon'         => plugin_dir_url( __FILE__ ) . '../../img/activities_admin_icon.png',
				'can_export'        => true,
				'supports'          => [ 'title', 'thumbnail', 'editor' ],
			]
		);
	}
}

// Hooks
add_action( 'init', [ FCABActivity::class, 'create_post_type' ] );

// Disable 'block editor'
add_filter( 'use_block_editor_for_post_type', function ( $use_block_editor, $post_type ) {
	if ( in_array( $post_type, array( 'post', FCABActivity::POST_TYPE ), true ) ) {
		return false;
	}
	return $use_block_editor;
}, 10, 2 );
