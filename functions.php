<?php
/**
 * Hello Elementor Child - Modular Monolith Kernel
 *
 * @package Jubelio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.1.22' );
}

// 1. Register PSR-4 SPL Autoloader
require_once get_stylesheet_directory() . '/inc/Core/Autoloader.php';
\Jubelio\Core\Autoloader::register();

// 2. Boot Theme Kernel & Feature Modules
\Jubelio\Core\Theme::init();
