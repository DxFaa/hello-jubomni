<?php
/**
 * ACF Pro Local JSON Synchronization
 *
 * Saves and loads ACF field groups to/from `acf-json/` folder in theme.
 *
 * @package Jubelio
 */

namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AcfSync {

	/**
	 * Register ACF JSON save and load filters
	 */
	public static function init() {
		add_filter( 'acf/settings/save_json', array( __CLASS__, 'save_point' ) );
		add_filter( 'acf/settings/load_json', array( __CLASS__, 'load_point' ) );
	}

	/**
	 * Point ACF Save to theme's acf-json directory
	 *
	 * @param string $path Default save path.
	 * @return string Custom save path.
	 */
	public static function save_point( $path ) {
		return get_stylesheet_directory() . '/acf-json';
	}

	/**
	 * Add theme's acf-json directory to ACF load paths
	 *
	 * @param array $paths Default load paths.
	 * @return array Updated load paths.
	 */
	public static function load_point( $paths ) {
		// Remove original path (optional, but clean)
		unset( $paths[0] );

		// Append our theme path
		$paths[] = get_stylesheet_directory() . '/acf-json';

		return $paths;
	}
}
