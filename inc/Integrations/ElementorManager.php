<?php
/**
 * Elementor Categories & Widgets Registry Manager
 *
 * @package Jubelio
 */

namespace Jubelio\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ElementorManager {

	/**
	 * Registered widget classes
	 *
	 * @var array
	 */
	protected static $widgets = array();

	/**
	 * Initialize Elementor hooks
	 */
	public static function init() {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_categories' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
		add_action( 'elementor/widgets/widgets_registered', array( __CLASS__, 'register_widgets_legacy' ) );
	}

	/**
	 * Register a widget class from any module
	 *
	 * @param string $class_name Fully qualified class name.
	 */
	public static function add_widget( $class_name ) {
		if ( ! in_array( $class_name, self::$widgets, true ) ) {
			self::$widgets[] = $class_name;
		}
	}

	/**
	 * Register Jubelio category in Elementor panel
	 */
	public static function register_categories( $elements_manager ) {
		$elements_manager->add_category(
			'jubelio-elements',
			array(
				'title' => esc_html__( 'Jubelio Elements', 'hello-elementor-child' ),
				'icon'  => 'fa fa-cubes',
			)
		);
	}

	/**
	 * Register widgets in Elementor 3.5+
	 */
	public static function register_widgets( $widgets_manager ) {
		static $registered = false;
		if ( $registered ) {
			return;
		}
		$registered = true;

		foreach ( self::$widgets as $class_name ) {
			if ( class_exists( $class_name ) ) {
				$widgets_manager->register( new $class_name() );
			}
		}
	}

	/**
	 * Register widgets in legacy Elementor (< 3.5)
	 */
	public static function register_widgets_legacy( $widgets_manager ) {
		static $registered = false;
		if ( $registered || did_action( 'elementor/widgets/register' ) ) {
			return;
		}
		$registered = true;

		foreach ( self::$widgets as $class_name ) {
			if ( class_exists( $class_name ) ) {
				$widgets_manager->register_widget_type( new $class_name() );
			}
		}
	}
}
