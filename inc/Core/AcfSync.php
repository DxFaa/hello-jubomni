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
	 * Append theme's acf-json directory to ACF load paths without removing defaults
	 *
	 * @param array $paths Default load paths.
	 * @return array Updated load paths.
	 */
	public static function load_point( $paths ) {
		$theme_path = get_stylesheet_directory() . '/acf-json';
		if ( is_array( $paths ) ) {
			if ( ! in_array( $theme_path, $paths, true ) ) {
				$paths[] = $theme_path;
			}
		} else {
			$paths = array( $theme_path );
		}

		return $paths;
	}
}
