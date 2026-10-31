<?php
namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AcfSync {

	public static function init() {
		add_filter( 'acf/settings/save_json', array( __CLASS__, 'save_point' ) );
		add_filter( 'acf/settings/load_json', array( __CLASS__, 'load_point' ) );
		add_filter( 'acf/prepare_field_group_for_export', array( __CLASS__, 'safeguard_field_group_json_export' ) );

		add_filter( 'acf/format_value', array( __CLASS__, 'fallback_to_default_value' ), 10, 3 );

		// Frontend-only fallback so WP Admin field group editor is never conflicted.
		add_action( 'acf/init', array( __CLASS__, 'register_local_fallback' ), 20 );

		if ( is_admin() ) {
			add_action( 'admin_action_jubelio_force_acf_sync', array( __CLASS__, 'force_sync_handler' ) );
			add_action( 'admin_notices', array( __CLASS__, 'admin_sync_notice' ) );
		}
	}

	// Emptied post fields return "" instead of their default in Elementor dynamic tags.
	public static function fallback_to_default_value( $value, $post_id, $field ) {
		if ( ( null === $value || '' === $value ) && isset( $field['default_value'] ) && '' !== $field['default_value'] ) {
			return $field['default_value'];
		}

		return $value;
	}

	// Prevent saving group_cerita_pebisnis to JSON with an empty fields array by restoring from disk.
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

	public static function save_point( $path ) {
		return get_stylesheet_directory() . '/acf-json';
	}

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

	public static function auto_sync_to_db() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$dir = get_stylesheet_directory() . '/acf-json';
		if ( ! is_dir( $dir ) ) {
			return;
		}

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

	public static function force_sync_handler() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized user.', 'hello-elementor-child' ) );
		}

		check_admin_referer( 'jubelio_acf_force_sync' );

		$dir = get_stylesheet_directory() . '/acf-json';

		if ( function_exists( 'acf_import_field_group' ) && file_exists( $dir . '/group_cerita_pebisnis.json' ) ) {
			$data = json_decode( file_get_contents( $dir . '/group_cerita_pebisnis.json' ), true );
			if ( ! empty( $data ) ) {
				acf_import_field_group( $data );
			}
		}

		if ( function_exists( 'acf_import_post_type' ) && file_exists( $dir . '/post_type_cerita_pebisnis.json' ) ) {
			$data = json_decode( file_get_contents( $dir . '/post_type_cerita_pebisnis.json' ), true );
			if ( ! empty( $data ) ) {
				acf_import_post_type( $data );
			}
		}

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

	public static function admin_sync_notice() {
		if ( isset( $_GET['acf_synced'] ) && '1' === $_GET['acf_synced'] ) {
			echo '<div class="notice notice-success is-dismissible"><p>' .
				esc_html__( 'Jubelio ACF Pro: Cerita Pebisnis field group, post type, and taxonomy synced successfully into the database!', 'hello-elementor-child' ) .
				'</p></div>';
		}
	}
}
