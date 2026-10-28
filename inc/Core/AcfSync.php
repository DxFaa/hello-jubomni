<?php
/**
 * ACF Pro Local JSON Synchronization & Auto-Import
 *
 * Saves and loads ACF field groups, post types, and taxonomies to/from `acf-json/`.
 * Automatically synchronizes JSON definitions into the WordPress database so they
 * appear immediately in the ACF Pro GUI under Field Groups, Post Types, and Taxonomies.
 *
 * @package Jubelio
 */

namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AcfSync {

	/**
	 * Register ACF JSON hooks and database sync
	 */
	public static function init() {
		// 1. JSON Save & Load filters
		add_filter( 'acf/settings/save_json', array( __CLASS__, 'save_point' ) );
		add_filter( 'acf/settings/load_json', array( __CLASS__, 'load_point' ) );
		add_filter( 'acf/settings/save_json/key=group_cerita_pebisnis', array( __CLASS__, 'prevent_empty_group_overwrite' ), 10, 2 );

		// 2. Local Fallback Registration on acf/init (guarantees fields are active on edit screens)
		add_action( 'acf/init', array( __CLASS__, 'register_local_fallback' ), 20 );

		// 3. Admin manual force-sync handler (only on explicit user request)
		if ( is_admin() ) {
			add_action( 'admin_action_jubelio_force_acf_sync', array( __CLASS__, 'force_sync_handler' ) );
			add_action( 'admin_notices', array( __CLASS__, 'admin_sync_notice' ) );
		}
	}

	/**
	 * Guard: Prevent saving/overwriting group_cerita_pebisnis.json if fields array is empty
	 *
	 * @param string $path Default save path.
	 * @param array  $field_group Field group definition.
	 * @return string|false Path or false to abort save.
	 */
	public static function prevent_empty_group_overwrite( $path, $field_group ) {
		if ( empty( $field_group['fields'] ) ) {
			return false;
		}
		return $path;
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

	/**
	 * Register local field group as fallback so fields are guaranteed to be active
	 */
	public static function register_local_fallback() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$file = get_stylesheet_directory() . '/acf-json/group_cerita_pebisnis.json';
		if ( file_exists( $file ) ) {
			$json_str = file_get_contents( $file );
			$group    = json_decode( $json_str, true );
			if ( ! empty( $group ) && is_array( $group ) ) {
				acf_add_local_field_group( $group );
			}
		}
	}

	/**
	 * Automatically import JSON files into WordPress database if not yet stored in DB.
	 * This makes them immediately visible and editable in ACF Pro under:
	 * - ACF > Field Groups
	 * - ACF > Post Types
	 * - ACF > Taxonomies
	 */
	public static function auto_sync_to_db() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$dir = get_stylesheet_directory() . '/acf-json';
		if ( ! is_dir( $dir ) ) {
			return;
		}

		// 1. Sync Field Group: group_cerita_pebisnis
		if ( function_exists( 'acf_import_field_group' ) ) {
			$existing_group = get_posts( array(
				'post_type'      => 'acf-field-group',
				'name'           => 'group_cerita_pebisnis',
				'post_status'    => 'any',
				'posts_per_page' => 1,
			) );

			$group_file = $dir . '/group_cerita_pebisnis.json';
			if ( file_exists( $group_file ) ) {
				$data = json_decode( file_get_contents( $group_file ), true );
				if ( ! empty( $data ) && is_array( $data ) ) {
					$should_import = false;
					if ( empty( $existing_group ) ) {
						$should_import = true;
					} else {
						$post_modified = strtotime( $existing_group[0]->post_modified );
						$json_modified = isset( $data['modified'] ) ? intval( $data['modified'] ) : filemtime( $group_file );
						if ( $json_modified > $post_modified ) {
							$should_import = true;
							$data['ID']    = $existing_group[0]->ID;
						}
					}
					if ( $should_import ) {
						acf_import_field_group( $data );
					}
				}
			}
		}

		// 2. Sync Post Type: post_type_cerita_pebisnis (ACF Pro 6.1+)
		if ( function_exists( 'acf_import_post_type' ) ) {
			$existing_cpt = get_posts( array(
				'post_type'      => 'acf-post-type',
				'name'           => 'post_type_cerita_pebisnis',
				'post_status'    => 'any',
				'posts_per_page' => 1,
			) );

			if ( empty( $existing_cpt ) ) {
				$cpt_file = $dir . '/post_type_cerita_pebisnis.json';
				if ( file_exists( $cpt_file ) ) {
					$data = json_decode( file_get_contents( $cpt_file ), true );
					if ( ! empty( $data ) && is_array( $data ) ) {
						acf_import_post_type( $data );
					}
				}
			}
		}

		// 3. Sync Taxonomy: taxonomy_industri (ACF Pro 6.1+)
		if ( function_exists( 'acf_import_taxonomy' ) ) {
			$existing_tax = get_posts( array(
				'post_type'      => 'acf-taxonomy',
				'name'           => 'taxonomy_industri',
				'post_status'    => 'any',
				'posts_per_page' => 1,
			) );

			if ( empty( $existing_tax ) ) {
				$tax_file = $dir . '/taxonomy_industri.json';
				if ( file_exists( $tax_file ) ) {
					$data = json_decode( file_get_contents( $tax_file ), true );
					if ( ! empty( $data ) && is_array( $data ) ) {
						acf_import_taxonomy( $data );
					}
				}
			}
		}
	}

	/**
	 * Force re-sync handler for manual admin trigger
	 */
	public static function force_sync_handler() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized user.', 'hello-elementor-child' ) );
		}

		check_admin_referer( 'jubelio_acf_force_sync' );

		$dir = get_stylesheet_directory() . '/acf-json';

		// Re-import Field Group
		if ( function_exists( 'acf_import_field_group' ) && file_exists( $dir . '/group_cerita_pebisnis.json' ) ) {
			$data = json_decode( file_get_contents( $dir . '/group_cerita_pebisnis.json' ), true );
			if ( ! empty( $data ) ) {
				acf_import_field_group( $data );
			}
		}

		// Re-import Post Type
		if ( function_exists( 'acf_import_post_type' ) && file_exists( $dir . '/post_type_cerita_pebisnis.json' ) ) {
			$data = json_decode( file_get_contents( $dir . '/post_type_cerita_pebisnis.json' ), true );
			if ( ! empty( $data ) ) {
				acf_import_post_type( $data );
			}
		}

		// Re-import Taxonomy
		if ( function_exists( 'acf_import_taxonomy' ) && file_exists( $dir . '/taxonomy_industri.json' ) ) {
			$data = json_decode( file_get_contents( $dir . '/taxonomy_industri.json' ), true );
			if ( ! empty( $data ) ) {
				acf_import_taxonomy( $data );
			}
		}

		wp_safe_redirect( add_query_arg( array(
			'post_type'   => 'acf-field-group',
			'acf_synced'  => '1',
		), admin_url( 'edit.php' ) ) );
		exit;
	}

	/**
	 * Admin notice when manual sync completes
	 */
	public static function admin_sync_notice() {
		if ( isset( $_GET['acf_synced'] ) && '1' === $_GET['acf_synced'] ) {
			echo '<div class="notice notice-success is-dismissible"><p>' .
				esc_html__( 'Jubelio ACF Pro: Cerita Pebisnis field group, post type, and taxonomy synced successfully into the database!', 'hello-elementor-child' ) .
				'</p></div>';
		}
	}
}
