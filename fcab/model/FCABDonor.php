<?php

namespace fcab\model;

use fcab\view\customfields\FCABDonationFields;
use JsonException;
use const fcab\DOMAIN;

const DONATIONS_PAGE_TITLE = 'Donate';

/**
 * Class FCABDonor
 * Represents a financial contributor to FCAB
 */
class FCABDonor {
	public const POST_TYPE = 'fcab_cpt_donor';
	public const DONATION_FIELD_NAME = 'fcab_cpt_donor_total_donations';
	public const FCAB_DONATION_AMOUNT = 'fcab_cpt_donation_amount';

	public static function create_post_type(): void {
		register_post_type( self::POST_TYPE,
			[
				'labels'            => [
					'name'                  => __( 'Donors', DOMAIN ),
					'singular_name'         => __( 'Donor', DOMAIN ),
					'menu_name'             => __( 'Donors', DOMAIN ),
					'add_new_item'          => __( 'Add new Donor', DOMAIN ),
					'edit_item'             => __( 'Edit Donor', DOMAIN ),
					'new_item'              => __( 'New Donor', DOMAIN ),
					'view_item'             => __( 'View Donor', DOMAIN ),
					'view_items'            => __( 'View Donors', DOMAIN ),
					'featured_image'        => __( 'Donor Image', DOMAIN ),
					'set_featured_image'    => __( 'Set donor image', DOMAIN ),
					'remove_featured_image' => __( 'Remove donor image', DOMAIN ),
				],
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => [ 'slug' => 'donor' ],
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_in_menu'      => true,
				'show_in_admin_bar' => true,
				'show_in_rest'      => true,
				'menu_icon'         => plugin_dir_url( __FILE__ ) . '../../img/donors_admin_icon.png',
				'can_export'        => true,
				'supports'          => [ 'title', 'thumbnail', 'customfields', 'page-attributes' ],
				'menu_position'     => 37,
			]
		);
	}

	public static function create_taxonomies(): void {
		register_taxonomy( self::FCAB_DONATION_AMOUNT, self::POST_TYPE, [
			'label'        => __( 'Total Donations', DOMAIN ),
			'rewrite'      => false, // ['slug', 'donations'],
			'hierarchical' => false,
			'public'       => true,
		] );
		register_taxonomy_for_object_type( self::FCAB_DONATION_AMOUNT, self::POST_TYPE );
	}

	public static function create_post_columns( $columns ): array {
		$columns['title'] = __( 'Name', DOMAIN );
		$new_columns      = array_merge( $columns, [ self::DONATION_FIELD_NAME => __( 'Total Donations', DOMAIN ) ] );
		$end_col          = $new_columns['date'];
		unset( $new_columns['date'] );
		$new_columns['date'] = $end_col;

		return $new_columns;
	}

	public static function make_columns_sortable( $columns ): array {
		$columns[ self::DONATION_FIELD_NAME ] = self::DONATION_FIELD_NAME;

		return $columns;
	}

	public static function create_post_column( $column, $post_id ): void {
		if ( $column === self::DONATION_FIELD_NAME ) {
			try {
				$field_name      = FCABDonationFields::PREFIX . FCABDonationFields::DONATION_FIELD;
				$metadata        = get_post_meta( $post_id, $field_name, true );
				$donations       = json_decode( $metadata, true, 512, JSON_THROW_ON_ERROR );
				$total_donations = FCABDonationFields::getTotalDonations( $donations );
				echo '<span>' . $total_donations . '</span>';
			} catch ( JsonException $e ) {
				echo '<span>0</span>';
			}
		}
	}

	/**
	 * Create Donations page
	 */
	public static function create_donations_page(): void {
		// Check if page already exists
		$page = get_page_by_title( DONATIONS_PAGE_TITLE );
		if ( $page !== null ) {
			return;
		}

		// Read default content
		$plugin_path  = dirname( __DIR__ ) . '/view/donors';
		$content_path = $plugin_path . '/default_content.php';
		$content = file_get_contents( $content_path ) or die( "Could not load default FCAB content!" );
		// Replace QR code refs
		$qr_img_url  = plugin_dir_url( __DIR__ ) . "view/donors/paypal_qr_code.png";
		$img_content = str_replace( "%QR_CODE%", $qr_img_url, $content );

		$donations_page = [
			'post_title'   => wp_strip_all_tags( DONATIONS_PAGE_TITLE ),
			'post_content' => $img_content,
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'page',
		];
		wp_insert_post( $donations_page );
	}
}

// Hooks
add_action( 'init', [ FCABDonor::class, 'create_post_type' ] );
add_filter( 'manage_fcab_cpt_donor_posts_columns', [ FCABDonor::class, 'create_post_columns' ] );
add_filter( 'manage_edit-fcab_cpt_donor_sortable_columns', [ FCABDonor::class, 'make_columns_sortable' ] );
add_action( 'manage_fcab_cpt_donor_posts_custom_column', [ FCABDonor::class, 'create_post_column' ], 10, 2 );
// Disable 'block editor'
add_filter( 'use_block_editor_for_post_type', function ( $use_block_editor, $post_type ) {
	if ( in_array( $post_type, array( 'post', FCABDonor::POST_TYPE ), true ) ) {
		return false;
	}

	return $use_block_editor;
}, 10, 2 );


