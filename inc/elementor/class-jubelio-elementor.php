<?php
/**
 * Jubelio Elementor Integration Loader
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jubelio_Elementor_Init {

	/**
	 * Instance
	 *
	 * @var Jubelio_Elementor_Init
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register Custom Elementor Category: Jubelio Elements
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'jubelio-elements',
			array(
				'title' => __( 'Jubelio Elements', 'hello-elementor-child' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Register Widgets
	 */
	public function register_widgets( $widgets_manager ) {
		require_once __DIR__ . '/widgets/class-widget-cerita-pebisnis.php';
		$widgets_manager->register( new \Jubelio_Widget_Cerita_Pebisnis() );
	}
}

// Initialize when Elementor is active
add_action( 'plugins_loaded', function() {
	if ( did_action( 'elementor/loaded' ) ) {
		Jubelio_Elementor_Init::get_instance();
	}
} );
