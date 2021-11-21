<?php

namespace fcab\model;

use WP_Post;
use WP_Term;
use const fcab\DOMAIN;


class FCABProgram {
	public const POST_TYPE = 'fcab_cpt_program';
	public const TAGS = 'fcab_program_tag';

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
				'supports'          => [ 'title', 'thumbnail', 'editor', 'page-attributes' ],
				'menu_position'     => 35,
			]
		);
	}


	public static function create_tag_taxonomies(): void {

		$post_types = [
			FCABProject::POST_TYPE,
			FCABActivity::POST_TYPE,
		];

		register_taxonomy( self::TAGS, $post_types, array(
			'hierarchical'          => true,
			'show_ui'               => true,
			'show_in_menu'          => false,
			'show_admin_column'     => true,
			'show_in_rest'          => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => self::TAGS ),
			'sort'                  => true,
		) );

		// register tags for custom post types
		foreach ( $post_types as $post_type ) {
			register_taxonomy_for_object_type( self::TAGS, $post_type );
		}
	}

	public static function handle_status_change( $new_status, $old_status, $program ): void {

		if ( $program !== null && $program->post_type === self::POST_TYPE ) {
			switch ( $new_status ) {
				case 'publish':
					// if term already exists, update
					$term = self::get_program_term( $program );
					if ( $term !== null ) {
						self::update_term( $term, $program );
					} else {
						self::add_term_on_new_program( $program );
					}
					break;

				case 'trash':
					self::remove_term_on_delete_program( $program );
					break;
			}
		}
	}

	public static function add_term_on_new_program( $post ): void {
		if ( $post === null ) {
			return;
		}
		wp_insert_term(
			$post->post_title,
			self::TAGS,
			[
				'description' => $post->post_content,
				'slug'        => $post->ID
			]
		);
	}

	public static function update_term( ?WP_Term $term, WP_Post $post ): void {
		if ( $term !== null ) {
			wp_update_term( $term->term_id, self::TAGS, [
				'description' => $post->post_content,
				'slug'        => $post->ID,
				'name'        => $post->post_title
			] );
		}
	}

	public static function remove_term_on_delete_program( $post ): void {
		if ( $post === null ) {
			return;
		}
		// get term id
		$term = self::get_program_term( $post );
		if ( $term !== null ) {
			wp_delete_term( $term->term_id, self::TAGS );
		}
	}

	public static function get_program_term( $program ): ?WP_Term {
		if ( $program === null ) {
			return null;
		}

		$term = get_term_by( 'slug', $program->ID, self::TAGS );
		if ( $term !== false ) {
			return $term;
		}

		return null;
	}
}

// Hooks
add_action( 'init', [ FCABProgram::class, 'create_post_type' ] );
add_action( 'init', [ FCABProgram::class, 'create_tag_taxonomies' ] );
add_action( 'transition_post_status', [ FCABProgram::class, 'handle_status_change' ], 10, 3 );
add_action( 'delete_post', [ FCABProgram::class, 'remove_term_on_delete_program' ] );

// Disable 'block editor'
add_filter( 'use_block_editor_for_post_type', function ( $use_block_editor, $post_type ) {
	if ( in_array( $post_type, array( 'post', FCABProgram::POST_TYPE ), true ) ) {
		return false;
	}

	return $use_block_editor;
}, 10, 2 );
