<?php
namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminCleanup {

	public static function init() {
		add_filter( 'comments_open', '__return_false', 20 );
		add_filter( 'pings_open', '__return_false', 20 );
		add_filter( 'comments_array', '__return_empty_array', 10 );
		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_action( 'init', array( __CLASS__, 'disable_xmlrpc' ) );
		add_filter( 'option_blogname', array( __CLASS__, 'fallback_blogname' ) );
		add_action( 'wp_head', array( __CLASS__, 'bing_verification_meta' ) );

		if ( is_admin() ) {
			add_action( 'init', array( __CLASS__, 'register_developer_role' ) );
			add_action( 'admin_init', array( __CLASS__, 'cleanup_comments_admin' ) );
			add_action( 'admin_menu', array( __CLASS__, 'remove_admin_menus' ) );
			add_filter( 'mce_buttons_2', array( __CLASS__, 'tinymce_buttons' ) );
			add_filter( 'tiny_mce_before_init', array( __CLASS__, 'tinymce_formats' ) );
		}

		if ( is_admin_bar_showing() ) {
			add_action( 'init', array( __CLASS__, 'remove_admin_bar_comments' ) );
		}
	}

	public static function register_developer_role() {
		if ( ! get_role( 'developer' ) ) {
			add_role(
				'developer',
				__( 'Developer', 'hello-elementor-child' ),
				array(
					'read'                   => true,
					'delete_posts'           => true,
					'delete_published_posts' => true,
					'edit_posts'             => true,
					'publish_posts'          => true,
					'edit_published_posts'   => true,
					'upload_files'           => true,
					'moderate_comments'      => true,
				)
			);
		}
	}

	public static function cleanup_comments_admin() {
		global $pagenow;
		if ( $pagenow === 'edit-comments.php' ) {
			wp_safe_redirect( admin_url() );
			exit;
		}

		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

		foreach ( get_post_types() as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	}

	public static function remove_admin_menus() {
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'edit.php?post_type=ae_global_templates' );

		$current_user = wp_get_current_user();
		$role         = (array) $current_user->roles;

		if ( in_array( 'administrator', $role, true ) ) {
			remove_menu_page( 'wp-mail-smtp' );
			remove_menu_page( 'menu-image-options' );
			remove_menu_page( 'smush' );
			remove_menu_page( 'mlang' );
		}
	}

	public static function remove_admin_bar_comments() {
		remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
	}

	public static function tinymce_buttons( $buttons ) {
		array_unshift( $buttons, 'fontsizeselect' );
		return $buttons;
	}

	public static function tinymce_formats( $initArray ) {
		$initArray['fontsize_formats'] = '8pt 10pt 12pt 14pt 18pt 24pt 36pt';
		return $initArray;
	}

	public static function disable_xmlrpc() {
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			wp_die( 'XML-RPC is disabled on this site.', 'Forbidden', array( 'response' => 403 ) );
		}
	}

	public static function fallback_blogname( $value ) {
		return $value ?: 'Jubelio';
	}

	public static function bing_verification_meta() {
		echo '<meta name="msvalidate.01" content="7C405658CB7F677DCA91C40FB791C06A" />' . "\n";
	}
}
