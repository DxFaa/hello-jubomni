<?php
/**
 * Asset Enqueues Manager
 *
 * @package Jubelio
 */

namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Enqueue {

	/**
	 * Register hooks
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_flush_caches_on_update' ), 20 );
		add_action( 'after_switch_theme', array( __CLASS__, 'flush_all_caches' ) );
		add_action( 'upgrader_process_complete', array( __CLASS__, 'flush_all_caches' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend' ), 20 );
		add_filter( 'script_loader_tag', array( __CLASS__, 'add_script_attributes' ), 10, 2 );
	}

	/**
	 * Resolve dynamic cache-busting version for a theme asset using filemtime()
	 * so any CSS/JS update automatically invalidates browser & CDN caches.
	 *
	 * @param string $relative_path Path relative to child theme directory (e.g. '/assets/css/cerita-pebisnis.css').
	 * @return string Version string.
	 */
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

	/**
	 * Detect when theme CSS/JS files have changed (e.g. after theme ZIP upload/import)
	 * and automatically purge Elementor & page/minification caches once per update.
	 * Automatically scans style.css and all CSS/JS files inside /assets/.
	 */
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

	/**
	 * Flush Elementor generated CSS and common WordPress page/minification caches.
	 */
	public static function flush_all_caches() {
		// 1. Elementor CSS & files cache
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		// 2. WordPress Object Cache
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		// 3. WP Rocket (Page cache & Minified CSS/JS)
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
		if ( function_exists( 'rocket_clean_minify' ) ) {
			rocket_clean_minify();
		}

		// 4. LiteSpeed Cache
		if ( has_action( 'litespeed_purge_all' ) ) {
			do_action( 'litespeed_purge_all' );
		}

		// 5. Autoptimize
		if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
			\autoptimizeCache::clearall();
		}

		// 6. W3 Total Cache
		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all();
		}

		// 7. WP Fastest Cache
		if ( function_exists( 'wpfc_clear_all_cache' ) ) {
			wpfc_clear_all_cache( true );
		}

		// 8. SiteGround Optimizer
		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
		}

		// 9. Breeze (Cloudways)
		if ( has_action( 'breeze_clear_all_cache' ) ) {
			do_action( 'breeze_clear_all_cache' );
		}
	}

	/**
	 * Enqueue frontend scripts and styles
	 */
	public static function enqueue_frontend() {
		$ver = defined( '_S_VERSION' ) ? _S_VERSION : '1.0.0';
		$uri = get_stylesheet_directory_uri();
		$dir = get_stylesheet_directory();

		// Parent & Child Theme Styles
		wp_enqueue_style(
			'hello-elementor-child-style',
			$uri . '/style.css',
			array( 'hello-elementor-theme-style' ),
			self::get_asset_version( '/style.css' )
		);

		// Vendor: Slick Carousel
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

		// Vendor: Responsive Tabs
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

		// Vendor: Accordion UI
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

		// Preserved Jubelio Custom Script
		wp_enqueue_script(
			'jubelio-custom-script',
			$uri . '/assets/js/custom.js',
			array( 'jquery', 'jubelio-slider' ),
			self::get_asset_version( '/assets/js/custom.js' ),
			true
		);

		// Vendor: Swiper v14 for modern responsive touch slider & carousel
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

		// Cerita Pebisnis Section Styles & Scripts
		// Registered for on-demand loading: Elementor only loads them on pages where the widget is present!
		// 'elementor-frontend' dependency ensures our CSS loads AFTER Elementor Kit styles in the DOM <head>
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

		// Client-side Lead & Traffic Attribution Tracker
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

	/**
	 * Add defer attribute to non-blocking tracker script
	 *
	 * @param string $tag Script HTML tag.
	 * @param string $handle Script handle.
	 * @return string Modified script tag.
	 */
	public static function add_script_attributes( $tag, $handle ) {
		if ( 'jubelio-lead-tracker' === $handle ) {
			if ( false === strpos( $tag, 'defer' ) ) {
				$tag = str_replace( ' src', ' defer="defer" src', $tag );
			}
		}
		return $tag;
	}
}
