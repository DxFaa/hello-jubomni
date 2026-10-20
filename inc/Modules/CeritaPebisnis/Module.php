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

class Module {

	/**
	 * Boot the module
	 */
	public static function init() {
		// Initialize Post Type & Taxonomy
		PostType::init();

		// Register Elementor Widget(s) for this module
		ElementorManager::add_widget( GridWidget::class );
	}
}
