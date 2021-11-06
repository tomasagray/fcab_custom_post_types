<?php

namespace fcab\model;

use const fcab\DOMAIN;


class FCABProgram {
	public const POST_TYPE = 'fcab_cpt_program';

	public static function create_post_type(): void {
		register_post_type( self::POST_TYPE,
			[
				'labels'            => [
					'name'                  => __( 'Programs', DOMAIN ),
					'singular_name'         => __( 'Program', DOMAIN ),
					'menu_name'             => __( 'Programs', DOMAIN ),
					'add_new_item'          => __( 'Add new Program', DOMAIN ),
					'edit_item'             => __( 'Edit Program', DOMAIN ),
					'new_item'              => __( 'New Program', DOMAIN ),
					'view_item'             => __( 'View Program', DOMAIN ),
					'view_items'            => __( 'View Program', DOMAIN ),
					'featured_image'        => __( 'Program Image', DOMAIN ),
					'set_featured_image'    => __( 'Set Program image', DOMAIN ),
					'remove_featured_image' => __( 'Remove Program image', DOMAIN ),
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [ 'slug' => 'program' ],
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_in_menu'      => true,
				'show_in_admin_bar' => true,
				'show_in_rest'      => true,
				'menu_icon'         => plugin_dir_url( __FILE__ ) . '../../img/programs_admin_icon.png',
				'can_export'        => true,
				'supports'          => [ 'title', 'thumbnail', 'editor' ],
			]
		);
	}
}

// Hooks
add_action( 'init', [ FCABProgram::class, 'create_post_type' ] );

// Disable 'block editor'
add_filter( 'use_block_editor_for_post_type', function ( $use_block_editor, $post_type ) {
	if ( in_array( $post_type, array( 'post', FCABProgram::POST_TYPE ), true ) ) {
		return false;
	}
	return $use_block_editor;
}, 10, 2 );
