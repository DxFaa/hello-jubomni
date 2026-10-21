<?php
/**
 * Contact Form 7 Dynamic Hierarchies & Select2
 *
 * @package Jubelio
 */

namespace Jubelio\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContactForm7 {

	public static function init() {
		add_filter( 'wpcf7_form_tag', array( __CLASS__, 'populate_category_hierarchy' ), 10, 2 );
		add_filter( 'wpcf7_form_tag', array( __CLASS__, 'populate_location_hierarchy' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_select2_assets' ) );
	}

	public static function populate_category_hierarchy( $tag ) {
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

	public static function populate_location_hierarchy( $tag ) {
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

	public static function enqueue_select2_assets() {
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
}
