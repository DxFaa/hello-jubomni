<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.1.22' );
}

require_once get_stylesheet_directory() . '/inc/Core/Autoloader.php';
\Jubelio\Core\Autoloader::register();

\Jubelio\Core\Theme::init();
