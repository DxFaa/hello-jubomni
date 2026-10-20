<?php
/**
 * Hello Elementor Child - Theme Functions & Modular Bootstrap
 *
 * Reorganized and modularized for Jubelio Omnichannel.
 * All modules are cleanly structured in the /inc/ directory.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Theme version constant
if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.0.0' );
}

/**
 * 1. Core: Enqueues & Admin/Security Tweaks
 */
require_once get_stylesheet_directory() . '/inc/core/enqueue.php';
require_once get_stylesheet_directory() . '/inc/core/admin-cleanup.php';

/**
 * 2. Custom Post Types & Data Helpers
 */
require_once get_stylesheet_directory() . '/inc/post-types/post-type-ebook.php';
require_once get_stylesheet_directory() . '/inc/post-types/post-type-cerita-pebisnis.php';

/**
 * 3. Integrations (Contact Form 7, Select2, Analytics/GTM overrides, Elementor Queries)
 */
require_once get_stylesheet_directory() . '/inc/integrations/cf7-hierarchies.php';
require_once get_stylesheet_directory() . '/inc/integrations/tracking-cleanup.php';
require_once get_stylesheet_directory() . '/inc/integrations/elementor-queries.php';

/**
 * 4. Shortcodes (Clean Legacy Buffering & Cerita Pebisnis Shortcode)
 */
require_once get_stylesheet_directory() . '/inc/shortcodes/legacy-shortcodes.php';
require_once get_stylesheet_directory() . '/inc/shortcodes/cerita-pebisnis-shortcode.php';

/**
 * 5. Elementor Pro Integration (Custom Widgets & Categories)
 */
require_once get_stylesheet_directory() . '/inc/elementor/class-jubelio-elementor.php';
