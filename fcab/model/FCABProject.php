<?php


namespace fcab\model;


use const fcab\DOMAIN;

class FCABProject {
	public const POST_TYPE = 'fcab_cpt_project';

	public static function create_post_type(): void {
		register_post_type( self::POST_TYPE,
			[
				'labels'            => [
					'name'                  => __( 'Projects', DOMAIN ),
					'singular_name'         => __( 'Project', DOMAIN ),
					'menu_name'             => __( 'Projects', DOMAIN ),
					'add_new_item'          => __( 'Add new Project', DOMAIN ),
					'edit_item'             => __( 'Edit Project', DOMAIN ),
					'new_item'              => __( 'New Project', DOMAIN ),
					'view_item'             => __( 'View Project', DOMAIN ),
					'view_items'            => __( 'View Project', DOMAIN ),
					'featured_image'        => __( 'Project Image', DOMAIN ),
					'set_featured_image'    => __( 'Set Project image', DOMAIN ),
					'remove_featured_image' => __( 'Remove Project image', DOMAIN ),
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [ 'slug' => 'project' ],
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_in_menu'      => true,
				'show_in_admin_bar' => true,
				'show_in_rest'      => true,
				'menu_icon'         => plugin_dir_url( __FILE__ ) . '../../img/projects_admin_icon.png',
				'can_export'        => true,
				'supports'          => [
					'title',
					'thumbnail',
					'editor',
					'page-attributes',
					'revisions',
					'custom-fields',
				],
				'menu_position'     => 36,
			]
		);
	}
}

// Hooks
add_action( 'init', [ FCABProject::class, 'create_post_type' ] );

// Disable 'block editor'
add_filter( 'use_block_editor_for_post_type', function ( $use_block_editor, $post_type ) {
	if ( in_array( $post_type, array( 'post', FCABProject::POST_TYPE ), true ) ) {
		return false;
	}

	return $use_block_editor;
}, 10, 2 );
