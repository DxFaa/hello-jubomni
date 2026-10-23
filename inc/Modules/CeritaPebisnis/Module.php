<?php
/**
 * Cerita Pebisnis Feature Module Bootstrapper
 *
 * @package Jubelio
 */

namespace Jubelio\Modules\CeritaPebisnis;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Jubelio\Integrations\ElementorManager;
use Jubelio\Modules\CeritaPebisnis\Widgets\GridWidget;
use Jubelio\Modules\CeritaPebisnis\Widgets\MetricsWidget;
use Jubelio\Modules\CeritaPebisnis\Widgets\TestimonialsWidget;

class Module {

	/**
	 * Boot the module
	 */
	public static function init() {
		// Initialize Post Type & Taxonomy
		PostType::init();

		// Initialize Demo Data Seeder for WP Admin
		Seeder::init();

		// Register Elementor Widget(s) for this module
		ElementorManager::add_widget( GridWidget::class );
		ElementorManager::add_widget( MetricsWidget::class );
		ElementorManager::add_widget( TestimonialsWidget::class );
	}
}
