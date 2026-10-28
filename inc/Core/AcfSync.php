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
		add_filter( 'acf/prepare_field_group_for_export', array( __CLASS__, 'safeguard_field_group_json_export' ) );

		// 2. Fallback to ACF field default_value when empty on frontend & Elementor dynamic tags
		add_filter( 'acf/format_value', array( __CLASS__, 'fallback_to_default_value' ), 10, 3 );

		// 3. Local Fallback Registration on acf/init (frontend only, so WP Admin editor is never conflicted)
		add_action( 'acf/init', array( __CLASS__, 'register_local_fallback' ), 20 );

		// 4. Admin manual force-sync handler (only on explicit user request)
		if ( is_admin() ) {
			add_action( 'admin_action_jubelio_force_acf_sync', array( __CLASS__, 'force_sync_handler' ) );
			add_action( 'admin_notices', array( __CLASS__, 'admin_sync_notice' ) );
		}
	}

	/**
	 * Automatically fallback to the field's configured default_value when empty.
	 * Solves the issue where an emptied post field returns "" instead of its default in Elementor dynamic tags.
	 *
	 * @param mixed      $value Formatted value.
	 * @param int|string $post_id Post ID.
	 * @param array      $field Field definition array.
	 * @return mixed
	 */
	public static function fallback_to_default_value( $value, $post_id, $field ) {
		if ( ( null === $value || '' === $value ) && isset( $field['default_value'] ) && '' !== $field['default_value'] ) {
			return $field['default_value'];
		}

		return $value;
	}

	/**
	 * Guard: Prevent saving group_cerita_pebisnis to JSON with an empty fields array.
	 * If fields are empty for any reason, restores existing fields from disk.
	 *
	 * @param array $field_group Field group definition.
	 * @return array Protected field group.
	 */
	public static function safeguard_field_group_json_export( $field_group ) {
		if ( ! is_array( $field_group ) || empty( $field_group['key'] ) || 'group_cerita_pebisnis' !== $field_group['key'] ) {
			return $field_group;
		}

		if ( empty( $field_group['fields'] ) || ! is_array( $field_group['fields'] ) ) {
			$file = get_stylesheet_directory() . '/acf-json/group_cerita_pebisnis.json';
			if ( file_exists( $file ) ) {
				$existing = json_decode( file_get_contents( $file ), true );
				if ( ! empty( $existing['fields'] ) && is_array( $existing['fields'] ) ) {
					$field_group['fields'] = $existing['fields'];
				}
			}
		}

		return $field_group;
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
	 * Register local field group as fallback on frontend so fields are guaranteed to be active
	 */
	public static function register_local_fallback() {
		if ( is_admin() || ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		if ( function_exists( 'acf_get_field_group' ) && acf_get_field_group( 'group_cerita_pebisnis' ) ) {
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
