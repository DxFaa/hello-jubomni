<?php
/**
 * Admin Clean-up, Roles, Comments & Security Tweaks
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Superadministration Role: Developer
 */
add_action( 'init', function() {
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
} );

/**
 * Admin Init: comments redirect, metabox removal, disable comment support
 */
add_action( 'admin_init', function () {
	global $pagenow;

	// Redirect any user trying to access comments page
	if ( $pagenow === 'edit-comments.php' ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	// Remove comments metabox from dashboard
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

	// Disable support for comments and trackbacks in post types
	foreach ( get_post_types() as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
} );

// Close comments on the front-end
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

// Hide existing comments
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// Remove comments & unused menus in admin
add_action( 'admin_menu', function () {
	remove_menu_page( 'edit-comments.php' );
	remove_menu_page( 'edit.php?post_type=ae_global_templates' );

	$current_user = wp_get_current_user();
	$role         = (array) $current_user->roles;

	// If the user is an administrator, remove some clutter menus
	if ( in_array( 'administrator', $role, true ) ) {
		remove_menu_page( 'wp-mail-smtp' );
		remove_menu_page( 'menu-image-options' );
		remove_menu_page( 'smush' );
		remove_menu_page( 'mlang' );
	}
} );

// Remove comments links from admin bar
add_action( 'init', function () {
	if ( is_admin_bar_showing() ) {
		remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
	}
} );

// TinyMCE font size options
function my_mce_buttons_2( $buttons ) {
	array_unshift( $buttons, 'fontsizeselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );

function my_mce_before_init_insert_formats( $initArray ) {
	$initArray['fontsize_formats'] = '8pt 10pt 12pt 14pt 18pt 24pt 36pt';
	return $initArray;
}
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );

// Disable XML-RPC for security
add_filter( 'xmlrpc_enabled', '__return_false' );
add_action( 'init', function () {
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		wp_die( 'XML-RPC is disabled on this site.', 'Forbidden', array( 'response' => 403 ) );
	}
} );

// Fallback blog name
add_filter( 'option_blogname', function ( $value ) {
	return $value ?: 'Jubelio';
} );

// Bing verification tag
function add_bing_verification_meta() {
	echo '<meta name="msvalidate.01" content="7C405658CB7F677DCA91C40FB791C06A" />' . "\n";
}
add_action( 'wp_head', 'add_bing_verification_meta' );
