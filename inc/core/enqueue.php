<?php
/**
 * Asset Enqueues
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Enqueue child theme scripts and styles
 */
function hello_elementor_child_enqueue_scripts() {
	// Parent theme dependency style
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		_S_VERSION
	);

	// Jubelio Slick Slider
	wp_enqueue_style(
		'jubelio-slider',
		'//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
		array(),
		_S_VERSION,
		'all'
	);

	// Jubelio Responsive Tabs
	wp_enqueue_style(
		'jubelio-responsive-tabs',
		'//cdnjs.cloudflare.com/ajax/libs/responsive-tabs/1.6.3/css/responsive-tabs.css',
		array(),
		_S_VERSION,
		'all'
	);

	// Jubelio Accordion UI
	wp_enqueue_style(
		'jubelio-accordion',
		'//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css',
		array(),
		_S_VERSION,
		'all'
	);

	// Slick Carousel JS
	wp_enqueue_script(
		'jubelio-slider',
		'//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
		array( 'jquery' ),
		_S_VERSION,
		true
	);

	// Responsive Tabs JS
	wp_enqueue_script(
		'jubelio-responsive-tabs',
		'//cdn.jsdelivr.net/jquery.responsive-tabs/latest/jquery.responsiveTabs.min.js',
		array( 'jquery' ),
		_S_VERSION,
		true
	);

	// Accordion JS
	wp_enqueue_script(
		'jubelio-accordion',
		'//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js',
		array( 'jquery' ),
		_S_VERSION,
		true
	);

	// Preserved Jubelio Custom Script
	wp_enqueue_script(
		'jubelio-custom-script',
		get_stylesheet_directory_uri() . '/assets/js/custom.js',
		array( 'jquery', 'jubelio-slider' ),
		_S_VERSION,
		true
	);

	// Cerita Pebisnis Styles (Modern UI/UX with Jubelio Brand Guidelines)
	if ( file_exists( get_stylesheet_directory() . '/assets/css/cerita-pebisnis.css' ) ) {
		wp_enqueue_style(
			'jubelio-cerita-pebisnis',
			get_stylesheet_directory_uri() . '/assets/css/cerita-pebisnis.css',
			array( 'hello-elementor-child-style' ),
			_S_VERSION
		);
	}

	// Cerita Pebisnis Scripts (Interactive filter tabs, video lightbox modal, slider helpers)
	if ( file_exists( get_stylesheet_directory() . '/assets/js/cerita-pebisnis.js' ) ) {
		wp_enqueue_script(
			'jubelio-cerita-pebisnis',
			get_stylesheet_directory_uri() . '/assets/js/cerita-pebisnis.js',
			array( 'jquery', 'jubelio-slider' ),
			_S_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );
