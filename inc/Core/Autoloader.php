<?php
namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Autoloader {

	protected static $prefix = 'Jubelio\\';

	protected static $base_dir = '';

	public static function register() {
		self::$base_dir = rtrim( dirname( __DIR__ ), '/\\' ) . DIRECTORY_SEPARATOR;

		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	public static function autoload( $class ) {
		$len = strlen( self::$prefix );
		if ( strncmp( self::$prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $len );

		$file = self::$base_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}
