<?php
/**
 * PSR-4 Compliant SPL Class Autoloader
 *
 * Maps namespace `Jubelio\` to the `/inc/` directory.
 *
 * @package Jubelio
 */

namespace Jubelio\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Autoloader {

	/**
	 * Namespace prefix
	 *
	 * @var string
	 */
	protected static $prefix = 'Jubelio\\';

	/**
	 * Base directory for namespace prefix
	 *
	 * @var string
	 */
	protected static $base_dir = '';

	/**
	 * Register the autoloader with SPL
	 */
	public static function register() {
		self::$base_dir = trailingslashit( dirname( __DIR__ ) );

		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Loads class file based on namespace and class name
	 *
	 * @param string $class Fully qualified class name.
	 */
	public static function autoload( $class ) {
		// Does the class use our namespace prefix?
		$len = strlen( self::$prefix );
		if ( strncmp( self::$prefix, $class, $len ) !== 0 ) {
			return;
		}

		// Get relative class name
		$relative_class = substr( $class, $len );

		// Replace namespace separators with directory separators and append with .php
		$file = self::$base_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		// If the file exists, require it
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}
