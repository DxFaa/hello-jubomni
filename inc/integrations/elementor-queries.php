<?php
/**
 * Elementor Custom Query Hooks
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Combined Resource Query (Ebook, Whitepaper, Template)
 */
add_action( 'elementor/query/combined_resource_query', function( $query ) {
	$query->set( 'post_type', array( 'ebook', 'whitepaper', 'template' ) );
	$query->set( 'post_status', 'publish' );
} );

/**
 * Custom Query ID for Cerita Pebisnis in native Elementor Loop/Posts widgets
 */
add_action( 'elementor/query/cerita_pebisnis_query', function( $query ) {
	$query->set( 'post_type', 'cerita-pebisnis' );
	$query->set( 'post_status', 'publish' );
} );
