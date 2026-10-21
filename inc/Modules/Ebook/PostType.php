<?php
/**
 * Post Type: E-book
 *
 * @package Jubelio
 */

namespace Jubelio\Modules\Ebook;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PostType {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	public static function register() {
		if ( ! post_type_exists( 'ebook' ) ) {
			register_post_type(
				'ebook',
				array(
					'labels'          => array(
						'name'               => _x( 'E-book', 'post type general name', 'hello-elementor-child' ),
						'singular_name'      => _x( 'E-book', 'post type singular name', 'hello-elementor-child' ),
						'add_new'            => _x( 'Add New', 'ebooks', 'hello-elementor-child' ),
						'add_new_item'       => __( 'Add New E-book', 'hello-elementor-child' ),
						'edit_item'          => __( 'Edit E-book', 'hello-elementor-child' ),
						'new_item'           => __( 'New E-book', 'hello-elementor-child' ),
						'view_item'          => __( 'View E-book', 'hello-elementor-child' ),
						'search_items'       => __( 'Search E-books', 'hello-elementor-child' ),
						'not_found'          => __( 'No E-books found', 'hello-elementor-child' ),
						'not_found_in_trash' => __( 'No E-books found in trash', 'hello-elementor-child' ),
					),
					'public'          => true,
					'show_ui'         => true,
					'query_var'       => true,
					'menu_icon'       => 'dashicons-media-document',
					'hierarchical'    => false,
					'show_in_nav_menus' => true,
					'has_archive'     => true,
					'rewrite'         => array(
						'slug'       => 'ebook',
						'with_front' => false,
					),
					'capability_type' => 'post',
					'supports'        => array( 'title', 'thumbnail', 'excerpt', 'editor' ),
				)
			);
		}
	}
}
