<?php
namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Enqueue {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_flush_caches_on_update' ), 20 );
		add_action( 'after_switch_theme', array( __CLASS__, 'flush_all_caches' ) );
		add_action( 'upgrader_process_complete', array( __CLASS__, 'flush_all_caches' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend' ), 20 );
		add_filter( 'script_loader_tag', array( __CLASS__, 'add_script_attributes' ), 10, 2 );
	}

	public static function get_asset_version( $relative_path ) {
		$base_ver  = defined( '_S_VERSION' ) ? _S_VERSION : wp_get_theme()->get( 'Version' );
		$full_path = get_stylesheet_directory() . $relative_path;

		if ( file_exists( $full_path ) ) {
			$mtime = @filemtime( $full_path );
			if ( $mtime ) {
				return $base_ver . '.' . $mtime;
			}
		}

		return $base_ver;
	}

	public static function maybe_flush_caches_on_update() {
		$dir = get_stylesheet_directory();

		$asset_files = array_merge(
			glob( $dir . '/assets/css/*.css' ) ?: array(),
			glob( $dir . '/assets/js/*.js' ) ?: array()
		);
		if ( file_exists( $dir . '/style.css' ) ) {
			array_unshift( $asset_files, $dir . '/style.css' );
		}

		$fingerprint_parts = array( defined( '_S_VERSION' ) ? _S_VERSION : '1.0.0' );
		foreach ( $asset_files as $path ) {
			$fingerprint_parts[] = basename( $path ) . ':' . (int) @filemtime( $path ) . ':' . (int) @filesize( $path );
		}

		$current_hash = md5( implode( '|', $fingerprint_parts ) );
		$saved_hash   = get_option( 'jubelio_assets_version_hash', '' );

		if ( $current_hash !== $saved_hash ) {
			update_option( 'jubelio_assets_version_hash', $current_hash, true );
			self::flush_all_caches();
		}
	}

	public static function flush_all_caches() {
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
		if ( function_exists( 'rocket_clean_minify' ) ) {
			rocket_clean_minify();
		}

		if ( has_action( 'litespeed_purge_all' ) ) {
			do_action( 'litespeed_purge_all' );
		}

		if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
			\autoptimizeCache::clearall();
		}

		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all();
		}

		if ( function_exists( 'wpfc_clear_all_cache' ) ) {
			wpfc_clear_all_cache( true );
		}

		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
		}

		if ( has_action( 'breeze_clear_all_cache' ) ) {
			do_action( 'breeze_clear_all_cache' );
		}
	}

	public static function enqueue_frontend() {
		$ver = defined( '_S_VERSION' ) ? _S_VERSION : '1.0.0';
		$uri = get_stylesheet_directory_uri();
		$dir = get_stylesheet_directory();

		wp_enqueue_style(
			'hello-elementor-child-style',
			$uri . '/style.css',
			array( 'hello-elementor-theme-style' ),
			self::get_asset_version( '/style.css' )
		);

		wp_enqueue_style(
			'jubelio-slider',
			'//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
			array(),
			$ver,
			'all'
		);
		wp_enqueue_script(
			'jubelio-slider',
			'//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
			array( 'jquery' ),
			$ver,
			true
		);

		wp_enqueue_style(
			'jubelio-responsive-tabs',
			'//cdnjs.cloudflare.com/ajax/libs/responsive-tabs/1.6.3/css/responsive-tabs.css',
			array(),
			$ver,
			'all'
		);
		wp_enqueue_script(
			'jubelio-responsive-tabs',
			'//cdn.jsdelivr.net/jquery.responsive-tabs/latest/jquery.responsiveTabs.min.js',
			array( 'jquery' ),
			$ver,
			true
		);

		wp_enqueue_style(
			'jubelio-accordion',
			'//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css',
			array(),
			$ver,
			'all'
		);
		wp_enqueue_script(
			'jubelio-accordion',
			'//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js',
			array( 'jquery' ),
			$ver,
			true
		);

		wp_enqueue_script(
			'jubelio-custom-script',
			$uri . '/assets/js/custom.js',
			array( 'jquery', 'jubelio-slider' ),
			self::get_asset_version( '/assets/js/custom.js' ),
			true
		);

		wp_register_style(
			'jubelio-swiper',
			'//cdn.jsdelivr.net/npm/swiper@14/swiper-bundle.min.css',
			array(),
			'14.3.0'
		);
		wp_register_script(
			'jubelio-swiper',
			'//cdn.jsdelivr.net/npm/swiper@14/swiper-bundle.min.js',
			array(),
			'14.3.0',
			true
		);

		// Registered for on-demand loading; 'elementor-frontend' ensures our CSS loads after Elementor Kit styles.
		if ( file_exists( $dir . '/assets/css/cerita-pebisnis.css' ) ) {
			wp_register_style(
				'jubelio-cerita-pebisnis',
				$uri . '/assets/css/cerita-pebisnis.css',
				array( 'elementor-frontend', 'hello-elementor-child-style', 'jubelio-swiper' ),
				self::get_asset_version( '/assets/css/cerita-pebisnis.css' )
			);
		}

		if ( file_exists( $dir . '/assets/js/cerita-pebisnis.js' ) ) {
			wp_register_script(
				'jubelio-cerita-pebisnis',
				$uri . '/assets/js/cerita-pebisnis.js',
				array( 'jquery', 'jubelio-swiper' ),
				self::get_asset_version( '/assets/js/cerita-pebisnis.js' ),
				true
			);
		}

		if ( file_exists( $dir . '/assets/js/lead-tracker.js' ) ) {
			wp_enqueue_script(
				'jubelio-lead-tracker',
				$uri . '/assets/js/lead-tracker.js',
				array(),
				self::get_asset_version( '/assets/js/lead-tracker.js' ),
				true
			);
		}
	}

	public static function add_script_attributes( $tag, $handle ) {
		if ( 'jubelio-lead-tracker' === $handle ) {
			if ( false === strpos( $tag, 'defer' ) ) {
				$tag = str_replace( ' src', ' defer="defer" src', $tag );
			}
		}
		return $tag;
	}
}
