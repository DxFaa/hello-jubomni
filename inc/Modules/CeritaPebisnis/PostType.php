<?php
/**
 * Post Type and Taxonomy Registration: Cerita Pebisnis
 *
 * @package Jubelio
 */

namespace Jubelio\Modules\CeritaPebisnis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PostType {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ), 5 );
	}

	public static function register() {
		// Register 'industri' taxonomy if not already registered by ACF Pro
		if ( ! taxonomy_exists( 'industri' ) ) {
			register_taxonomy(
				'industri',
				array( 'cerita-pebisnis' ),
				array(
					'labels'            => array(
						'name'          => __( 'Industri', 'hello-elementor-child' ),
						'singular_name' => __( 'Industri', 'hello-elementor-child' ),
						'search_items'  => __( 'Cari Industri', 'hello-elementor-child' ),
						'all_items'     => __( 'Semua Industri', 'hello-elementor-child' ),
						'edit_item'     => __( 'Edit Industri', 'hello-elementor-child' ),
						'update_item'   => __( 'Update Industri', 'hello-elementor-child' ),
						'add_new_item'  => __( 'Tambah Industri Baru', 'hello-elementor-child' ),
						'new_item_name' => __( 'Nama Industri Baru', 'hello-elementor-child' ),
						'menu_name'     => __( 'Industri', 'hello-elementor-child' ),
					),
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => true,
					'rewrite'           => array( 'slug' => 'industri' ),
				)
			);
		}

		// Register 'cerita-pebisnis' post type if not already registered by ACF Pro
		if ( ! post_type_exists( 'cerita-pebisnis' ) ) {
			register_post_type(
				'cerita-pebisnis',
				array(
					'labels'             => array(
						'name'               => _x( 'Cerita Pebisnis', 'post type general name', 'hello-elementor-child' ),
						'singular_name'      => _x( 'Cerita Pebisnis', 'post type singular name', 'hello-elementor-child' ),
						'add_new'            => _x( 'Tambah Baru', 'cerita-pebisnis', 'hello-elementor-child' ),
						'add_new_item'       => __( 'Tambah Cerita Pebisnis Baru', 'hello-elementor-child' ),
						'edit_item'          => __( 'Edit Cerita Pebisnis', 'hello-elementor-child' ),
						'new_item'           => __( 'Cerita Pebisnis Baru', 'hello-elementor-child' ),
						'view_item'          => __( 'Lihat Cerita Pebisnis', 'hello-elementor-child' ),
						'search_items'       => __( 'Cari Cerita Pebisnis', 'hello-elementor-child' ),
						'not_found'          => __( 'Tidak ada Cerita Pebisnis ditemukan', 'hello-elementor-child' ),
						'not_found_in_trash' => __( 'Tidak ada Cerita Pebisnis di Kotak Sampah', 'hello-elementor-child' ),
						'menu_name'          => __( 'Cerita Pebisnis', 'hello-elementor-child' ),
					),
					'public'             => true,
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'query_var'          => true,
					'rewrite'            => array(
						'slug'       => 'cerita-pebisnis',
						'with_front' => false,
					),
					'capability_type'    => 'post',
					'has_archive'        => true,
					'hierarchical'       => false,
					'menu_position'      => 20,
					'menu_icon'          => 'dashicons-format-chat',
					'show_in_rest'       => true,
					'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
					'taxonomies'         => array( 'industri' ),
				)
			);
		}
	}
}
