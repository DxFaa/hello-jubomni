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
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend' ), 20 );
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
			$ver
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
			$ver,
			true
		);

		// Vendor: Swiper v11 for modern responsive touch slider & carousel
		wp_register_style(
			'jubelio-swiper',
			'//cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
			array(),
			'11.1.4'
		);
		wp_register_script(
			'jubelio-swiper',
			'//cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
			array(),
			'11.1.4',
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
				$ver
			);
		}

		if ( file_exists( $dir . '/assets/js/cerita-pebisnis.js' ) ) {
			wp_register_script(
				'jubelio-cerita-pebisnis',
				$uri . '/assets/js/cerita-pebisnis.js',
				array( 'jquery', 'jubelio-swiper' ),
				$ver,
				true
			);
		}
	}
}
