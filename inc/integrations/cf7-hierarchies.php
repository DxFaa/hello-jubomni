<?php
/**
 * Contact Form 7 Dynamic Hierarchies & Select2
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Populate select-category with CSV hierarchy
 */
function populate_select_with_hierarchy( $tag ) {
	if ( $tag['name'] !== 'select-category' ) {
		return $tag;
	}

	$csv_path = WP_CONTENT_DIR . '/uploads/category.csv';
	if ( ! file_exists( $csv_path ) ) {
		return $tag;
	}

	$categories = array();
	if ( ( $handle = fopen( $csv_path, 'r' ) ) !== false ) {
		$row = 0;
		while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
			$row++;
			if ( $row === 1 ) {
				continue;
			}

			$parent = trim( $data[0] );
			$child  = trim( $data[1] );

			if ( $parent && $child ) {
				$categories[ $parent ][] = $child;
			}
		}
		fclose( $handle );
	}

	$options = array();
	foreach ( $categories as $parent => $children ) {
		foreach ( $children as $child ) {
			$options[] = "$parent - $child";
		}
	}

	$tag['raw_values'] = $options;
	$tag['values']     = $options;
	$tag['labels']     = $options;

	return $tag;
}
add_filter( 'wpcf7_form_tag', 'populate_select_with_hierarchy', 10, 2 );

/**
 * Populate select-location with CSV hierarchy
 */
function populate_provinceselect_with_hierarchy( $tag ) {
	if ( $tag['name'] !== 'select-location' ) {
		return $tag;
	}

	$csv_path = WP_CONTENT_DIR . '/uploads/cities_in_Indonesia.csv';
	if ( ! file_exists( $csv_path ) ) {
		return $tag;
	}

	$province = array();
	if ( ( $handle = fopen( $csv_path, 'r' ) ) !== false ) {
		$row = 0;
		while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
			$row++;
			if ( $row === 1 ) {
				continue;
			}

			$parent = trim( $data[0] );
			$child  = trim( $data[1] );

			if ( $parent && $child ) {
				$province[ $parent ][] = $child;
			}
		}
		fclose( $handle );
	}

	$options = array();
	foreach ( $province as $parent => $children ) {
		foreach ( $children as $child ) {
			$options[] = "$parent - $child";
		}
	}

	$tag['raw_values'] = $options;
	$tag['values']     = $options;
	$tag['labels']     = $options;

	return $tag;
}
add_filter( 'wpcf7_form_tag', 'populate_provinceselect_with_hierarchy', 10, 2 );

/**
 * Enqueue Select2 Assets for Contact Forms
 */
function enqueue_select2_assets() {
	wp_enqueue_style( 'select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', array(), '4.0.13' );
	wp_enqueue_script( 'select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array( 'jquery' ), '4.0.13', true );

	wp_localize_script( 'select2-js', 'select2_params', array(
		'placeholder' => __( 'Please choose an option', 'jubelio' ),
	) );

	wp_add_inline_script( 'select2-js', "
		jQuery(document).ready(function($) {
			$('.wpcf7-select').select2({
				placeholder: select2_params.placeholder,
				allowClear: true
			});
		});
	" );
}
add_action( 'wp_enqueue_scripts', 'enqueue_select2_assets' );
